<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->unsignedInteger('max_location_accuracy_meters')->default(100)->after('attendance_radius_meters');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedInteger('location_accuracy_meters')->nullable()->after('distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('location_accuracy_meters');
        });

        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn('max_location_accuracy_meters');
        });
    }
};
