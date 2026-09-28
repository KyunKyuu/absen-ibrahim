<x-layouts.app title="Absensi Siswa">
    <style>
        .student-checkin-page{max-width:680px;margin:0 auto}.student-checkin-head{text-align:center;margin-bottom:20px}.student-checkin-head .mark{display:grid;place-items:center;width:68px;height:68px;margin:0 auto 14px;border-radius:22px;color:#15533b;background:#b9e3cc;font-size:30px;font-weight:900}.student-checkin-card{padding:24px;border-radius:18px;background:#fff;box-shadow:0 12px 35px rgba(16,35,28,.08)}.student-checkin-card .attendance-success{padding:20px}.back-dashboard{display:inline-flex;margin-bottom:18px;color:var(--accent);font-size:13px;font-weight:750}@media(max-width:600px){.main{padding:20px 14px 38px}.student-checkin-card{padding:16px}.student-checkin-card .btn{min-height:50px;font-size:15px}}
    </style>
    <div class="student-checkin-page">
        <a class="back-dashboard" href="{{ route('dashboard') }}">← Kembali ke overview</a>
        <div class="student-checkin-head">
            <div class="mark">✓</div>
            <h1>Absensi hari ini</h1>
            <p class="muted">Halo, {{ auth()->user()->name }}. Pastikan GPS dan lokasi presisi HP aktif.</p>
        </div>
        @if(session('status'))<div class="alert">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="alert errors">{{ $errors->first() }}</div>@endif
        <div class="student-checkin-card">
            <x-attendance-check-in :attendance="$attendance" :setting="$setting" />
        </div>
    </div>
</x-layouts.app>
