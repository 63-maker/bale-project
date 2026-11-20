<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('uploaded_by')->constrained('profiles')->cascadeOnDelete();
            $table->string('storage_path', 500);
            $table->string('original_filename', 255);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');
            $table->timestamps();

            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_attachments');
    }
};
