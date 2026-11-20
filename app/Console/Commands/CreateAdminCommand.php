<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'ccms:create-admin
                            {--name= : Full Name of the Administrator}
                            {--email= : Official Email of the Administrator}
                            {--password= : Password for the Administrator account}';

    protected $description = 'Provision the initial Admin account for College Complaint Management System';

    public function handle(): int
    {
        $this->info('=== CCMS Initial Admin Provisioning ===');

        $name = $this->option('name') ?: $this->ask('Administrator Full Name');
        $email = $this->option('email') ?: $this->ask('Administrator Email Address');
        $password = $this->option('password') ?: $this->secret('Administrator Password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $adminDept = Department::where('code', 'ADMIN')->first();

        $admin = Profile::create([
            'full_name' => $name,
            'email' => $email,
            'password' => $password, // mutator hashes to password_hash
            'role' => Profile::ROLE_ADMIN,
            'department_id' => $adminDept?->id,
            'account_status' => Profile::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $this->info("Administrator account created successfully with ID: {$admin->id}");
        $this->info("Email: {$admin->email}");
        $this->info("Role: {$admin->role}");

        return self::SUCCESS;
    }
}
