<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCenter;

class ServiceCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service_center')->insert([
            [
                'coms_sc' => 'BAHAWALPUR',
                'sc' => 'BWP',
                'service_center' => 'BAHAWALPUR',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'DERA ISMAIL KHAN',
                'sc' => 'DIK',
                'service_center' => 'DERA ISMAIL KHAN',
                'zone' => 'North',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'FAISALABAD',
                'sc' => 'FSD',
                'service_center' => 'FAISALABAD',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'GUJRAT',
                'sc' => 'GRT',
                'service_center' => 'GUJRAT',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'GUJRANWALA',
                'sc' => 'GRW',
                'service_center' => 'GUJRANWALA',
                'zone' => 'Lahore',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'KARACHI',
                'sc' => 'KHI-1',
                'service_center' => 'KARACHI',
                'zone' => 'South',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'KARACHI-2',
                'sc' => 'KHI-2',
                'service_center' => 'KARACHI-2',
                'zone' => 'South',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'KARACHI-3',
                'sc' => 'KHI-3',
                'service_center' => 'KARACHI-3',
                'zone' => 'South',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
            'coms_sc' => 'LAHORE',
                'sc' => 'LHR-1',
                'service_center' => 'LAHORE',
                'zone' => 'Lahore',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
            'coms_sc' => 'LAHORE-2',
                'sc' => 'LHR-2',
                'service_center' => 'LAHORE-2',
                'zone' => 'Lahore',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'MULTAN',
                'sc' => 'MTN',
                'service_center' => 'MULTAN',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'PESHAWAR',
                'sc' => 'PWR',
                'service_center' => 'PESHAWAR',
                'zone' => 'North',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'QUETTA',
                'sc' => 'QTA',
                'service_center' => 'QUETTA',
                'zone' => 'South',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'RAWALPINDI',
                'sc' => 'RWP',
                'service_center' => 'RAWALPINDI',
                'zone' => 'North',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'R Y Khan',
                'sc' => 'RYK',
                'service_center' => 'R Y KHAN (FR)',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'SARGODHA',
                'sc' => 'SGD',
                'service_center' => 'SARGODHA',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'SAHIWAL',
                'sc' => 'SHW',
                'service_center' => 'SAHIWAL',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'SIALKOT',
                'sc' => 'SKT',
                'service_center' => 'SIALKOT',
                'zone' => 'Central',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coms_sc' => 'SUKKUR',
                'sc' => 'SUK',
                'service_center' => 'SUKKUR',
                'zone' => 'South',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
