<?php

use App\Http\Controllers\Api\ValidationController;
use Illuminate\Support\Facades\Route;

// ─── Protected API Routes (Sanctum) ─────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/validate', [ValidationController::class, 'validate'])->name('api.validate');
    Route::get('/tickets/confirmed', [ValidationController::class, 'confirmedList'])->name('api.tickets.confirmed');
});
