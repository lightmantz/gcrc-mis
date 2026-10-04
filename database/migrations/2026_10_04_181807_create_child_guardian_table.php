<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_guardian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();

            $table->enum('relationship', [
                'mother', 'father', 'grandmother', 'grandfather',
                'aunt', 'uncle', 'sibling', 'legal_guardian',
                'foster_parent', 'other',
            ]);
            $table->string('relationship_other', 80)->nullable();

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_legal')->default(false);
            $table->boolean('consent_medical')->default(false);
            $table->boolean('consent_education')->default(false);
            $table->boolean('consent_photography')->default(false);
            $table->boolean('lives_with_child')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['child_id', 'guardian_id']);
            $table->index(['child_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_guardian');
    }
};