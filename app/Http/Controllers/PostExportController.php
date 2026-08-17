<?php

namespace App\Http\Controllers;

use App\Jobs\ExportPostsToTelegramJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostExportController extends Controller
{
    public function export(Request $request): JsonResponse
    {
        // Retrieve chat ID from request or fallback to default
        $chatId = $request->input('chat_id',config('services.telegram.chat_id'));

        // Dispatch job to the queue
        ExportPostsToTelegramJob::dispatch($chatId);


        // Return immediate response to Postman
        return response()->json([
            'status' => 'success',
            'message' => 'Export job has been queued successfully. Check your Telegram.'
        ],200);
    }
}
