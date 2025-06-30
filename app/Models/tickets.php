<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserEventRegistration;

class tickets extends Model
{
    protected $fillable = [
        'user_event_registration_id',
        'validation_code',
        'status'
    ];

    public function userEventRegistration()
    {
        return $this->belongsTo(UserEventRegistration::class,'user_event_registration_id');
    }
}
