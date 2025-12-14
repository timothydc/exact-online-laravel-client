<?php


use TimothyDC\ExactOnline\LaravelClient\Http\Controllers\ExactOnlineController;

Route::get('oauth/start', [ExactOnlineController::class, 'startAuthorization'])->name('exact-online.authorize');
Route::get('oauth/complete', [ExactOnlineController::class, 'completeAuthorization'])->name('exact-online.callback');
Route::get('oauth/disconnect', [ExactOnlineController::class, 'disconnect'])->name('exact-online.disconnect');
Route::get('oauth/test', [ExactOnlineController::class, 'test'])->name('exact-online.test');
