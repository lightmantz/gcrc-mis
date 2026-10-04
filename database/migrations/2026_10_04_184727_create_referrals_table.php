<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // The child being referred to the center
            $table->foreignId('child_id')->nullable()->constrained()->nullOnDelete();

            // Referral source
            $table->enum('source_type', [
                'walk_in', 'hospital', 'clinic', 'school',
                'community', 'self', 'other',
            ])->default('walk_in');
            $table->string('source_name', 200)->nullable();     // person or organization
            $table->string('source_contact', 200)->nullable();  // phone or email

            $table->date('referral_date');
            $table->text('reason')->nullable();

            // Status: for now just "received"; Module 14 expands
            $table->enum('status', [
                'received', 'accepted', 'in_progress', 'completed', 'declined',
            ])->default('received');

            // Sensitive: internal notes
            $table->text('notes')->nullable();

            // Who registered the referral in the system
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('referral_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};