<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InitialCustomerInformation extends Model
{
    protected $table = 'initial_customer_information';

    protected $fillable = [
        'ticket_no',
        'service_center',
        'complaint_escalation_date',
        'case_status',
        'complaint_category',
        'agent_name',
        'reason_of_escalation',
        'escalation_level',
        'voice_of_customer',
    ];
}