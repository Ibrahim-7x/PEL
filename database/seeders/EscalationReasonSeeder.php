<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EscalationReason;

class EscalationReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $escalationReason = [
            'Behavior Issue- Training Required',
            'Complaint attended but not Resolved-After Sales',
            'Complaint Resolved late-After Sales',
            'Customer Approval Awaited-Pending from Customer',
            'Customer Not Available-Pending from Customer',
            'Customer Phone Off-Pending from Customer',
            'Documents Pending-Pending from Customer',
            'Fake Statement/Remarks by CSO',
            'Fake Statement/Remarks by Tech',
            'High Charges Taken- After Sales',
            'Missing Tools- Training Required',
            'Phone Off/Call Not Received',
            'PNA-After Sales',
            'Poor Product Quality-Quality Side',
            'Re-Do-After Sales',
            'Sales Return Approval-CSD Head Office',
            'Unit is in W/S- After Sales',
            'Technician not contact/visit yet',
            'Other',

        ];

        foreach ($escalationReason as $reason) {
            EscalationReason::create(['reason' => $reason]);
        }
    }
}
