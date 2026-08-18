<?php

namespace App\Jobs;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Http;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;






class ExportPostsToTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries =3;
    public $timeout =300;


    /**
     * Create a new job instance.
     */
    public function __construct(public string $chatId)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Fetch Blog posts data
        $blogposts = BlogPost::with(['category','user'])
            ->select('id', 'author', 'post', 'category_blog_post_id', 'user_id', 'created_at', 'updated_at')
            ->get();

        // 2. Prepare storage file path safely
        $fileName = 'posts_export_' . time() . '.csv';
        $filePath = storage_path('app/' . $fileName);

        // Ensure storage directory exists
        if (!file_exists(storage_path('app'))) {
            mkdir(storage_path('app'), 0755, true);
        }

        // 3. Write CSV content
        $file = fopen($filePath, 'w');

        fputcsv($file, ['ID', 'Author', 'Post', 'Category Blog Posts ID','Category Name', 'User ID','User Name', 'Created at', 'Updated at']);

        foreach ($blogposts as $blogpost) {
            fputcsv($file, [
                $blogpost->id,
                $blogpost->author,
                $blogpost->post,
                $blogpost->category_blog_post_id,
                $blogpost->category?->category_name ?? 'N/A',
                $blogpost->user_id,
                $blogpost->user?->name ?? 'N/A',
                $blogpost->created_at?->toDateTimeString(),
                $blogpost->updated_at?->toDateTimeString(),
            ]);
        }

        fclose($file);

        // 4. Send document to Telegram
        $botToken = config('services.telegram.bot_token');


        // FIXED: Passed $fileStream into Http::attach
        $fileStream = fopen($filePath, 'r');

        $response = Http::withoutVerifying()->attach(
            'document',
            $fileStream,
            $fileName
        )->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
            'chat_id' => $this->chatId,
            'caption' => 'Here is your requested posts export file!',
        ]);

        if (is_resource($fileStream)) {
            fclose($fileStream);
        }

        // 5. Clean up local temp file
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
