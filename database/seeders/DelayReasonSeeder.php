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
        $delayReasons = [
            'Parts not available',
            'Technician not available',
            'Customer not available',
            'Weather conditions',
            'Transportation issues',
            'Supplier delay',
            'Technical complexity',
            'Documentation issues',
            'Approval pending',
            'Other'
        ];

        foreach ($delayReasons as $reason) {
            DelayReason::create(['reason' => $reason]);
        }
    }
}
