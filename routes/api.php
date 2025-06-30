<?php

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserEventController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/invoices/{ticketId}',[UserEventController::class,'generateTicketInvoice'])->name('invoice.ticket');
