<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Polymorphic: a document can attach to a child, staff, referral, etc.
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');

            $table->string('original_name');         // filename as uploaded
            $table->string('stored_path');           // path on the disk
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size_bytes');

            $table->string('title')->nullable();     // human-friendly label
            $table->enum('category', [
                'referral_letter', 'medical_report', 'assessment_report',
                'consent_form', 'discharge_document', 'identification',
                'photo', 'other',
            ])->default('other');
            $table->text('description')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};