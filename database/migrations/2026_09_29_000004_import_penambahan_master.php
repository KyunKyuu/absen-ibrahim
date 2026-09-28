<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $path = base_path('penambahan.md');
        if (! is_file($path)) {
            return;
        }

        $group = 'Penambahan poin';
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if (preg_match('/^[A-F]\s+(.+?)(?:\s+\+\d+)?$/u', $line, $heading)
                || preg_match('/^Akhlak\s*$/u', $line)) {
                $group = trim($heading[1] ?? $line);
                continue;
            }

            if (! preg_match('/^\d+\s+(.+?)\s+\+(\d+)$/u', $line, $row)) {
                continue;
            }

            $name = trim($row[1]);
            $points = (int) $row[2];
            $exists = DB::table('assessment_masters')
                ->where('kind', 'achievement')->where('name', $name)->first();
            if ($exists && (int) $exists->points !== $points) {
                $name .= ' ('.$points.' poin)';
            }

            DB::table('assessment_masters')->updateOrInsert(
                ['kind' => 'achievement', 'name' => $name],
                [
                    'group_name' => $group,
                    'points' => $points,
                    'sanction' => null,
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
        DB::table('assessment_masters')->where('kind', 'achievement')->delete();
    }
};
