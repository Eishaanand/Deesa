<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AttemptAnswerController;
use App\Http\Controllers\Api\GeminiQuestionGenerationController;
use App\Http\Controllers\Api\LiveMonitoringController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/attempts/{attempt}/answers', AttemptAnswerController::class)->name('api.attempts.answers.store');
    Route::post('/activity-logs', ActivityLogController::class)->name('api.activity.store');
    Route::get('/admin/live-monitoring', LiveMonitoringController::class)->middleware('role:admin')->name('api.admin.live-monitoring');
    Route::post('/admin/gemini/questions', GeminiQuestionGenerationController::class)->middleware('role:admin')->name('api.admin.gemini.questions');
});
