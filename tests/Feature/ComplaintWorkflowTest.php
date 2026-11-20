<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Profile;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complaint_submission_creates_complaint_and_generates_number(): void
    {
        SystemSetting::set('auto_assignment_enabled', false);

        $student = Profile::factory()->student()->create();
        $dept = Department::factory()->create();
        $cat = ComplaintCategory::factory()->create(['department_id' => $dept->id]);

        $response = $this->actingAs($student)->post(route('student.complaints.store'), [
            'subject' => 'Projector broken in Room 204',
            'department_id' => $dept->id,
            'category_id' => $cat->id,
            'priority' => 'HIGH',
            'description' => 'The ceiling HDMI projector is flickering constantly during lectures.',
        ]);

        $response->assertRedirect();

        $complaint = Complaint::where('student_id', $student->id)->first();
        $this->assertNotNull($complaint);
        $this->assertMatchesRegularExpression('/^CCMS-\d{4}-\d{6}$/', $complaint->complaint_number);
        $this->assertEquals(Complaint::STATUS_SUBMITTED, $complaint->status);

        // Assert status history recorded
        $this->assertDatabaseHas('complaint_status_history', [
            'complaint_id' => $complaint->id,
            'new_status' => Complaint::STATUS_SUBMITTED,
        ]);
    }

    public function test_admin_can_manually_assign_complaint(): void
    {
        $admin = Profile::factory()->admin()->create();
        $dept = Department::factory()->create();
        $management = Profile::factory()->management()->create(['department_id' => $dept->id]);
        $complaint = Complaint::factory()->create(['department_id' => $dept->id, 'status' => Complaint::STATUS_SUBMITTED]);

        $response = $this->actingAs($admin)->post(route('admin.complaints.assign', $complaint->id), [
            'management_id' => $management->id,
            'remarks' => 'Assigned for immediate hardware replacement',
        ]);

        $response->assertRedirect();

        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_ASSIGNED, $complaint->status);

        $this->assertDatabaseHas('complaint_assignments', [
            'complaint_id' => $complaint->id,
            'management_id' => $management->id,
            'assignment_method' => ComplaintAssignment::METHOD_MANUAL,
            'is_active' => true,
        ]);
    }

    public function test_automatic_assignment_selects_lowest_workload_management_member(): void
    {
        $dept = Department::factory()->create();

        // Officer A has 2 active open complaints
        $officerA = Profile::factory()->management()->create([
            'employee_id' => 'EMP001',
            'department_id' => $dept->id,
        ]);
        $complaint1 = Complaint::factory()->create(['department_id' => $dept->id, 'status' => Complaint::STATUS_IN_PROGRESS]);
        $complaint2 = Complaint::factory()->create(['department_id' => $dept->id, 'status' => Complaint::STATUS_IN_PROGRESS]);
        ComplaintAssignment::factory()->create(['complaint_id' => $complaint1->id, 'management_id' => $officerA->id, 'is_active' => true]);
        ComplaintAssignment::factory()->create(['complaint_id' => $complaint2->id, 'management_id' => $officerA->id, 'is_active' => true]);

        // Officer B has 0 active open complaints
        $officerB = Profile::factory()->management()->create([
            'employee_id' => 'EMP002',
            'department_id' => $dept->id,
        ]);

        $targetComplaint = Complaint::factory()->create([
            'department_id' => $dept->id,
            'status' => Complaint::STATUS_SUBMITTED,
        ]);

        $admin = Profile::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.complaints.auto-assign', $targetComplaint->id));
        $response->assertRedirect();

        $targetComplaint->refresh();
        $this->assertEquals(Complaint::STATUS_ASSIGNED, $targetComplaint->status);

        // Verify assigned to officer B (lowest workload)
        $this->assertDatabaseHas('complaint_assignments', [
            'complaint_id' => $targetComplaint->id,
            'management_id' => $officerB->id,
            'assignment_method' => ComplaintAssignment::METHOD_AUTOMATIC,
            'is_active' => true,
        ]);
    }

    public function test_full_resolution_and_verification_lifecycle(): void
    {
        $dept = Department::factory()->create();
        $student = Profile::factory()->student()->create();
        $management = Profile::factory()->management()->create(['department_id' => $dept->id]);
        $admin = Profile::factory()->admin()->create();

        $complaint = Complaint::factory()->create([
            'student_id' => $student->id,
            'department_id' => $dept->id,
            'status' => Complaint::STATUS_ASSIGNED,
        ]);

        ComplaintAssignment::factory()->create([
            'complaint_id' => $complaint->id,
            'management_id' => $management->id,
            'is_active' => true,
        ]);

        // 1. Management starts investigation
        $this->actingAs($management)->post(route('management.complaints.start', $complaint->id));
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_IN_PROGRESS, $complaint->status);

        // 2. Management submits resolution
        $this->actingAs($management)->post(route('management.complaints.resolution.store', $complaint->id), [
            'resolution_description' => 'The faulty HDMI port cable was replaced with an insulated 4K cable. Test completed.',
        ]);
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_RESOLUTION_SUBMITTED, $complaint->status);

        // 3. Admin approves verification
        $this->actingAs($admin)->post(route('admin.complaints.verify', $complaint->id), [
            'decision' => 'approved',
            'remarks' => 'Verified with classroom technician.',
        ]);
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_VERIFIED_FOR_CLOSURE, $complaint->status);

        // 4. Management completes closure
        $this->actingAs($management)->post(route('management.complaints.complete-closure', $complaint->id));
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_CLOSED, $complaint->status);
        $this->assertEquals($management->id, $complaint->closed_by);

        // 5. Student requests reopening
        $this->actingAs($student)->post(route('student.complaints.reopen', $complaint->id), [
            'reason' => 'The screen still flashes green intermittently during morning sessions.',
        ]);
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_REOPENED, $complaint->status);
    }

    public function test_admin_verification_rejection_workflow(): void
    {
        $management = Profile::factory()->management()->create();
        $admin = Profile::factory()->admin()->create();

        $complaint = Complaint::factory()->create([
            'status' => Complaint::STATUS_RESOLUTION_SUBMITTED,
            'resolution_description' => 'Investigated verbally.',
        ]);

        ComplaintAssignment::factory()->create([
            'complaint_id' => $complaint->id,
            'management_id' => $management->id,
            'is_active' => true,
        ]);

        // Admin rejects with mandatory explanation
        $response = $this->actingAs($admin)->post(route('admin.complaints.verify', $complaint->id), [
            'decision' => 'rejected',
            'remarks' => 'Inadequate evidence. Please provide hardware test signature.',
        ]);

        $response->assertRedirect();
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_VERIFICATION_REJECTED, $complaint->status);
        $this->assertEquals('Inadequate evidence. Please provide hardware test signature.', $complaint->verification_remarks);
    }

    public function test_admin_can_emergency_force_close(): void
    {
        $admin = Profile::factory()->admin()->create();
        $complaint = Complaint::factory()->create([
            'status' => Complaint::STATUS_IN_PROGRESS,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.complaints.force-close', $complaint->id), [
            'reason' => 'Duplicate grievance resolved in ticket CCMS-2026-000008.',
        ]);

        $response->assertRedirect();
        $complaint->refresh();
        $this->assertEquals(Complaint::STATUS_CLOSED, $complaint->status);
        $this->assertEquals($admin->id, $complaint->closed_by);
    }
}
