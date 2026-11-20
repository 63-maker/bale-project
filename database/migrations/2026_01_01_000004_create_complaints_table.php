<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('complaint_number', 50)->unique();
            $table->foreignUuid('student_id')->constrained('profiles')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('complaint_categories')->restrictOnDelete();
            $table->foreignUuid('department_id')->constrained('departments')->restrictOnDelete();
            $table->string('subject', 255);
            $table->text('description');
            $table->string('priority', 20)->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('status', 30)->default('SUBMITTED');
            // Allowed: SUBMITTED, UNDER_REVIEW, ASSIGNED, IN_PROGRESS, RESOLUTION_SUBMITTED, VERIFICATION_REJECTED, VERIFIED_FOR_CLOSURE, CLOSED, REOPENED, REJECTED

            $table->text('resolution_description')->nullable();
            $table->timestamp('resolution_submitted_at')->nullable();

            $table->foreignUuid('verified_by')->nullable()->constrained('profiles')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_remarks')->nullable();

            $table->foreignUuid('closed_by')->nullable()->constrained('profiles')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();

            $table->text('rejection_reason')->nullable();
            $table->text('reopened_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['student_id', 'status']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
