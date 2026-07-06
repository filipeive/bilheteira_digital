<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\SaleConfirmController;
use App\Http\Controllers\Api\ValidationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicTicketController;
use App\Http\Middleware\CheckRole;
use App\Livewire\AdminDashboard;
use App\Livewire\ManualTicketForm;
use App\Livewire\TicketList;
use App\Livewire\Admin\UserList;
use App\Livewire\Admin\UserForm;
use App\Livewire\Admin\Profile as AdminProfile;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\BatchManager;
use App\Livewire\Admin\QuickSale;
use App\Livewire\Admin\AuditLogs;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\NotificationsManager;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────
Route::get('/', [PublicTicketController::class, 'index'])->name('home');
Route::get('/bilhetes', [PublicTicketController::class, 'index'])->name('bilhetes');
Route::get('/sobre-o-evento', [PublicTicketController::class, 'about'])->name('about');
Route::get('/consultar', [PublicTicketController::class, 'lookupPage'])->name('tickets.lookup.form');
Route::post('/consultar', [PublicTicketController::class, 'lookup'])->name('tickets.lookup');
Route::get('/bilhetes/{ticket}/download', [PublicTicketController::class, 'download'])
    ->middleware('signed')
    ->name('tickets.download');
Route::get('/bilhetes/{ticket}/download/png', [PublicTicketController::class, 'downloadPng'])
    ->middleware('signed')
    ->name('tickets.download.png');

// ─── Admin Routes (auth required) ───────────────────────────
Route::middleware(['auth', CheckRole::class . ':admin,organizer,super_admin'])->prefix('admin')->group(function () {
    // Existing routes (preserved)
    Route::get('/', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/tickets', TicketList::class)->name('admin.tickets');
    Route::get('/tickets/bulk/download', [AdminController::class, 'bulkDownloadTickets'])->name('admin.tickets.bulk_download');
    Route::get('/tickets/{ticket}/download', [AdminController::class, 'downloadTicket'])->name('admin.tickets.download');
    Route::get('/tickets/{ticket}/preview', [AdminController::class, 'previewTicket'])->name('admin.tickets.preview');
    Route::get('/tickets/{ticket}/download/png', [AdminController::class, 'downloadTicketPng'])->name('admin.tickets.download.png');
    Route::get('/manual', ManualTicketForm::class)->name('admin.manual');
    Route::get('/site', [AdminController::class, 'siteContent'])->name('admin.site');
    Route::post('/site', [AdminController::class, 'updateSiteContent'])->name('admin.site.update');
    Route::get('/tickets/export', [AdminController::class, 'exportCsv'])->name('admin.export');

    // Phase 2 — New routes
    Route::get('/users', UserList::class)->name('admin.users.index');
    Route::get('/users/create', UserForm::class)->name('admin.users.create');
    Route::get('/users/{user}/edit', UserForm::class)->name('admin.users.edit');
    Route::get('/profile', AdminProfile::class)->name('admin.profile');
    Route::get('/settings', SiteSettings::class)->name('admin.settings');
    Route::get('/batches', BatchManager::class)->name('admin.batches');
    Route::get('/quick-sale', QuickSale::class)->name('admin.quick-sale');
    Route::get('/reports', Reports::class)->name('admin.reports');
    Route::get('/audit', AuditLogs::class)->name('admin.audit');
    Route::get('/notifications', NotificationsManager::class)->name('admin.notifications');

    // ─── Sale Scanner ─────────────────────────────────────────────
    // Scanner page — confirms pending tickets at the point of physical sale
    Route::get('/vender', [SaleConfirmController::class, 'index'])->name('admin.sale.scanner');
    Route::post('/vender/confirmar', [SaleConfirmController::class, 'confirm'])->name('admin.sale.confirm');

    // ─── Ticket Deletion (cancelled only) ─────────────────────────
    Route::delete('/tickets/{ticket}', [AdminController::class, 'deleteTicket'])->name('admin.tickets.delete');
});

// ─── Validator Route ─────────────────────────────────────────
Route::get('/validar', [ValidationController::class, 'index'])->name('validator');
Route::middleware('auth')->group(function () {
    Route::post('/validar/bilhete', [ValidationController::class, 'validate'])->name('validator.validate');
    Route::get('/validar/confirmados', [ValidationController::class, 'confirmedList'])->name('validator.confirmed');
});

// ─── Auth Dashboard Redirect ─────────────────────────────────
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// ─── Profile Routes (from Breeze) ────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
