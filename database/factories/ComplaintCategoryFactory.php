<?php

namespace Database\Factories;

use App\Models\ComplaintCategory;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintCategoryFactory extends Factory
{
    protected $model = ComplaintCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' Category',
            'description' => fake()->sentence(),
            'department_id' => Department::factory(),
            'is_active' => true,
        ];
    }
}
