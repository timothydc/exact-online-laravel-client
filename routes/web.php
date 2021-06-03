<?php

use PolarisDC\ExactOnline\ExactOnlineClient\Http\Controllers\ExactOnlineController;

Route::get('authorize', [ExactOnlineController::class, 'authorizeExactConnection'])->name('exact.authorize');
Route::get('oauth', [ExactOnlineController::class, 'callbackAuthorizeExactConnection'])->name('exact.callback');
Route::get('disconnect', [ExactOnlineController::class, 'disconnectExactConnection'])->name('exact.disconnect');
