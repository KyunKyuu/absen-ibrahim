<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('gender');
        });

        Schema::table('school_settings', function (Blueprint $table) {
            $table->decimal('latitude_2', 10, 7)->nullable()->after('longitude');
            $table->decimal('longitude_2', 10, 7)->nullable()->after('latitude_2');
            $table->unsignedInteger('attendance_radius_meters_2')->nullable()->after('attendance_radius_meters');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', fn (Blueprint $table) => $table->dropColumn('photo_path'));
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['latitude_2', 'longitude_2', 'attendance_radius_meters_2']);
        });
    }
};
