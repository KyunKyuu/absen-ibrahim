<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('Sekolah');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('attendance_radius_meters')->default(100);
            $table->time('start_time')->default('07:00:00');
            $table->time('late_after')->default('07:00:00');
            $table->json('point_rules')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('attendance_date');
            $table->time('checked_in_at')->nullable();
            $table->string('status', 20)->default('present');
            $table->string('source', 20)->default('web');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->boolean('is_within_radius')->default(false);
            $table->boolean('is_ontime')->default(false);
            $table->string('device_identifier')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_user_id', 'attendance_date']);
        });

        Schema::create('attitude_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('aspect');
            $table->unsignedTinyInteger('score');
            $table->integer('points');
            $table->text('notes')->nullable();
            $table->date('assessed_on');
            $table->timestamps();
        });

        Schema::create('achievement_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('achievement');
            $table->integer('points');
            $table->text('notes')->nullable();
            $table->date('awarded_on');
            $table->timestamps();
        });

        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20);
            $table->integer('points');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description');
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('student_point_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->integer('general_points')->default(0);
            $table->integer('attitude_points')->default(0);
            $table->integer('attendance_points')->default(0);
            $table->integer('achievement_points')->default(0);
            $table->string('label')->default('Perlu Dipantau');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_point_summaries');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('achievement_assessments');
        Schema::dropIfExists('attitude_assessments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('school_settings');
    }
};
