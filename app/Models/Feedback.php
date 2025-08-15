<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = ['ici_id', 'name', 'role', 'message'];

    public function ici()
    {
        return $this->belongsTo(InitialCustomerInformation::class, 'ici_id');
    }
}
