<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_successfully(): void
    {
        $dept = Department::factory()->create();

        $response = $this->post('/register', [
            'full_name' => 'John Doe',
            'register_number' => '2026CSE042',
            'email' => 'john.doe@college.edu',
            'department_id' => $dept->id,
            'study_year' => 2,
            'password' => 'SecurePass@123',
            'password_confirmation' => 'SecurePass@123',
            'role' => 'admin', // Malicious attempt to self-assign admin
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('profiles', [
            'register_number' => '2026CSE042',
            'email' => 'john.doe@college.edu',
            'role' => Profile::ROLE_STUDENT, // Role must strictly be student
            'account_status' => Profile::STATUS_ACTIVE,
        ]);
    }

    public function test_student_registration_fails_with_duplicate_register_number(): void
    {
        $dept = Department::factory()->create();
        Profile::factory()->student()->create([
            'register_number' => '2026CSE001',
            'department_id' => $dept->id,
        ]);

        $response = $this->from('/register')->post('/register', [
            'full_name' => 'Another Student',
            'register_number' => '2026CSE001',
            'email' => 'another@college.edu',
            'department_id' => $dept->id,
            'study_year' => 1,
            'password' => 'SecurePass@123',
            'password_confirmation' => 'SecurePass@123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('register_number');
    }

    public function test_student_registration_fails_with_duplicate_email(): void
    {
        $dept = Department::factory()->create();
        Profile::factory()->student()->create([
            'email' => 'existing@college.edu',
            'department_id' => $dept->id,
        ]);

        $response = $this->from('/register')->post('/register', [
            'full_name' => 'Another Student',
            'register_number' => '2026CSE999',
            'email' => 'existing@college.edu',
            'department_id' => $dept->id,
            'study_year' => 1,
            'password' => 'SecurePass@123',
            'password_confirmation' => 'SecurePass@123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
    }

    public function test_student_can_login_with_register_number(): void
    {
        $student = Profile::factory()->student()->create([
            'register_number' => '2026IT055',
            'password' => 'MySecretPass@123',
        ]);

        $response = $this->post('/login/student', [
            'register_number' => '2026IT055',
            'password' => 'MySecretPass@123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_management_can_login_with_employee_id(): void
    {
        $management = Profile::factory()->management()->create([
            'employee_id' => 'MGT9001',
            'password' => 'Management@123',
        ]);

        $response = $this->post('/login/management', [
            'identifier' => 'MGT9001',
            'password' => 'Management@123',
        ]);

        $response->assertRedirect(route('management.dashboard'));
        $this->assertAuthenticatedAs($management);
    }

    public function test_management_can_login_with_email(): void
    {
        $management = Profile::factory()->management()->create([
            'email' => 'officer@college.edu',
            'password' => 'Management@123',
        ]);

        $response = $this->post('/login/management', [
            'identifier' => 'officer@college.edu',
            'password' => 'Management@123',
        ]);

        $response->assertRedirect(route('management.dashboard'));
        $this->assertAuthenticatedAs($management);
    }

    public function test_admin_can_login_with_email(): void
    {
        $admin = Profile::factory()->admin()->create([
            'email' => 'superadmin@college.edu',
            'password' => 'AdminMaster@123',
        ]);

        $response = $this->post('/login/admin', [
            'identifier' => 'superadmin@college.edu',
            'password' => 'AdminMaster@123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_suspended_account_cannot_login(): void
    {
        Profile::factory()->student()->suspended()->create([
            'register_number' => '2026SUSPENDED',
            'password' => 'Password@123',
        ]);

        $response = $this->post('/login/student', [
            'register_number' => '2026SUSPENDED',
            'password' => 'Password@123',
        ]);

        $response->assertSessionHasErrors('register_number');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $student = Profile::factory()->student()->create();

        $response = $this->actingAs($student)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_change_password(): void
    {
        $student = Profile::factory()->student()->create([
            'password' => 'OldPassword@123',
        ]);

        $response = $this->actingAs($student)->put('/change-password', [
            'current_password' => 'OldPassword@123',
            'new_password' => 'NewSecurePassword@123',
            'new_password_confirmation' => 'NewSecurePassword@123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword@123', $student->password_hash));
    }
}
