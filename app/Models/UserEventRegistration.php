<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\UserObserver;
use App\Models\tickets;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

//#[ObservedBy([UserObserver::class])]
class UserEventRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_type_id',
        'quantity',
        'status',
        'reference',
        'amount'
    ];

    public function ticketTypes(){
        return $this->belongsTo(TicketTypes::class,'ticket_type_id');
    }

    public function tickets(){
        return $this->hasMany(tickets::class,'id');
    }

    public function event(){
        return $this->belongsTo(Event::class,'event_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
