<?php

use App\Http\Controllers\SupportChatController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// Support chat routes
Route::get('/support', [SupportChatController::class, 'index']);
Route::post('/support/chat', [SupportChatController::class, 'chat']);
Route::post('/support/clear', [SupportChatController::class, 'clear']);

require __DIR__.'/settings.php';
