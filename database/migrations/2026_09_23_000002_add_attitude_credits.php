<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attitude_assessments', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('credit_cost')->default(1)->after('score');
            $table->index(['teacher_user_id', 'school_class_id', 'subject_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::table('attitude_assessments', function (Blueprint $table) {
            $table->dropIndex(['teacher_user_id', 'school_class_id', 'subject_id', 'semester_id']);
            $table->dropConstrainedForeignId('semester_id');
            $table->dropColumn('credit_cost');
        });
    }
};
