<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PublicController;

// ─── Public Routes (No Auth Required) ───
Route::get('/portal', [PublicController::class, 'home'])->name('public.home');
Route::get('/portal/request', [PublicController::class, 'requestForm'])->name('public.request');

// Rate-limited: max 5 submissions per hour per IP
Route::post('/portal/request', [PublicController::class, 'submitRequest'])
    ->middleware('throttle:public-request')
    ->name('public.submit');

Route::get('/portal/success/{tracking_code}', [PublicController::class, 'success'])->name('public.success');
Route::get('/portal/track', [PublicController::class, 'trackForm'])->name('public.track');

// Rate-limited: max 30 tracking lookups per minute per IP
Route::post('/portal/track', [PublicController::class, 'track'])
    ->middleware('throttle:public-track')
    ->name('public.track.search');

// ─── Guest Routes ───
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // Rate-limited: max 5 login attempts per minute per IP
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
});

// ─── Authenticated Routes ───
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard — accessible by admin and staff
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Admin Only: destructive & management actions ───
    Route::middleware('role:admin')->group(function () {
        // Resident management (create, edit, delete)
        Route::resource('residents', ResidentController::class)->except(['index', 'show']);

        // Resident CSV export
        Route::get('residents/export/csv', [ResidentController::class, 'export'])->name('residents.export');

        // Resident CSV import
        Route::post('residents/import/csv', [ResidentController::class, 'import'])->name('residents.import');

        // Document Type management (create, edit, delete, toggle, reorder)
        Route::resource('document-types', DocumentTypeController::class)->except(['index', 'show']);
        Route::patch('document-types/{document_type}/toggle', [DocumentTypeController::class, 'toggle'])->name('document-types.toggle');
        Route::post('document-types/reorder', [DocumentTypeController::class, 'reorder'])->name('document-types.reorder');

        // Delete requests (admin only)
        Route::delete('requests/{request_item}', [DocumentRequestController::class, 'destroy'])->name('requests.destroy');
    });

    // ─── Admin & Staff: view access ───
    Route::middleware('role:admin,staff')->group(function () {
        // Residents
        Route::resource('residents', ResidentController::class)->only(['index', 'show']);

        // Document Types (view only)
        Route::get('document-types', [DocumentTypeController::class, 'index'])->name('document-types.index');

        // Document Requests (view only)
        Route::get('requests/check-updates', [DocumentRequestController::class, 'checkUpdates'])->name('requests.checkUpdates');
        Route::get('requests', [DocumentRequestController::class, 'index'])->name('requests.index');
        Route::get('requests/create', [DocumentRequestController::class, 'create'])->name('requests.create');
        Route::post('requests', [DocumentRequestController::class, 'store'])->name('requests.store');
        Route::get('requests/{request_item}', [DocumentRequestController::class, 'show'])->name('requests.show');
        Route::get('requests/{request_item}/print', [DocumentRequestController::class, 'print'])->name('requests.print');

        // Status actions — both admin and staff can process requests
        Route::post('requests/{request_item}/approve', [DocumentRequestController::class, 'approve'])->name('requests.approve');
        Route::post('requests/{request_item}/release', [DocumentRequestController::class, 'release'])->name('requests.release');
        Route::post('requests/{request_item}/reject',  [DocumentRequestController::class, 'reject'])->name('requests.reject');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
});
