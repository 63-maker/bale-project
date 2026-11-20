<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ComplaintCategorySeeder::class,
            SystemSettingSeeder::class,
        ]);

        $adminDept = Department::where('code', 'ADMIN')->first();

        // Seed default initial administrator if not present
        Profile::firstOrCreate(
            ['email' => 'admin@college.edu'],
            [
                'full_name' => 'College Administrator',
                'password' => 'Admin@12345',
                'role' => Profile::ROLE_ADMIN,
                'department_id' => $adminDept?->id,
                'account_status' => Profile::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );
    }
}
