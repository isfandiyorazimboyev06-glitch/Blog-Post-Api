<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BlogPostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiresource('/blogpost',BlogPostController::class)
        ->missing(function () {
            return response()->json([
                'status' =>false,
                'message' => 'The requested blog was not found in our db.'
            ],404);
});
