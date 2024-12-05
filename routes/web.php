<?php

use App\Http\Controllers\GuestUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Invocable\GenerateCsrfToken;
use Illuminate\Support\Facades\Route;


Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('/csrf-token', GenerateCsrfToken::class);

Route::apiResource('users', GuestUserController::class)->parameters([
    'users' => 'guestUser'
]);
