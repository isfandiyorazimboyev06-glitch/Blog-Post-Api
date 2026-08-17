<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\BlogPostRepositoryInterface;
use App\Repositories\BlogPostRepository;

use App\Events\BlogPostCreated;
use App\Listeners\SendBlogPostToTelegram;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(
            BlogPostRepositoryInterface::class,
            BlogPostRepository::class
        );

        // Event::listen(
        //     BlogPostCreated::class,
        //     SendBlogPostToTelegram::class
        // );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

    }
}
