<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    public function run(): void
    {
        $adminDept = Department::where('code', 'ADMIN')->first();
        $estateDept = Department::where('code', 'ESTATE')->first();
        $hostelDept = Department::where('code', 'HOSTEL')->first();
        $libraryDept = Department::where('code', 'LIBRARY')->first();
        $transportDept = Department::where('code', 'TRANSPORT')->first();
        $canteenDept = Department::where('code', 'CANTEEN')->first();
        $itDept = Department::where('code', 'IT')->first();
        $cseDept = Department::where('code', 'CSE')->first();

        $categories = [
            ['name' => 'Academics', 'description' => 'Coursework, evaluations, class scheduling, and academic issues', 'department_id' => $cseDept?->id ?? $adminDept?->id],
            ['name' => 'Infrastructure', 'description' => 'Classrooms, benches, fans, lighting, labs, and building maintenance', 'department_id' => $estateDept?->id ?? $adminDept?->id],
            ['name' => 'Hostel', 'description' => 'Hostel rooms, water supply, hygiene, warden support, and electrical issues', 'department_id' => $hostelDept?->id ?? $adminDept?->id],
            ['name' => 'Library', 'description' => 'Book availability, digital access, reading hall facilities, and library cards', 'department_id' => $libraryDept?->id ?? $adminDept?->id],
            ['name' => 'Transport', 'description' => 'College bus routes, timing, driver conduct, and bus pass queries', 'department_id' => $transportDept?->id ?? $adminDept?->id],
            ['name' => 'Canteen', 'description' => 'Food hygiene, canteen menu, pricing, and seating capacity', 'department_id' => $canteenDept?->id ?? $adminDept?->id],
            ['name' => 'IT Support', 'description' => 'Campus Wi-Fi, computer labs, portal login, and network connectivity', 'department_id' => $itDept?->id ?? $adminDept?->id],
            ['name' => 'Administration', 'description' => 'Fee receipts, bonafide certificates, identity cards, and office queries', 'department_id' => $adminDept?->id],
            ['name' => 'Other', 'description' => 'General queries and miscellaneous grievances not covered in other categories', 'department_id' => $adminDept?->id],
        ];

        foreach ($categories as $cat) {
            ComplaintCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
