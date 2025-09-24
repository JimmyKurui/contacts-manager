<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Group\GroupController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('contacts', ContactController::class);

    Route::resource('groups', GroupController::class);
    Route::post('groups/{groupId}/attach-contact', [GroupController::class, 'attachContact'])->name('groups.attachContact');
    Route::post('groups/{groupId}/detach-contact', [GroupController::class, 'detachContact'])->name('groups.detachContact');
});

require __DIR__.'/auth.php';
