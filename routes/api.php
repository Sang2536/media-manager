<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MediaFileController;
use App\Http\Controllers\Api\MediaFolderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->name('api.v1.')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('/user', [UserController::class, 'show'])->name('user.show');

        Route::get('/folders', [MediaFolderController::class, 'index'])->name('folder.index');
        Route::get('/folder/{folderId}', [MediaFolderController::class, 'show'])->name('folder.show');

        Route::get('/files', [MediaFileController::class, 'index'])->name('file.index');
        Route::get('/file/{fileId}', [MediaFileController::class, 'show'])->name('file.show');
    });
//});
