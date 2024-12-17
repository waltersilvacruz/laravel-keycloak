<?php
use Illuminate\Support\Facades\Route;
use TCEMT\KeyCloak\Http\Controllers\KeyCloakController;

Route::get('/auth/redirect', [KeyCloakController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback', [KeyCloakController::class, 'callback'])->name('auth.callback');
Route::get('/auth/logout', [KeyCloakController::class, 'logout'])->name('auth.logout');
