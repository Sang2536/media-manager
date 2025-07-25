<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\MediaFolderController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MediaLogController;
use App\Http\Controllers\MediaMetadataController;
use App\Http\Controllers\MediaTagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('media')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('media.index');

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('media.dashboard');
    Route::get('/guide', [DashboardController::class, 'guide'])->name('media.guide');

    Route::resource('/folders', MediaFolderController::class)->names('media-folders');

    Route::resource('/files', MediaFileController::class)->names('media-files');

    Route::resource('/tags', MediaTagController::class)->names('media-tags');

    Route::resource('/metadata', MediaMetadataController::class)->names('media-metadata');

    Route::resource('/logs', MedialogController::class)->names('media-logs');
    Route::delete('/logs/clear', [MediaLogController::class, 'clear'])->name('media-logs.clear');
});

