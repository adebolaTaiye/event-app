<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTypes extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_type',
        'ticket_count',
        'ticket_available'
    ];
}
