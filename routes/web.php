<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
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

    Route::get('/event/create', [EventController::class, 'create'])->name('event.create');
    Route::get('/event', [EventController::class, 'index'])->name('event.index');
    Route::get('/event/{event}', [EventController::class, 'show'])->name('event.show');
    Route::get('/event/{event}/edit', [EventController::class, 'edit'])->name('event.edit');
    Route::post('/event/store', [EventController::class, 'store'])->name('event.store');
    Route::delete('/event/{event}', [EventController::class, 'destroy'])->name('event.destroy');
});

require __DIR__.'/auth.php';
