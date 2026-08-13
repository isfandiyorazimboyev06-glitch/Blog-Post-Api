<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// --- PUBLIC ROUTES ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);



// Protected auth routes (requires Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiresource('/blogpost',BlogPostController::class)
        ->missing(function () {
            return response()->json([
                'status' =>false,
                'message' => 'The requested blog was not found in our db.'
            ],404);
});






