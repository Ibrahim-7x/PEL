<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InitialCustomerInformationAuditLog extends Model
{
    protected $table = 'initial_customer_information_audit_logs';

    protected $fillable = [
        'complaint_number',
        'ticket_no',
        'action',
        'escalation_level',
        'old_values',
        'new_values',
        'changed_fields',
        'user_name',
        'user_role',
        'user_id',
        'notes',
        'action_timestamp',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'action_timestamp' => 'datetime',
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
        return $query->where('ticket_no', $ticketNo);
    }

    /**
     * Scope to filter by action type
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('action_timestamp', [$startDate, $endDate]);
    }
}
