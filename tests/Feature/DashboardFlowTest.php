<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_ui_appears_first_on_root_url(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('System Authentication');
        $response->assertSee('Sign In as Student');
        $response->assertSee('Quick Demo Sign-In');
    }

    public function test_student_sees_user_details_and_functions_after_login(): void
    {
        $dept = Department::factory()->create(['name' => 'Computer Science & Engineering', 'code' => 'CSE']);
        $student = Profile::factory()->student()->create([
            'full_name' => 'Aarav Sharma',
            'register_number' => '2026CSE001',
            'email' => 'student.cse@college.edu',
            'department_id' => $dept->id,
            'study_year' => 3,
            'account_status' => Profile::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertStatus(200);
        // Details according to user input:
        $response->assertSee('Authenticated Student Profile');
        $response->assertSee('Aarav Sharma');
        $response->assertSee('2026CSE001');
        $response->assertSee('student.cse@college.edu');
        $response->assertSee('3 Year Undergraduate');
        // Functions:
        $response->assertSee('Authorized Student Functions');
        $response->assertSee('Submit New Complaint');
        $response->assertSee('My Complaints Queue');
        $response->assertSee('Officer Discussions');
        $response->assertSee('Request Reopening');
    }

    public function test_management_sees_user_details_and_functions_after_login(): void
    {
        $dept = Department::factory()->create(['name' => 'Computer Science & Engineering', 'code' => 'CSE']);
        $mgmt = Profile::factory()->management()->create([
            'full_name' => 'Prof. S. R. Ramanathan',
            'employee_id' => 'EMP1001',
            'email' => 'mgmt.cse@college.edu',
            'department_id' => $dept->id,
            'account_status' => Profile::STATUS_ACTIVE,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($mgmt)->get('/management/dashboard');

        $response->assertStatus(200);
        // Details according to user input:
        $response->assertSee('Authenticated Management Profile');
        $response->assertSee('Prof. S. R. Ramanathan');
        $response->assertSee('EMP1001');
        $response->assertSee('mgmt.cse@college.edu');
        // Functions:
        $response->assertSee('Authorized Management Functions');
        $response->assertSee('Assigned Complaints Queue');
        $response->assertSee('Start Investigation');
        $response->assertSee('Submit Resolution');
        $response->assertSee('Verification Status');
    }

    public function test_admin_sees_user_details_and_functions_after_login(): void
    {
        $dept = Department::factory()->create(['name' => 'College Administration', 'code' => 'ADMIN']);
        $admin = Profile::factory()->admin()->create([
            'full_name' => 'College Administrator',
            'email' => 'admin@college.edu',
            'department_id' => $dept->id,
            'account_status' => Profile::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Details according to user input:
        $response->assertSee('Authenticated System Administrator Profile');
        $response->assertSee('College Administrator');
        $response->assertSee('admin@college.edu');
        // Functions:
        $response->assertSee('Authorized Administrative Functions');
        $response->assertSee('Verification Queue');
        $response->assertSee('Complaint Assignment');
        $response->assertSee('Management Accounts');
        $response->assertSee('Student Directory');
        $response->assertSee('Departments & Categories', false);
        $response->assertSee('Analytics & Reports', false);
    }
}
