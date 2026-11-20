<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'complaint_number' => sprintf('CCMS-2026-%06d', fake()->unique()->numberBetween(1, 99999)),
            'student_id' => Profile::factory()->student(),
            'category_id' => ComplaintCategory::factory(),
            'department_id' => Department::factory(),
            'subject' => fake()->sentence(5),
            'description' => fake()->paragraph(3),
            'priority' => Complaint::PRIORITY_MEDIUM,
            'status' => Complaint::STATUS_SUBMITTED,
        ];
    }
}
