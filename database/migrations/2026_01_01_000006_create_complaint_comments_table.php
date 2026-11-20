<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('sender_id')->constrained('profiles')->cascadeOnDelete();
            $table->text('message');
            $table->string('visibility', 20)->default('public'); // public, internal
            $table->timestamps();

            $table->index(['complaint_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_comments');
    }
};
