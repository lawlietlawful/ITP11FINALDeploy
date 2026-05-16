<?php

use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\RealtimeStatusController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['system.settings'])->group(function () {
    Route::get('/', [PublicController::class, 'home'])->name('public.home');
    Route::get('/portal/request', [PublicController::class, 'requestForm'])->name('public.request');

    Route::post('/portal/request', [PublicController::class, 'submitRequest'])
        ->middleware('throttle:public-request')
        ->name('public.submit');

    Route::get('/portal/success/{tracking_code}', [PublicController::class, 'success'])->name('public.success');
    Route::get('/portal/track', [PublicController::class, 'trackForm'])->name('public.track');

    Route::post('/portal/track', [PublicController::class, 'track'])
        ->middleware('throttle:public-track')
        ->name('public.track.search');
        
    Route::view('/maintenance', 'public.maintenance')->name('public.maintenance');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::get('/profile/photo/serve', [ProfileController::class, 'servePhoto'])->name('profile.photo.serve');
    Route::delete('/profile/sessions', [ProfileController::class, 'logoutOtherBrowserSessions'])->name('profile.sessions.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::resource('residents', ResidentController::class);
    Route::get('residents/export/csv', [ResidentController::class, 'export'])->name('residents.export');
    Route::post('residents/import/csv', [ResidentController::class, 'import'])->name('residents.import');

    Route::resource('document-types', DocumentTypeController::class)->except(['show']);
    Route::patch('document-types/{document_type}/toggle', [DocumentTypeController::class, 'toggle'])->name('document-types.toggle');
    Route::post('document-types/reorder', [DocumentTypeController::class, 'reorder'])->name('document-types.reorder');

    Route::get('requests', [DocumentRequestController::class, 'index'])->name('requests.index');
    Route::get('requests/create', [DocumentRequestController::class, 'create'])->name('requests.create');
    Route::post('requests', [DocumentRequestController::class, 'store'])->name('requests.store');
    Route::get('requests/{request_item}', [DocumentRequestController::class, 'show'])->name('requests.show');
    Route::get('requests/{request_item}/print', [DocumentRequestController::class, 'print'])->name('requests.print');
    Route::post('requests/{request_item}/approve', [DocumentRequestController::class, 'approve'])->name('requests.approve');
    Route::post('requests/{request_item}/release', [DocumentRequestController::class, 'release'])->name('requests.release');
    Route::post('requests/{request_item}/ready-for-pickup', [DocumentRequestController::class, 'readyForPickup'])->name('requests.readyForPickup');
    Route::post('requests/{request_item}/reject', [DocumentRequestController::class, 'reject'])->name('requests.reject');
    Route::delete('requests/{request_item}', [DocumentRequestController::class, 'destroy'])->name('requests.destroy');

    Route::post('notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');

    Route::get('system/realtime-status', [RealtimeStatusController::class, 'show'])
        ->name('system.realtime.status');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // TEMPORARY: Test email delivery
    Route::get('/test-email', function () {
        try {
            \Illuminate\Support\Facades\Mail::raw(
                'This is a test email from VistaBarangay. If you see this, email delivery is working!',
                function ($message) {
                    $message->to('lianzyrellelorejo21@gmail.com')
                            ->subject('VistaBarangay - Email Test');
                }
            );
            return response('<h1 style="color:green">✅ Email sent successfully!</h1><p>Check your inbox (and spam folder) for the test email.</p>', 200);
        } catch (\Throwable $e) {
            return response('<h1 style="color:red">❌ Email FAILED</h1>'
                . '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>'
                . '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>', 500);
        }
    });
});
