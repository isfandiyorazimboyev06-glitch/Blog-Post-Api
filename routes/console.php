<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ExportPostsToTelegramJob;
use App\Http\Controllers\PostExportController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// Dispatching a Queue Job periodically (Every hour)
Schedule::job(new ExportPostsToTelegramJob('2022202461'))
    ->everyMinute()
    ->withoutOverlapping(); // Prevents multiple instances if a run takes longer than expected




