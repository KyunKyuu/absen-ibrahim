<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unique(['academic_year_id', 'name']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('school_class_id')->nullable()->after('student_user_id')->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->after('school_class_id')->constrained()->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
        });

        // Data lama minimal mendapatkan snapshot kelas/tahun ajaran saat migrasi.
        DB::table('attendances')->orderBy('id')->each(function ($attendance) {
            $profile = DB::table('student_profiles')->where('user_id', $attendance->student_user_id)->first();
            $schoolClass = $profile?->school_class_id
                ? DB::table('school_classes')->find($profile->school_class_id)
                : null;

            DB::table('attendances')->where('id', $attendance->id)->update([
                'school_class_id' => $profile?->school_class_id,
                'academic_year_id' => $schoolClass?->academic_year_id,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('semester_id');
            $table->dropConstrainedForeignId('academic_year_id');
            $table->dropConstrainedForeignId('school_class_id');
        });

        Schema::dropIfExists('semesters');
    }
};
