<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DelayReason;

class DelayReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $delayReason = [
            'Delay complaint attending by SC',
            'Delay complaint resolution by SC',
            'Delay at Customer end',
            'Delay Approval Sales Dept',
            'Customer understanding Issue (Policy)',
            'Re-Do Jobs',
            'Premature product failure',
            'Incomplete documentation provided by customer',
            'Parts not available',
            'RTW case not proper follow up',
            'Customer not available',
            'Understanding issue in customer unit by Technician',
            'Customer satisfaction Issue',
            'Part to attend delay',
            'Late parts delivery by courier',
            'Load Shedding issue at customer premises',
            'Local Market repairing Delay',
            'Repeated Parts Failure',
            'SC Estimate is High as compared to Local Market',
            'Technician Soft Skills',
            'Wrong cancellation done at SC end',
            'Poor Product Quality',
        ];

        foreach ($delayReason as $reason) {
            DelayReason::create(['reason' => $reason]);
        }
    }
}
