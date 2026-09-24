@props(['attendance', 'setting'])
<style>#geo-cancel[hidden]{display:none}</style>
<section class="panel">
    <h2>Absensi hari ini</h2>
    <p class="muted">{{ now()->format('d M Y') }} · {{ config('app.timezone') }}</p>
    @if($attendance)
        <div class="attendance-success">
            <span class="status-icon">✓</span>
            <div>
                <strong>Absensi sudah tersimpan</strong>
                <p class="muted">{{ substr($attendance->checked_in_at ?? '', 0, 5) }} · {{ $attendance->is_ontime ? 'Tepat waktu' : 'Terlambat' }}</p>
                @if($attendance->distance_meters !== null)<small>Jarak {{ $attendance->distance_meters }} m · galat lokasi ±{{ $attendance->location_accuracy_meters }} m.</small>@endif
            </div>
        </div>
    @elseif($setting->latitude === null || $setting->longitude === null)
        <div class="alert errors">Titik absensi sekolah belum diatur. Hubungi admin sekolah sebelum melakukan absensi.</div>
    @else
        <form class="stack" method="post" action="{{ route('attendance.check-in') }}" id="attendance-form"
              data-latitude="{{ $setting->latitude }}" data-longitude="{{ $setting->longitude }}"
              data-radius="{{ $setting->attendance_radius_meters }}" data-accuracy="{{ $setting->max_location_accuracy_meters }}">
            @csrf
            <input type="hidden" name="latitude">
            <input type="hidden" name="longitude">
            <input type="hidden" name="accuracy">
            <input type="hidden" name="location_timestamp">
            <div>
                <strong>{{ substr($setting->attendance_open_time, 0, 5) }}–{{ substr($setting->attendance_close_time, 0, 5) }}</strong>
                <p class="muted">Batas tepat waktu {{ substr($setting->late_after, 0, 5) }}. Jam absensi mengikuti waktu server.</p>
            </div>
            <p class="muted">Aktifkan lokasi/GPS dan lokasi presisi di HP, lalu izinkan browser mengakses lokasi. Tetap buka halaman ini selama pemeriksaan.</p>
            <span class="field-help">Radius sekolah {{ $setting->attendance_radius_meters }} m. Galat maksimal ±{{ $setting->max_location_accuracy_meters }} m; jarak beserta galatnya harus masuk radius.</span>
            <p class="geo-status muted" id="geo-status" role="status" aria-live="polite">Menyiapkan pemeriksaan lokasi…</p>
            <button class="btn primary" type="submit" id="geo-button" disabled>Periksa GPS & absen</button>
            <button class="btn" type="button" id="geo-cancel" hidden>Batalkan pemeriksaan</button>
            <noscript><div class="alert errors">Aktifkan JavaScript agar browser dapat meminta izin dan membaca lokasi.</div></noscript>
        </form>
        <script type="module" src="{{ asset('js/attendance-check-in.js') }}"></script>
    @endif
</section>
