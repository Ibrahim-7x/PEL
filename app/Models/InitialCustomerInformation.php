<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    protected static function booted()
    {
        static::created(function ($model) {
            self::logAction($model, 'CREATED');
        });

        static::updated(function ($model) {
            self::logAction($model, 'UPDATED');
        });
    }

    private static function logAction($model, $action)
    {
        $user = Auth::user();

        InitialCustomerInformationLog::create([
            'complaint_number' => $model->complaint_id,
            'ticket_number' => $model->ticket_number,
            'action' => $action,
            'case_status' => $model->case_status,
            'escalation_level' => $model->escalation_level,
            'voice_of_customers' => $model->voice_of_customer,
            'user_id' => $user ? $user->id : null,
        ]);
    }

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