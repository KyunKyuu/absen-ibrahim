<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessment_masters', function (Blueprint $table) {
            $table->string('group_name')->nullable()->after('kind');
            $table->text('sanction')->nullable()->after('points');
        });

        $path = base_path('pelanggaran.md');
        if (! is_file($path)) {
            return;
        }

        $group = null;
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if (preg_match('/^[IVX]+\.\s+(.+)$/u', $line, $heading)) {
                $group = trim($heading[1]);
                continue;
            }

            if (! preg_match('/^\d+\s+(.+?)\s+(-\d+)\s+(.+)$/u', $line, $row)) {
                continue;
            }

            DB::table('assessment_masters')->updateOrInsert(
                ['kind' => 'violation', 'name' => trim($row[1])],
                [
                    'group_name' => $group,
                    'points' => (int) $row[2],
                    'sanction' => trim($row[3]),
                    'score' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        Schema::table('assessment_masters', function (Blueprint $table) {
            $table->dropColumn(['group_name', 'sanction']);
        });
    }
};
