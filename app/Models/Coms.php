<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coms extends Model
{
    protected $table = 'coms';

    protected $fillable = [
        'complaint_number',
        'job',
        'coms_complaint_date',
        'job_type',
        'customer_name',
        'contact_number',
        'technician_name',
        'date_of_purchase',
        'product',
        'job_status',
        'problem',
        'work_done',
    ];

    public function initialCustomerInformation()
    {
        return $this->hasOne(InitialCustomerInformation::class, 'complaint_id');
    }
}