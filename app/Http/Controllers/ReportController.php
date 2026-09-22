<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function attendanceCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Tanggal', 'Nama', 'Kelas', 'Status', 'Jam', 'Sumber', 'Jarak Meter', 'Tepat Waktu']);

            Attendance::query()->with('student.studentProfile.schoolClass')->orderByDesc('attendance_date')->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $attendance) {
                    fputcsv($handle, [
                        $attendance->attendance_date?->format('Y-m-d'),
                        $this->csvSafe($attendance->student?->name),
                        $this->csvSafe($attendance->student?->studentProfile?->schoolClass?->name),
                        $this->csvSafe($attendance->status),
                        $attendance->checked_in_at,
                        $this->csvSafe($attendance->source),
                        $attendance->distance_meters,
                        $attendance->is_ontime ? 'Ya' : 'Tidak',
                    ]);
                }
            });
        }, 'laporan-absensi.csv', ['Content-Type' => 'text/csv']);
    }

    public function attitudeCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Nama', 'Kelas', 'Point General', 'Point Sikap', 'Point Absen', 'Point Prestasi', 'Label']);

            User::query()->where('role', 'student')->with(['studentProfile.schoolClass', 'pointSummary'])->orderBy('name')->chunk(200, function ($students) use ($handle) {
                foreach ($students as $student) {
                    $summary = $student->pointSummary;
                    fputcsv($handle, [
                        $this->csvSafe($student->name),
                        $this->csvSafe($student->studentProfile?->schoolClass?->name),
                        $summary?->general_points ?? 0,
                        $summary?->attitude_points ?? 0,
                        $summary?->attendance_points ?? 0,
                        $summary?->achievement_points ?? 0,
                        $this->csvSafe($summary?->label ?? 'Perlu Dipantau'),
                    ]);
                }
            });
        }, 'raport-sikap.csv', ['Content-Type' => 'text/csv']);
    }

    private function csvSafe(?string $value): ?string
    {
        if ($value !== null && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'".$value;
        }

        return $value;
    }
}
