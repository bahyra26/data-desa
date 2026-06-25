<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PerangkatWilayahController;
use App\Http\Controllers\ProfileSettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/settings/profile', [ProfileSettingsController::class, 'edit'])->name('settings.profile');
    Route::put('/settings/profile', [ProfileSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/profile/password', [ProfileSettingsController::class, 'updatePassword'])->name('settings.password.update');

    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('role:super_admin');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('activity-logs.index');
    Route::get('/activity-logs/export/excel', [ExportController::class, 'activityLogsExcel'])
        ->middleware(['role:super_admin', 'throttle:30,1'])
        ->name('activity-logs.export.excel');
    Route::get('/activity-logs/export/pdf', [ExportController::class, 'activityLogsPdf'])
        ->middleware(['role:super_admin', 'throttle:30,1'])
        ->name('activity-logs.export.pdf');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])
        ->middleware('role:super_admin')
        ->name('activity-logs.show');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{filename}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/{filename}/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });

    Route::get('/desa', [DesaController::class, 'index'])->name('desa.index');
    Route::get('/desa/export/excel', [ExportController::class, 'desaExcel'])->middleware('throttle:30,1')->name('desa.export.excel');
    Route::get('/desa/export/pdf', [ExportController::class, 'desaListPdf'])->middleware('throttle:30,1')->name('desa.export.list-pdf');

    Route::middleware('role:super_admin,operator')->group(function () {
        Route::get('/desa/create', [DesaController::class, 'create'])->name('desa.create');
        Route::post('/desa', [DesaController::class, 'store'])->name('desa.store');
        Route::get('/desa/{desa}/edit', [DesaController::class, 'edit'])->name('desa.edit');
        Route::match(['put', 'patch'], '/desa/{desa}', [DesaController::class, 'update'])->name('desa.update');
    });

    Route::get('/desa/{desa}', [DesaController::class, 'show'])->name('desa.show');
    Route::get('/desa/{desa}/export-pdf', [ExportController::class, 'desaPdf'])->middleware('throttle:30,1')->name('desa.export.pdf');

    Route::delete('/desa/{desa}', [DesaController::class, 'destroy'])
        ->middleware('role:super_admin')
        ->name('desa.destroy');

    Route::get('/perangkat', [PerangkatWilayahController::class, 'index'])->name('perangkat.index');
    Route::get('/perangkat/export/excel', [ExportController::class, 'perangkatExcel'])->middleware('throttle:30,1')->name('perangkat.export.excel');
    Route::get('/perangkat/export/pdf', [ExportController::class, 'perangkatPdf'])->middleware('throttle:30,1')->name('perangkat.export.pdf');

    Route::middleware('role:super_admin,operator')->group(function () {
        Route::get('/perangkat/create', [PerangkatWilayahController::class, 'create'])->name('perangkat.create');
        Route::post('/perangkat', [PerangkatWilayahController::class, 'store'])->name('perangkat.store');
        Route::get('/perangkat/{perangkat}/edit', [PerangkatWilayahController::class, 'edit'])->name('perangkat.edit');
        Route::match(['put', 'patch'], '/perangkat/{perangkat}', [PerangkatWilayahController::class, 'update'])->name('perangkat.update');
    });

    Route::delete('/perangkat/{perangkat}', [PerangkatWilayahController::class, 'destroy'])
        ->middleware('role:super_admin')
        ->name('perangkat.destroy');
});
