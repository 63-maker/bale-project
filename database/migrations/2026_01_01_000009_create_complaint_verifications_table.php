<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('management_id')->constrained('profiles')->cascadeOnDelete();
            $table->foreignUuid('admin_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('decision', 20); // approved, rejected
            $table->text('remarks');
            $table->timestamps();

            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_verifications');
    }
};
