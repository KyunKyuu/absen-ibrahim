<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->json('criteria')->nullable();
            $table->timestamps();
        });

        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->timestamp('awarded_at');
            $table->timestamps();
            $table->unique(['student_user_id', 'achievement_id']);
        });

        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier')->unique();
            $table->string('api_token_hash');
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('iot_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iot_device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_identifier');
            $table->string('fingerprint_user_id')->nullable();
            $table->foreignId('student_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at');
            $table->string('status', 20)->default('received');
            $table->json('payload')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_attendance_logs');
        Schema::dropIfExists('iot_devices');
        Schema::dropIfExists('student_achievements');
        Schema::dropIfExists('achievements');
    }
};
