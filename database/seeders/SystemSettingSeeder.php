<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'auto_assignment_enabled' => true,
            'college_name' => 'Apex Institute of Technology & Engineering',
            'support_email' => 'grievance@apex.edu',
            'max_attachments_per_complaint' => 5,
            'max_file_size_mb' => 5,
            'sla_hours' => [
                'LOW' => 120,
                'MEDIUM' => 72,
                'HIGH' => 48,
                'CRITICAL' => 24,
            ],
            'email_notifications_enabled' => true,
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::set($key, $value);
        }
    }
}
