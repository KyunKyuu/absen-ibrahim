<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('video_url', 2048)->nullable();
            $table->string('video_title')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_title', 'vision', 'mission']);
        });
    }
};
