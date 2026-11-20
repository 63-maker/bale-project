<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'Password@123',
            'role' => Profile::ROLE_STUDENT,
            'department_id' => Department::factory(),
            'study_year' => 2,
            'register_number' => strtoupper(fake()->unique()->bothify('2026###???')),
            'account_status' => Profile::STATUS_ACTIVE,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ];
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Profile::ROLE_STUDENT,
            'register_number' => strtoupper(fake()->unique()->bothify('2026###???')),
            'study_year' => fake()->numberBetween(1, 4),
        ]);
    }

    public function management(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Profile::ROLE_MANAGEMENT,
            'employee_id' => strtoupper(fake()->unique()->bothify('EMP####')),
            'register_number' => null,
            'study_year' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Profile::ROLE_ADMIN,
            'register_number' => null,
            'study_year' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_status' => Profile::STATUS_SUSPENDED,
        ]);
    }
}
