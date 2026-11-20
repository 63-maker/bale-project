<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\Department;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = Profile::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_student_cannot_access_management_dashboard(): void
    {
        $student = Profile::factory()->student()->create();

        $response = $this->actingAs($student)->get('/management/dashboard');

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_student_cannot_view_other_students_complaint(): void
    {
        $studentA = Profile::factory()->student()->create();
        $studentB = Profile::factory()->student()->create();

        $complaintB = Complaint::factory()->create([
            'student_id' => $studentB->id,
        ]);

        $response = $this->actingAs($studentA)->get(route('student.complaints.show', $complaintB->id));

        $response->assertForbidden();
    }

    public function test_management_cannot_view_unassigned_complaint(): void
    {
        $managementA = Profile::factory()->management()->create();
        $complaint = Complaint::factory()->create();

        $response = $this->actingAs($managementA)->get(route('management.complaints.show', $complaint->id));

        $response->assertForbidden();
    }

    public function test_management_cannot_access_admin_pages(): void
    {
        $management = Profile::factory()->management()->create();

        $response = $this->actingAs($management)->get('/admin/settings');

        $response->assertRedirect(route('management.dashboard'));
    }

    public function test_management_cannot_verify_resolutions(): void
    {
        $management = Profile::factory()->management()->create();
        $complaint = Complaint::factory()->create([
            'status' => Complaint::STATUS_RESOLUTION_SUBMITTED,
        ]);

        $response = $this->actingAs($management)->post(route('admin.complaints.verify', $complaint->id), [
            'decision' => 'approved',
        ]);

        $response->assertRedirect(route('management.dashboard'));
    }

    public function test_management_cannot_close_complaint_before_admin_verification(): void
    {
        $management = Profile::factory()->management()->create();
        $complaint = Complaint::factory()->create([
            'status' => Complaint::STATUS_IN_PROGRESS,
        ]);

        ComplaintAssignment::factory()->create([
            'complaint_id' => $complaint->id,
            'management_id' => $management->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($management)->post(route('management.complaints.complete-closure', $complaint->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => Complaint::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_only_admin_can_create_management_accounts(): void
    {
        $student = Profile::factory()->student()->create();
        $dept = Department::factory()->create();

        // Student attempt
        $response = $this->actingAs($student)->post(route('admin.management.store'), [
            'full_name' => 'Fake Management',
            'employee_id' => 'EMP999',
            'email' => 'fake@college.edu',
            'department_id' => $dept->id,
            'password' => 'Password@123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertDatabaseMissing('profiles', ['employee_id' => 'EMP999']);

        // Admin attempt succeeds
        $admin = Profile::factory()->admin()->create();

        $adminResponse = $this->actingAs($admin)->post(route('admin.management.store'), [
            'full_name' => 'Real Management',
            'employee_id' => 'EMP999',
            'email' => 'real@college.edu',
            'department_id' => $dept->id,
            'password' => 'Password@123',
        ]);

        $adminResponse->assertRedirect();
        $this->assertDatabaseHas('profiles', [
            'employee_id' => 'EMP999',
            'role' => Profile::ROLE_MANAGEMENT,
        ]);
    }
}
