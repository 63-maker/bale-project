<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintAssignmentFactory extends Factory
{
    protected $model = ComplaintAssignment::class;

    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'management_id' => Profile::factory()->management(),
            'assigned_by' => Profile::factory()->admin(),
            'assignment_method' => ComplaintAssignment::METHOD_MANUAL,
            'is_active' => true,
            'assigned_at' => now(),
        ];
    }
}
