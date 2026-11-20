<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('management_id')->constrained('profiles')->cascadeOnDelete();
            $table->foreignUuid('assigned_by')->constrained('profiles')->cascadeOnDelete();
            $table->string('assignment_method', 20)->default('manual'); // manual, automatic
            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();
            $table->timestamps();

            $table->index(['management_id', 'is_active']);
            $table->index(['complaint_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_assignments');
    }
};
