<?php

namespace App\Services;

use App\Models\Profile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    public function __construct(
        protected AuditService $auditService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Register a new student account
     */
    public function registerStudent(array $data): Profile
    {
        $student = Profile::create([
            'full_name' => $data['full_name'],
            'register_number' => strtoupper(trim($data['register_number'])),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'role' => Profile::ROLE_STUDENT,
            'department_id' => $data['department_id'],
            'study_year' => $data['study_year'],
            'account_status' => Profile::STATUS_ACTIVE,
            'must_change_password' => false,
            'email_verified_at' => now(), // Can be configured with verification flow
        ]);

        $this->auditService->log(
            action: 'STUDENT_REGISTERED',
            entityType: 'Profile',
            entityId: $student->id,
            details: ['register_number' => $student->register_number, 'email' => $student->email],
            actor: $student
        );

        event(new Registered($student));

        return $student;
    }

    /**
     * Authenticate student using register number and password
     */
    public function attemptStudentLogin(string $registerNumber, string $password, bool $remember = false): bool
    {
        $student = Profile::where('register_number', strtoupper(trim($registerNumber)))
            ->where('role', Profile::ROLE_STUDENT)
            ->first();

        if (! $student || ! Hash::check($password, $student->password_hash)) {
            return false;
        }

        if ($student->isSuspended()) {
            throw ValidationException::withMessages([
                'register_number' => 'Your student account has been suspended. Please contact the administrator.',
            ]);
        }

        Auth::login($student, $remember);
        request()->session()->regenerate();

        $this->auditService->log('STUDENT_LOGIN', 'Profile', $student->id, null, $student);

        return true;
    }

    /**
     * Authenticate management using employee ID or email and password
     */
    public function attemptManagementLogin(string $identifier, string $password, bool $remember = false): bool
    {
        $identifier = trim($identifier);

        $management = Profile::where('role', Profile::ROLE_MANAGEMENT)
            ->where(function ($query) use ($identifier) {
                $query->where('employee_id', strtoupper($identifier))
                    ->orWhere('email', strtolower($identifier));
            })
            ->first();

        if (! $management || ! Hash::check($password, $management->password_hash)) {
            return false;
        }

        if ($management->isSuspended()) {
            throw ValidationException::withMessages([
                'identifier' => 'Your management account has been suspended. Please contact the administrator.',
            ]);
        }

        Auth::login($management, $remember);
        request()->session()->regenerate();

        $this->auditService->log('MANAGEMENT_LOGIN', 'Profile', $management->id, null, $management);

        return true;
    }

    /**
     * Authenticate admin using email or employee identifier and password
     */
    public function attemptAdminLogin(string $identifier, string $password, bool $remember = false): bool
    {
        $identifier = trim($identifier);

        $admin = Profile::where('role', Profile::ROLE_ADMIN)
            ->where(function ($query) use ($identifier) {
                $query->where('email', strtolower($identifier))
                    ->orWhere('employee_id', strtoupper($identifier));
            })
            ->first();

        if (! $admin || ! Hash::check($password, $admin->password_hash)) {
            return false;
        }

        if ($admin->isSuspended()) {
            throw ValidationException::withMessages([
                'identifier' => 'Your administrator account has been suspended.',
            ]);
        }

        Auth::login($admin, $remember);
        request()->session()->regenerate();

        $this->auditService->log('ADMIN_LOGIN', 'Profile', $admin->id, null, $admin);

        return true;
    }

    /**
     * Admin creates a management account
     */
    public function createManagementAccount(array $data, Profile $admin): Profile
    {
        $management = Profile::create([
            'full_name' => $data['full_name'],
            'employee_id' => strtoupper(trim($data['employee_id'])),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'role' => Profile::ROLE_MANAGEMENT,
            'department_id' => $data['department_id'],
            'account_status' => $data['account_status'] ?? Profile::STATUS_ACTIVE,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        $this->auditService->log(
            action: 'MANAGEMENT_ACCOUNT_CREATED',
            entityType: 'Profile',
            entityId: $management->id,
            details: [
                'employee_id' => $management->employee_id,
                'email' => $management->email,
                'department_id' => $management->department_id,
            ],
            actor: $admin
        );

        $this->notificationService->notify(
            $management,
            'Management Account Created',
            'Your CCMS Management account has been created by the Administrator. Please update your password.'
        );

        return $management;
    }

    /**
     * Update permitted profile fields
     */
    public function updateProfile(Profile $user, array $data): bool
    {
        $fillable = ['full_name'];

        if ($user->isAdmin()) {
            $fillable[] = 'email';
        }

        $user->fill(array_intersect_key($data, array_flip($fillable)));
        $saved = $user->save();

        if ($saved) {
            $this->auditService->log('PROFILE_UPDATED', 'Profile', $user->id, null, $user);
        }

        return $saved;
    }

    /**
     * Change user password
     */
    public function changePassword(Profile $user, string $currentPassword, string $newPassword): bool
    {
        if (! Hash::check($currentPassword, $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => 'The provided current password does not match our records.',
            ]);
        }

        $user->password = $newPassword;
        $user->must_change_password = false;
        $user->save();

        $this->auditService->log('PASSWORD_CHANGED', 'Profile', $user->id, null, $user);

        return true;
    }

    /**
     * Log user out and invalidate session
     */
    public function logout(): void
    {
        if ($user = Auth::user()) {
            $this->auditService->log('LOGOUT', 'Profile', $user->id, null, $user);
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
