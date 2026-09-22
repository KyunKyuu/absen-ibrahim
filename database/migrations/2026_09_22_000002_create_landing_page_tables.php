<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->default('Sekolah Islam Terpadu');
            $table->string('headline')->default('Tumbuh dalam iman, ilmu, dan adab.');
            $table->text('intro')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->string('primary_cta_label')->default('Informasi Pendaftaran');
            $table->string('primary_cta_url')->default('#pendaftaran');
            $table->string('secondary_cta_label')->default('Jelajahi Program');
            $table->string('secondary_cta_url')->default('#program');
            $table->string('about_title')->default('Pendidikan yang dekat dengan kehidupan');
            $table->text('about_body')->nullable();
            $table->string('admission_title')->default('Mari bertumbuh bersama kami');
            $table->text('admission_body')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('instagram_url')->nullable();
            $table->timestamps();
        });

        Schema::create('landing_items', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 30)->index();
            $table->string('title');
            $table->string('kicker')->nullable();
            $table->text('body')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_items');
        Schema::dropIfExists('landing_pages');
    }
};
