<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type',
        'ticket_count',
        'ticket_available',
        'ticket_price'
    ];

    public function ticketTypes(){
        return $this->hasMany(UserEventRegistration::class,'ticket_type_id');
    }
}
