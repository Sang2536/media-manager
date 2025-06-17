<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/media', [MediaController::class, 'index'])->name('media.index');
