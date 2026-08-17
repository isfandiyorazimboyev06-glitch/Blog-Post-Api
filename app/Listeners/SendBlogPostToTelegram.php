<?php

namespace App\Listeners;

use App\Events\BlogPostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Jobs\ExportPostsToTelegramJob;

class SendBlogPostToTelegram 
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BlogPostCreated $event): void
    {
        // $event->post orqali yaratilagan blogpost ma'lumotlarni olamiz
        $blogpost = $event->blogpost;

        ExportPostsToTelegramJob::dispatch('2022202461');
    }
}
