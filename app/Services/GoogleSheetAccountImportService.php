<?php

namespace App\Services;

use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\StudentClassHistory;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoogleSheetAccountImportService
{
    public function import(string $sheetUrl, Role $role): array
    {
        try {
            $csv = Http::timeout(20)->retry(2, 250)->get($this->exportUrl($sheetUrl));
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'sheet_url' => 'Google Sheet tidak dapat dihubungi. Coba kembali beberapa saat lagi.',
            ]);
        }

        if (! $csv->successful()) {
            throw ValidationException::withMessages([
                'sheet_url' => 'Google Sheet tidak dapat dibaca. Pastikan link dapat diakses oleh siapa pun yang memiliki link.',
            ]);
        }

        if (strlen($csv->body()) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages(['sheet_url' => 'Google Sheet terlalu besar. Batas file adalah 5 MB.']);
        }

        return $this->importRows($this->parseCsv($csv->body()), $role);
    }

    public function importFile(UploadedFile $file, Role $role): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = match ($extension) {
            'csv', 'txt' => $this->parseCsv((string) file_get_contents($file->getRealPath())),
            'xlsx' => $this->parseXlsx($file->getRealPath()),
            default => throw ValidationException::withMessages(['spreadsheet' => 'Gunakan file CSV atau XLSX.']),
        };

        return $this->importRows($rows, $role);
    }

    private function importRows(array $rows, Role $role): array
    {
        $created = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                DB::transaction(fn () => $this->createAccount($row, $role));
                $created++;
            } catch (\Throwable $exception) {
                $message = $exception::class === \RuntimeException::class
                    ? $exception->getMessage()
                    : 'Data tidak valid atau duplikat.';
                $errors[] = 'Baris '.($index + 2).': '.$message;
            }
        }

        return ['created' => $created, 'skipped' => count($errors), 'errors' => $errors];
    }

    private function parseXlsx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages(['spreadsheet' => 'File XLSX tidak dapat dibuka.']);
        }

        $sharedStrings = [];
        if (($sharedXml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($sharedXml);
            foreach ($xml?->xpath('//*[local-name()="si"]') ?: [] as $item) {
                $parts = $item->xpath('.//*[local-name()="t"]') ?: [];
                $sharedStrings[] = implode('', array_map(fn ($part) => (string) $part, $parts));
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) {
            throw ValidationException::withMessages(['spreadsheet' => 'Sheet pertama tidak ditemukan dalam file XLSX.']);
        }

        $sheet = simplexml_load_string($sheetXml);
        $matrix = [];
        foreach ($sheet?->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [] as $row) {
            $values = [];
            foreach ($row->xpath('./*[local-name()="c"]') ?: [] as $cell) {
                preg_match('/^([A-Z]+)/', (string) $cell['r'], $column);
                $index = $this->columnIndex($column[1] ?? 'A');
                $type = (string) $cell['t'];
                $raw = (string) (($cell->xpath('./*[local-name()="v"]')[0] ?? null));
                if ($type === 's') {
                    $value = $sharedStrings[(int) $raw] ?? '';
                } elseif ($type === 'inlineStr') {
                    $parts = $cell->xpath('.//*[local-name()="t"]') ?: [];
                    $value = implode('', array_map(fn ($part) => (string) $part, $parts));
                } else {
                    $value = $raw;
                }
                $values[$index] = $value;
            }
            if ($values !== []) {
                $matrix[] = array_replace(array_fill(0, max(array_keys($values)) + 1, ''), $values);
            }
        }

        if ($matrix === []) {
            throw ValidationException::withMessages(['spreadsheet' => 'File XLSX tidak memiliki data pada sheet pertama.']);
        }

        $stream = fopen('php://temp', 'r+');
        foreach ($matrix as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $this->parseCsv($csv);
    }

    private function columnIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function exportUrl(string $sheetUrl): string
    {
        $parts = parse_url($sheetUrl);

        if (($parts['scheme'] ?? null) !== 'https' || ($parts['host'] ?? null) !== 'docs.google.com'
            || ! preg_match('#/spreadsheets/d/([A-Za-z0-9_-]+)#', $parts['path'] ?? '', $matches)) {
            throw ValidationException::withMessages([
                'sheet_url' => 'Gunakan link Google Sheet HTTPS dari docs.google.com.',
            ]);
        }

        parse_str($parts['query'] ?? '', $query);
        parse_str($parts['fragment'] ?? '', $fragment);
        $gid = filter_var($query['gid'] ?? $fragment['gid'] ?? 0, FILTER_VALIDATE_INT);

        return 'https://docs.google.com/spreadsheets/d/'.$matches[1].'/export?format=csv&gid='.($gid === false ? 0 : $gid);
    }

    private function parseCsv(string $csv): array
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $csv);
        rewind($stream);
        $headers = fgetcsv($stream);

        if (! $headers) {
            throw ValidationException::withMessages(['sheet_url' => 'Google Sheet tidak memiliki header.']);
        }

        $headers = array_map(fn ($header) => Str::of((string) $header)->trim()->lower()->ascii()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString(), $headers);

        if (! in_array('nama', $headers, true) && ! in_array('name', $headers, true)) {
            throw ValidationException::withMessages(['sheet_url' => 'Header nama/name tidak ditemukan. Pastikan Sheet dapat dibaca melalui link.']);
        }

        $rows = [];

        while (($values = fgetcsv($stream)) !== false) {
            if (count(array_filter($values, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $values = array_pad($values, count($headers), null);
            $rows[] = array_combine($headers, array_slice($values, 0, count($headers)));

            if (count($rows) > 1000) {
                throw ValidationException::withMessages(['sheet_url' => 'Maksimal 1.000 akun dalam satu proses import.']);
            }
        }

        fclose($stream);

        if ($rows === []) {
            throw ValidationException::withMessages(['sheet_url' => 'Google Sheet tidak memiliki baris akun.']);
        }

        return $rows;
    }

    private function createAccount(array $row, Role $role): User
    {
        $name = trim((string) ($row['nama'] ?? $row['name'] ?? ''));

        if ($name === '') {
            throw new \RuntimeException('Kolom nama wajib diisi.');
        }

        $providedUsername = trim((string) ($row['username'] ?? ''));
        $username = $this->uniqueUsername($providedUsername !== '' ? $providedUsername : $name);
        $email = trim((string) ($row['email'] ?? '')) ?: null;

        if ($email && (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::query()->where('email', $email)->exists())) {
            throw new \RuntimeException('Email tidak valid atau sudah digunakan.');
        }

        $user = User::query()->create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'role' => $role->name,
            'password' => Hash::make(config('auth.import_default_password')),
            'must_change_password' => true,
        ]);
        $user->roles()->attach($role->id);

        $this->createProfile($user, $role->name, $row);

        return $user;
    }

    private function createProfile(User $user, string $role, array $row): void
    {
        if ($role === 'student') {
            $nis = trim((string) ($row['nis'] ?? ''));
            $className = trim((string) ($row['kelas'] ?? $row['class'] ?? ''));
            $schoolClass = SchoolClass::query()->where('name', $className)->first();

            if ($nis === '' || StudentProfile::query()->where('nis', $nis)->exists()) {
                throw new \RuntimeException('NIS wajib unik untuk akun siswa.');
            }
            if (! $schoolClass) {
                throw new \RuntimeException("Kelas '{$className}' tidak ditemukan.");
            }

            StudentProfile::query()->create(['user_id' => $user->id, 'school_class_id' => $schoolClass->id, 'nis' => $nis]);
            StudentClassHistory::query()->create([
                'student_user_id' => $user->id,
                'school_class_id' => $schoolClass->id,
                'academic_year_id' => $schoolClass->academic_year_id,
                'started_on' => today(),
            ]);
        } elseif ($role === 'teacher') {
            $employeeNumber = trim((string) ($row['nip'] ?? $row['employee_number'] ?? '')) ?: null;

            if ($employeeNumber && TeacherProfile::query()->where('employee_number', $employeeNumber)->exists()) {
                throw new \RuntimeException('NIP sudah digunakan.');
            }

            TeacherProfile::query()->create(['user_id' => $user->id, 'employee_number' => $employeeNumber]);
        } elseif ($role === 'parent') {
            ParentProfile::query()->create(['user_id' => $user->id, 'phone' => trim((string) ($row['phone'] ?? $row['no_hp'] ?? '')) ?: null]);
        }
    }

    private function uniqueUsername(string $source): string
    {
        $base = Str::of($source)->lower()->ascii()->replaceMatches('/[^a-z0-9._-]+/', '.')->trim('.')->limit(40, '')->toString() ?: 'user';
        $username = $base;
        $suffix = 1;

        while (User::query()->where('username', $username)->exists()) {
            $username = $base.'.'.$suffix++;
        }

        return $username;
    }
}
