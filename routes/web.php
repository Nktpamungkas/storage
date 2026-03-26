<?php

use App\Http\Controllers\DriveController;
use App\Http\Controllers\DriveFileController;
use App\Http\Controllers\DriveFolderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DriveController::class, 'index'])->name('dashboard');
    Route::post('/folders', [DriveFolderController::class, 'store'])->name('drive.folders.store');
    Route::patch('/folders/{folder}', [DriveFolderController::class, 'update'])->name('drive.folders.update');
    Route::delete('/folders/{folder}', [DriveFolderController::class, 'destroy'])->name('drive.folders.destroy');
    Route::post('/files', [DriveFileController::class, 'store'])->name('drive.files.store');
    Route::get('/files/{file}/download', [DriveFileController::class, 'download'])->name('drive.files.download');
    Route::patch('/files/{file}', [DriveFileController::class, 'update'])->name('drive.files.update');
    Route::delete('/files/{file}', [DriveFileController::class, 'destroy'])->name('drive.files.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
