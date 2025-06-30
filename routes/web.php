<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\VerifyTicketController;
use App\Http\Middleware\ProtectAttendeeRoutes;
use App\Http\Controllers\OrganizerDashboardController;
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

//Route::get('/dashboard', function () {
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/paystack/webhook', [UserEventController::class, 'handleWebhook'])->name('initialize.transaction');
Route::get('/payment/callback', [UserEventController::class, 'handleCallback'])->name('payment.callback');
Route::get('/confirm_page', [UserEventController::class, 'paymentSuccess'])->name('page.confirm');
//Route::get('/invoices/{ticketId}',[UserEventController::class,'generateTicketInvoice'])->name('invoice.ticket');


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
    Route::patch('/event/{event}', [EventController::class, 'update'])->name('event.update');

    Route::middleware(ProtectAttendeeRoutes::class)->group(function () {
        Route::get('/index', [UserEventController::class, 'index'])->name('user.index');
        Route::get('/events/{event}', [UserEventController::class, 'show'])->name('user.show');
        Route::post('/events/register', [UserEventController::class, 'register'])->name('user.register');
        Route::post('/events/pay_for_tickets', [UserEventController::class, 'payForTickets'])->name('user.payment');
        Route::get('/complete_payment', [UserEventController::class, 'getPaymentCompletionPage'])->name('user.complete_payment');
    });

    Route::get('/verify/{validationCode}', VerifyTicketController::class)->name('ticket.verify');
    
});

require __DIR__ . '/auth.php';
