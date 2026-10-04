<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();

            $table->string('name', 120);
            $table->string('relationship', 80)->nullable();
            $table->string('phone', 40);
            $table->string('alternate_phone', 40)->nullable();
            $table->string('email', 180)->nullable();
            $table->text('address')->nullable();

            // Priority: 1 = first to call
            $table->unsignedTinyInteger('priority')->default(1);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['child_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};