<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->foreignId('submitted_by_user_id')->nullable()->after('student_bill_id')->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by_user_id')->nullable()->after('received_by_user_id')->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('verified')->after('amount');
            $table->string('proof_path')->nullable()->after('reference');
            $table->text('review_notes')->nullable()->after('notes');
            $table->timestamp('reviewed_at')->nullable()->after('review_notes');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropConstrainedForeignId('submitted_by_user_id');
            $table->dropConstrainedForeignId('reviewed_by_user_id');
            $table->dropColumn(['status', 'proof_path', 'review_notes', 'reviewed_at']);
        });
    }
};
