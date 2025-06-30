<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image',
        'date',
        'total_ticket',
        'available_ticket',
        'registration_expires_at'
    ];


    public function ticketTypes(){
        return $this->hasMany(TicketTypes::class,'event_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
