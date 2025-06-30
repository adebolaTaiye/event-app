<?php

namespace App\Observers;

use App\Models\UserEventRegistration;
use App\Models\TicketTypes;
use App\Models\Event;

class UserObserver
{
    /**
     * Handle the UserEventRegistration "created" event.
     */
    public function created(UserEventRegistration $userEventRegistration): void
    {
        //  $ticketTypes = TicketTypes::where('id',$userEventRegistration->ticket_type_id)->first();
        //  $event = Event::where('id',$userEventRegistration->event_id)->first();
        //  $ticketTypesTotal;

        //  if($ticketTypes){
        //      $ticketTypes->ticket_available -= $userEventRegistration->quantity;
        //      $ticketTypes->save();
        //  }

        //  if($event){
        //      $event->available_ticket -= $userEventRegistration->quantity;
        //      $event->save();
        //  }
    }

    /**
     * Handle the UserEventRegistration "updated" event.
     */
    public function updated(UserEventRegistration $userEventRegistration): void
    {
        //  $ticketTypes = TicketTypes::where('id',$userEventRegistration->ticket_type_id)->first();
        //  $event = Event::where('id',$userEventRegistration->event_id)->first();
        //  $ticketTypesTotal;

        //  if($ticketTypes){
        //      $ticketTypes->ticket_available =  $ticketTypes->ticket_count - $userEventRegistration->quantity;
        //      $ticketTypes->save();
        //  }

        //  if($event){
        //      $event->available_ticket =  $event->total_ticket - $userEventRegistration->quantity;
        //      $event->save();
        //  }
    }

    /**
     * Handle the UserEventRegistration "deleted" event.
     */
    public function deleted(UserEventRegistration $userEventRegistration): void
    {
        //
    }

    /**
     * Handle the UserEventRegistration "restored" event.
     */
    public function restored(UserEventRegistration $userEventRegistration): void
    {
        //
    }

    /**
     * Handle the UserEventRegistration "force deleted" event.
     */
    public function forceDeleted(UserEventRegistration $userEventRegistration): void
    {
        //
    }
}
