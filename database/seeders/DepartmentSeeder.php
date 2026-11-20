<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Science & Engineering', 'code' => 'CSE', 'is_active' => true],
            ['name' => 'Information Technology', 'code' => 'IT', 'is_active' => true],
            ['name' => 'Electronics & Communication Engineering', 'code' => 'ECE', 'is_active' => true],
            ['name' => 'Mechanical Engineering', 'code' => 'MECH', 'is_active' => true],
            ['name' => 'Civil Engineering', 'code' => 'CIVIL', 'is_active' => true],
            ['name' => 'College Administration', 'code' => 'ADMIN', 'is_active' => true],
            ['name' => 'Campus Infrastructure & Maintenance', 'code' => 'ESTATE', 'is_active' => true],
            ['name' => 'Hostel Administration', 'code' => 'HOSTEL', 'is_active' => true],
            ['name' => 'Central Library', 'code' => 'LIBRARY', 'is_active' => true],
            ['name' => 'Transport & Logistics', 'code' => 'TRANSPORT', 'is_active' => true],
            ['name' => 'Canteen & Food Services', 'code' => 'CANTEEN', 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
