<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->time('attendance_open_time')->default('05:00:00')->after('attendance_radius_meters');
            $table->time('attendance_close_time')->default('10:00:00')->after('late_after');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['attendance_open_time', 'attendance_close_time']);
        });
    }
};
