<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Event extends Model
{
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

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => env('APP_URL').Storage::url($value)
        );
    }

    public function ticketTypes(){
        return $this->hasMany(TicketTypes::class,'event_id');
    }
}
