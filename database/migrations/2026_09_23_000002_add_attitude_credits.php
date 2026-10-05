<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attitude_assessments', 'semester_id')) {
            Schema::table('attitude_assessments', function (Blueprint $table) {
                $table->foreignId('semester_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('attitude_assessments', 'credit_cost')) {
            Schema::table('attitude_assessments', function (Blueprint $table) {
                $table->unsignedSmallInteger('credit_cost')->default(1)->after('score');
            });
        }

        if (! Schema::hasIndex('attitude_assessments', 'att_assess_teacher_class_subject_semester_idx')) {
            Schema::table('attitude_assessments', function (Blueprint $table) {
                $table->index(
                    ['teacher_user_id', 'school_class_id', 'subject_id', 'semester_id'],
                    'att_assess_teacher_class_subject_semester_idx',
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('attitude_assessments', 'att_assess_teacher_class_subject_semester_idx')) {
            Schema::table('attitude_assessments', function (Blueprint $table) {
                $table->dropIndex('att_assess_teacher_class_subject_semester_idx');
            });
        }

        if (Schema::hasColumn('attitude_assessments', 'semester_id')) {
            Schema::table('attitude_assessments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('semester_id');
            });
        }

        if (Schema::hasColumn('attitude_assessments', 'credit_cost')) {
            Schema::table('attitude_assessments', function (Blueprint $table) {
                $table->dropColumn('credit_cost');
            });
        }
    }
};
