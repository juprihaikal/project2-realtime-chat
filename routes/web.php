<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/conversation/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/private', [ChatController::class, 'storePrivate'])->name('chat.private');
    Route::post('/chat/group', [ChatController::class, 'storeGroup'])->name('chat.group');
    Route::post('/send-message', [ChatController::class, 'send'])->name('chat.send');
});

require __DIR__.'/auth.php';
