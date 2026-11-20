<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ManagementLoginRequest;
use App\Http\Requests\StudentLoginRequest;
use App\Http\Requests\StudentRegistrationRequest;
use App\Models\Department;
use App\Services\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuthenticationService $authService
    ) {}

    /**
     * Show unified enterprise login screen
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return match (Auth::user()->role) {
                'student' => redirect()->route('student.dashboard'),
                'management' => redirect()->route('management.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
                default => redirect('/'),
            };
        }

        $activeTab = $request->query('role', 'student');
        if (! in_array($activeTab, ['student', 'management', 'admin'], true)) {
            $activeTab = 'student';
        }

        return view('auth.login', compact('activeTab'));
    }

    /**
     * Handle student login with rate limiting
     */
    public function loginStudent(StudentLoginRequest $request): RedirectResponse
    {
        $throttleKey = 'login-student:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'register_number' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $authenticated = $this->authService->attemptStudentLogin(
            $request->input('register_number'),
            $request->input('password'),
            $request->boolean('remember')
        );

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'register_number' => 'Invalid register number or password.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        return redirect()->intended(route('student.dashboard'));
    }

    /**
     * Handle management login with rate limiting
     */
    public function loginManagement(ManagementLoginRequest $request): RedirectResponse
    {
        $throttleKey = 'login-management:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'identifier' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $authenticated = $this->authService->attemptManagementLogin(
            $request->input('identifier'),
            $request->input('password'),
            $request->boolean('remember')
        );

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'identifier' => 'Invalid Employee ID/email or password.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        if (Auth::user()->must_change_password) {
            return redirect()->route('password.change')
                ->with('info', 'Please change your temporary password to continue.');
        }

        return redirect()->intended(route('management.dashboard'));
    }

    /**
     * Handle admin login with rate limiting
     */
    public function loginAdmin(AdminLoginRequest $request): RedirectResponse
    {
        $throttleKey = 'login-admin:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'identifier' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $authenticated = $this->authService->attemptAdminLogin(
            $request->input('identifier'),
            $request->input('password'),
            $request->boolean('remember')
        );

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'identifier' => 'Invalid Administrator credentials.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Show student registration form
     */
    public function showRegister(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('auth.register', compact('departments'));
    }

    /**
     * Process student registration
     */
    public function registerStudent(StudentRegistrationRequest $request): RedirectResponse
    {
        $student = $this->authService->registerStudent($request->validated());

        Auth::login($student);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')
            ->with('success', 'Registration successful! Welcome to the Complaint Management System.');
    }

    /**
     * Logout
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login')->with('info', 'You have been successfully logged out.');
    }

    /**
     * Show profile
     */
    public function showProfile(): View
    {
        $user = Auth::user()->load('department');

        return view('auth.profile', compact('user'));
    }

    /**
     * Update profile details
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
        ]);

        $this->authService->updateProfile(Auth::user(), $validated);

        return back()->with('success', 'Profile details updated successfully.');
    }

    /**
     * Show password change form
     */
    public function showChangePassword(): View
    {
        return view('auth.change_password');
    }

    /**
     * Update password
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $this->authService->changePassword(
            Auth::user(),
            $request->input('current_password'),
            $request->input('new_password')
        );

        $role = Auth::user()->role;
        $dashboardRoute = match ($role) {
            'student' => 'student.dashboard',
            'management' => 'management.dashboard',
            'admin' => 'admin.dashboard',
            default => 'login',
        };

        return redirect()->route($dashboardRoute)->with('success', 'Your password has been changed successfully.');
    }
}
