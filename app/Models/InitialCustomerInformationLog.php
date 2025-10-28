<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InitialCustomerInformationLog extends Model
{
    protected $table = 'ici_logs';

    protected $fillable = [
        'complaint_number',
        'ticket_number',
        'action',
        'case_status',
        'escalation_level',
        'voice_of_customers',
        'user_id',
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to filter by complaint number
     */
    public function scopeByComplaint($query, $complaintNumber)
    {
        return $query->where('complaint_number', $complaintNumber);
    }

    /**
     * Scope to filter by ticket number
     */
    public function scopeByTicket($query, $ticketNo)
    {
        return $query->where('ticket_number', $ticketNo);
    }

    /**
     * Scope to filter by action type
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
