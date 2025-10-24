<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InitialCustomerInformation extends Model
{
    protected $table = 'initial_customer_information';

    protected $fillable = [
        'ticket_number',
        'complaint_id',
        'service_center',
        'complaint_escalation_date',
        'aging',
        'case_status',
        'complaint_category',
        'agent_name',
        'reason_of_escalation',
        'escalation_level',
        'voice_of_customer',
        'user_id',
    ];
 
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'ici_id');
    }

    public function happyCallStatus()
    {
        return $this->hasOne(HappyCallStatus::class, 'ici_id');
    }

    public function coms()
    {
        return $this->belongsTo(Coms::class, 'complaint_id');
    }
}