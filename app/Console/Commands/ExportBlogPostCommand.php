<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Http;
use Exception;

class ExportBlogPostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:export-telegram
                            {--count=50 : The number of fresh blog posts to seed and export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a temporary CSV spreadsheet of blog posts and sends Telegram bot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $this->info("Creating {$count} blog posts records in the local database...");

        $newPostIds = [];
        $fakeAuthors = ['Muzaffar Jalolov','Zokirov Rustam','Darkhonbek Azimov','James Clear'];

        // seed step: generate fake records directly into the database
        for ($i=1; $i<=$count; $i++) {
           // $randomTitle = "Qiziqarli blog post mavzusi #" . rand(100,999);

            $createdPost = BlogPost::create([
                'author' => $fakeAuthors[array_rand($fakeAuthors)],
                'post' => "Yangi postni raqami #" . $i,
            ]);

            $newPostIds[] = $createdPost->id;
        }

        $this->info("Successfully seeded {$count} new records into the database. ");
        // fetch step: Retrieve only the fresh items we just created
        $posts = BlogPost::whereIn('id',$newPostIds)->latest()->get();

        if($posts->isEmpty()) {
            $this->error("Aborting process: No blog posts discovered inside the db.");
            return Command::FAILURE;
        }

        // Define standard system write paths for the generated file
        $fileName = "blog_posts_export_" . now()->format('Y_m_d_His') . ".csv";
        $filePath = storage_path("app/private/". $fileName);

        // Ensure storage subdirectory directory structure is secure
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Write data to a standard comma-separated spreadsheet file stream
        $fileStream = fopen($filePath, 'w');

        // Add Byte Order Mark (BOM) to force Excel to renter UTF-8 Cyrillic/Uzbek data correctly
        fprintf($fileStream, chr(0xEF).chr(0xBB).chr(0xBF));

        // Inject standardized database table headers
        fputcsv($fileStream, ['ID','Author','Post','Created At','Updated At']);

        foreach($posts as $post) {
            fputcsv($fileStream, [
                $post->id,
                $post->author,
                $post->post,
                $post->created_at ? $post->created_at->toDateTimeString() : '',
                $post->updated_at ? $post->updated_at->toDateTimeString() : '',
            ]);
        }
        fclose($fileStream);
        $this->info("Local file successfully built at: {$filePath}");

        // Transport data structure to Telegram using the native HTTP Multi-part client
        $this->info("Transporting document artifact over secure networl payload layers...");

        try {
            // Pull configuration environments set inside your application configs or .env settings
            $botToken = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
            $charId = config('services.telegram.chat_id') ?? env('TELEGRAM_CHAT_ID');

            if (!$botToken || !$charId) {
                throw new Exception("Missing secure Telegram environmental configuration within my project configuration layers.");
            }

            // FIX: Added api. and /bot to the URL string
            $response = Http::attach(
                'document',
                file_get_contents($filePath),
                $fileName
            )->withoutVerifying()
            ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                'chat_id' => $charId,
                'caption' => "Data Synchronized Successfully",
                'parse_mode' => 'Markdown',
            ]);

            if ($response->failed()) {
                throw new Exception("Telegram system feedback transmission drop: " . $response->body());
            }

            $this->info("Transmission status: Absolute Success! Excel/Csv formatted file");
            unlink($filePath);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error("Transmission Interruption Error: " . $e->getMessage());

            // Safe cleanup fallback loop execution
            if(file_exists($filePath)) {
                unlink($filePath);
            }
            return Command::FAILURE;
        }
    }
}
