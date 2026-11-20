<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\ComplaintCategory;
use App\Models\ComplaintComment;
use App\Models\Department;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with institutional defaults and demo credentials.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ComplaintCategorySeeder::class,
            SystemSettingSeeder::class,
        ]);

        $adminDept = Department::where('code', 'ADMIN')->first();
        $cseDept = Department::where('code', 'CSE')->first();

        // 1. Seed initial Administrator
        $admin = Profile::firstOrCreate(
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

        // 2. Seed demo Management Officer
        $management = Profile::firstOrCreate(
            ['employee_id' => 'EMP1001'],
            [
                'full_name' => 'Prof. S. R. Ramanathan',
                'email' => 'mgmt.cse@college.edu',
                'password' => 'Management@12345',
                'role' => Profile::ROLE_MANAGEMENT,
                'department_id' => $cseDept?->id,
                'account_status' => Profile::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed demo Student
        $student = Profile::firstOrCreate(
            ['register_number' => '2026CSE001'],
            [
                'full_name' => 'Aarav Sharma',
                'email' => 'student.cse@college.edu',
                'password' => 'Student@12345',
                'role' => Profile::ROLE_STUDENT,
                'department_id' => $cseDept?->id,
                'study_year' => 3,
                'account_status' => Profile::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // 4. Seed demo complaints if none exist
        if (Complaint::count() === 0) {
            $catLab = ComplaintCategory::where('code', 'LAB')->first() ?? ComplaintCategory::first();
            $catAcad = ComplaintCategory::where('code', 'ACAD')->first() ?? ComplaintCategory::first();

            // Complaint 1: In Progress, assigned to Management
            $complaint1 = Complaint::create([
                'complaint_number' => 'CCMS-2026-000001',
                'student_id' => $student->id,
                'category_id' => $catLab?->id,
                'department_id' => $cseDept?->id,
                'subject' => 'Operating Systems Lab 3 workstation GPU drivers failing',
                'description' => 'Workstations 12 through 18 in OS Laboratory 3 crash consistently when launching Linux virtualization containers for the Advanced OS curriculum practicals.',
                'priority' => Complaint::PRIORITY_HIGH,
                'status' => Complaint::STATUS_IN_PROGRESS,
            ]);

            ComplaintAssignment::create([
                'complaint_id' => $complaint1->id,
                'assigned_by' => $admin->id,
                'management_id' => $management->id,
                'assignment_method' => ComplaintAssignment::METHOD_MANUAL,
                'assigned_at' => now(),
                'is_active' => true,
            ]);

            ComplaintComment::create([
                'complaint_id' => $complaint1->id,
                'sender_id' => $management->id,
                'message' => 'Investigation initiated. Systems Engineering team has identified kernel driver conflict on the workstations.',
                'visibility' => ComplaintComment::VISIBILITY_PUBLIC,
            ]);

            // Complaint 2: Resolution submitted, in Admin Verification Queue
            $complaint2 = Complaint::create([
                'complaint_number' => 'CCMS-2026-000002',
                'student_id' => $student->id,
                'category_id' => $catAcad?->id,
                'department_id' => $cseDept?->id,
                'subject' => 'Request for additional reference materials in Data Structures repository',
                'description' => 'The digital library repository for Semester 5 Data Structures does not contain lecture notes for Module 4 (Graph Algorithms).',
                'priority' => Complaint::PRIORITY_MEDIUM,
                'status' => Complaint::STATUS_RESOLUTION_SUBMITTED,
                'resolution_description' => 'Digitized lecture notes and reference textbooks for Module 4 have been compiled and uploaded to the departmental digital repository. Access verified for all Year 3 students.',
                'resolution_submitted_at' => now()->subHours(2),
            ]);

            ComplaintAssignment::create([
                'complaint_id' => $complaint2->id,
                'assigned_by' => $admin->id,
                'management_id' => $management->id,
                'assignment_method' => ComplaintAssignment::METHOD_AUTOMATIC,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }
    }
}
