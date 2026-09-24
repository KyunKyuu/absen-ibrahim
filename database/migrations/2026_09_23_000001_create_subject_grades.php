<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->string('kind', 20);
            $table->string('title', 150);
            $table->date('assessed_on');
            $table->timestamps();
        });
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->restrictOnDelete();
            $table->decimal('score', 5, 2);
            $table->string('notes', 1000)->nullable();
            $table->timestamps();
            $table->unique(['grade_assessment_id', 'student_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('grade_assessments');
    }
};
