<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('billing_cycle', 20)->default('monthly');
            $table->unsignedBigInteger('default_amount')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_class_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->date('started_on');
            $table->date('ended_on')->nullable();
            $table->timestamps();
            $table->index(['student_user_id', 'ended_on']);
        });

        Schema::create('finance_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposed_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_class_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('billing_mode', 20);
            $table->unsignedBigInteger('amount');
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_fee_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('finance_proposal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->string('billing_period', 30)->nullable();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('unpaid');
            $table->boolean('is_arrears')->default(false);
            $table->timestamps();
            $table->unique(['finance_proposal_id', 'student_user_id']);
            $table->unique(['student_user_id', 'school_fee_type_id', 'billing_period'], 'student_fee_period_unique');
            $table->index(['student_user_id', 'status', 'is_arrears']);
        });

        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_bill_id')->constrained()->restrictOnDelete();
            $table->foreignId('received_by_user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->date('paid_on');
            $table->string('payment_method', 30)->default('cash');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_payments');
        Schema::dropIfExists('student_bills');
        Schema::dropIfExists('finance_proposals');
        Schema::dropIfExists('student_class_histories');
        Schema::dropIfExists('school_fee_types');
    }
};
