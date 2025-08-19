<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HappyCallStatus extends Model
{
    protected $table = 'happy_call_status';

    protected $fillable = [
        'agent_id',
        'resolved_date',
        'happy_call_date',
        'customer_satisfied',
        'delay_reason',
        'voice_of_customer'
    ];
}
