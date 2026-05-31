<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ValidationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicTicketController;
use App\Http\Middleware\CheckRole;
use App\Livewire\AdminDashboard;
use App\Livewire\ManualTicketForm;
use App\Livewire\TicketList;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────
Route::get('/', [PublicTicketController::class, 'index'])->name('home');
Route::get('/bilhetes', [PublicTicketController::class, 'index'])->name('bilhetes');
Route::get('/consultar', [PublicTicketController::class, 'lookupPage'])->name('tickets.lookup.form');
Route::post('/consultar', [PublicTicketController::class, 'lookup'])->name('tickets.lookup');
Route::get('/bilhetes/{ticket}/download', [PublicTicketController::class, 'download'])
    ->middleware('signed')
    ->name('tickets.download');
Route::get('/bilhetes/{ticket}/download/png', [PublicTicketController::class, 'downloadPng'])
    ->middleware('signed')
    ->name('tickets.download.png');

// ─── Admin Routes (auth required) ───────────────────────────
Route::middleware(['auth', CheckRole::class . ':admin,organizer'])->prefix('admin')->group(function () {
    Route::get('/', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/tickets', TicketList::class)->name('admin.tickets');
    Route::get('/tickets/{ticket}/download', [AdminController::class, 'downloadTicket'])->name('admin.tickets.download');
    Route::get('/tickets/{ticket}/download/png', [AdminController::class, 'downloadTicketPng'])->name('admin.tickets.download.png');
    Route::get('/manual', ManualTicketForm::class)->name('admin.manual');
    Route::get('/site', [AdminController::class, 'siteContent'])->name('admin.site');
    Route::post('/site', [AdminController::class, 'updateSiteContent'])->name('admin.site.update');
    Route::get('/tickets/export', [AdminController::class, 'exportCsv'])->name('admin.export');
});

// ─── Validator Route ─────────────────────────────────────────
Route::get('/validar', [ValidationController::class, 'index'])->name('validator');
Route::middleware('auth')->group(function () {
    Route::post('/validar/bilhete', [ValidationController::class, 'validate'])->name('validator.validate');
    Route::get('/validar/confirmados', [ValidationController::class, 'confirmedList'])->name('validator.confirmed');
});

// ─── Auth Dashboard Redirect ─────────────────────────────────
Route::get('/dashboard', function () {
    return redirect('/admin');
})->middleware(['auth'])->name('dashboard');

// ─── Profile Routes (from Breeze) ────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
