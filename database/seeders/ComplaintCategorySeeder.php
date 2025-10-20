<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ComplaintCategory;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ComplaintCategory = 
        [
            'Dealer Feedback Survey',
            'Cancelled Job (Assigned) Survey',
            'Cancelled Job (Un-Assigned) Survey',
            'Happy Calls',
            'Re-verification of Happy Calls',
            'Customer Care (Email)',
            'Speakup (Email)',
            'Escalation Job',
            'Social Media',
            'WhatsApp/SMS',
            'Online Complaint Registration Form',
            'Corporate Complaint',
            'Head Office Care-of',
            'PEL Dost',
            'Head Office Exchange',
        ];

        foreach ($ComplaintCategory as $category) {
            ComplaintCategory::create(['category_name' => $category]);
        }
    }
}
