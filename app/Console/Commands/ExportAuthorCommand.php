<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Author;

use Exception;

use Illuminate\Support\Facades\Http;


class ExportAuthorCommand extends Command
{
    protected $signature = "author:export-info-telegram-bot
                             {--count=50 : Created authors information newly}";

    protected $description = "Generate and Seeds author information and sent to Telegram bot";

    public function handle()
    {
        // explicitly get count
        $count = $this->option('count');

        // Message about starting first step
        $this->info("1) Creating {$count} authors data.");

        // after saving data in db, getting authorID
        $newAuthorID = [];

        // loop for seeding author data
        for ($i=1;$i<=$count;$i++)
        {
            $createAuthorsData = Author::create([
                'name' => fake()->name(),
                'age' => rand(1,100),
                'address' => fake('uz_UZ')->address(),
            ]);

            $newAuthorID[] = $createAuthorsData->id;
        }

        // Successfully message for saving db
        $this->info("2)Successfully created authors data and insert into db.");

        // getting authors data only i created by loop
        $authors = Author::whereIn('id',$newAuthorID)->latest()->get();

        // checking authors var
        if ($authors->isEmpty()) {
            $this->error("Author is empty, there is not file found in db");
            return Command::FAILURE;
        }

        // start making file and its path
        $fileName ='author_export_' . now()->format('Y.m.d.His') . '.csv';
        $filePath = storage_path('app/private/author/' . $fileName);

        // checking whether folder exists if not then make new one
        if(!file_exists(dirname($filePath)))
        {
            mkdir(dirname($filePath), 0755,true);
        }

        // Message about successfully generated file
        $this->info('3) File Successfully generated in app/private/author/ location');

        // opening the stream to write in file.
        $fileStream = fopen($filePath,'w');

        // BOM (Byte order markup) making utf-8 modern version to write
        fprintf($fileStream, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write to file only headers(columns)
        fputcsv($fileStream, ['ID','Name','Age','Address','Created At','Updated At']);

        // write loop in order to fill out rows for headers(columns)
        foreach($authors as $author)
        {
            fputcsv($fileStream,[
                $author->id,
                $author->name ?? '',
                $author->age ?? 18,
                $author->address ?? 'Kokand',
                $author->created_at ? $author->created_at->format('Y_m_d_His') : '',
                $author->updated_at ? $author->updated_at->format('Y_m_d_His') : '',

            ]);
        }

        // close the filestrema
        fclose($fileStream);

        // message about successfylly written data
        $this->info("4)Successfully written data inside file and it is ready to send.");

        $this->info("5)Next step to send document to Telegram bot");

        try
        {
            // get credentials from servies or env
            $botToken = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
            $chatID = config('services.telegram.chat_id') ?? env('TELEGRAM_CHAT_ID');

            // check whether they do exists
            if (!$botToken || !$chatID) {
                throw new Exception('Error in credentials');
            }

            // send file(csv) to telegram bot
            $response = Http::attach(
                'document',
                file_get_contents($filePath),
                $fileName
            )->withoutVerifying()
            ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                'chat_id' => $chatID,
                'caption' => '*Generated Authors Data Successfully* The file is in CSV formated.',
                'parse_mode' =>'Markdown'
            ]);

            if ($response->failed()) {
                throw new Exception("Failed to send file {$response->body()}");
            }

            $this->info("6)File is sent correctly to telegram bot");

            unlink($filePath);
            return Command::SUCCESS;


        }catch (Exception $e){
            $this->error("Received error {$e->getMessage()}");

            if(file_exists($filePath))
            {
                    unlink($filePath);
            }
                return Command::FAILURE;
        }

    }
}

?>
