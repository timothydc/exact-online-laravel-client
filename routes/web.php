<?php

use Illuminate\Support\Facades\Route;
use PolarisDC\Laravel\ExactOnlineConnector\Http\Controllers\ExactOnlineController;

Route::get('authorize', [ExactOnlineController::class, 'startAuthorization'])->name('exact-online.authorize');
Route::get('oauth', [ExactOnlineController::class, 'completeAuthorization'])->name('exact-online.callback');
Route::get('disconnect', [ExactOnlineController::class, 'disconnect'])->name('exact-online.disconnect');
