<?php

namespace App\Http\Controllers;

use App\Models\tickets;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class VerifyTicketController extends Controller
{
    public function __invoke($verificationCode)
    {

        $ticket = tickets::where('verification_code',$verificationCode)->first();

        Gate::authorize('update',$ticket);

        if($ticket) {
            if($ticket->status == 'booked'){
                $ticket->status == 'validated';
                return Inertia::render('Event/TicketValidation',[
                    'message' => "ticket validated successful"
                ]);
            }else if($ticket->status == 'validated'){
                return Inertia::render('Event/TicketValidation',[
                    'message' => "ticket has already been validated"
                ]);
            }else {
                 return Inertia::render('Event/TicketValidation',[
                    'message' => "invalid ticket"
                ]);
            }
        }else {
             return Inertia::render('Event/TicketValidation',[
                    'message' => "ticket does not exist"
                ]);
        }
    }
}
