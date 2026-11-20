<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintCounter;
use App\Models\Department;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_counter_detects_existing_complaints_and_avoids_unique_collision(): void
    {
        $dept = Department::factory()->create();
        $student = Profile::factory()->student()->create(['department_id' => $dept->id]);
        $category = ComplaintCategory::factory()->create();

        // Simulate pre-existing complaints (e.g. from seeders)
        Complaint::factory()->create([
            'complaint_number' => 'CCMS-2026-000001',
            'student_id' => $student->id,
            'category_id' => $category->id,
            'department_id' => $dept->id,
        ]);

        Complaint::factory()->create([
            'complaint_number' => 'CCMS-2026-000002',
            'student_id' => $student->id,
            'category_id' => $category->id,
            'department_id' => $dept->id,
        ]);

        // Counter table is initially completely empty
        $this->assertDatabaseEmpty('complaint_counters');

        // Next generated number should automatically be CCMS-2026-000003
        $number = ComplaintCounter::getNextComplaintNumber(2026);
        $this->assertSame('CCMS-2026-000003', $number);

        // Next one is CCMS-2026-000004
        $number2 = ComplaintCounter::getNextComplaintNumber(2026);
        $this->assertSame('CCMS-2026-000004', $number2);
    }

    public function test_student_can_submit_complaint_when_seeded_complaint_exists(): void
    {
        $dept = Department::factory()->create();
        $student = Profile::factory()->student()->create(['department_id' => $dept->id]);
        $category = ComplaintCategory::factory()->create();

        // Pre-existing complaint #1
        Complaint::factory()->create([
            'complaint_number' => 'CCMS-2026-000001',
            'student_id' => $student->id,
            'category_id' => $category->id,
            'department_id' => $dept->id,
        ]);

        $response = $this->actingAs($student)->post('/student/complaints', [
            'category_id' => $category->id,
            'department_id' => $dept->id,
            'subject' => 'Hostel food quality issue',
            'description' => 'The food provided in the mess was cold and unhygienic.',
            'priority' => 'HIGH',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('complaints', [
            'complaint_number' => 'CCMS-2026-000002',
            'subject' => 'Hostel food quality issue',
        ]);
    }
}
