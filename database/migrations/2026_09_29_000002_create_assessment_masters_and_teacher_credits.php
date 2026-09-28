<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessment_masters', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 30);
            $table->string('name', 100);
            $table->integer('points')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['kind', 'name']);
        });

        Schema::create('teacher_attitude_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('credits')->default(100);
            $table->timestamps();
            $table->unique('teacher_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attitude_credits');
        Schema::dropIfExists('assessment_masters');
    }
};
