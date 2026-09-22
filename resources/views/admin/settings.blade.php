@php
    $pages = [
        'general' => ['Profil & absensi', 'Atur identitas sekolah, titik lokasi, dan jam operasional absensi.'],
        'classes' => ['Kelas & wali kelas', 'Kelola daftar kelas dan guru yang bertanggung jawab sebagai wali kelas.'],
        'academic' => ['Tahun ajaran & semester', 'Susun periode akademik aktif tanpa bercampur dengan pengaturan lain.'],
        'iot' => ['Perangkat IoT', 'Daftarkan dan pantau perangkat fingerprint sekolah.'],
    ];
    [$pageTitle, $pageDescription] = $pages[$section];
@endphp
<x-layouts.app :title="$pageTitle">
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><span>Sekolah & IoT</span><span>/</span><span>{{ $pageTitle }}</span></div><h1>{{ $pageTitle }}</h1><p class="muted">{{ $pageDescription }}</p></div></div>
    <nav class="section-tabs" aria-label="Pengaturan sekolah">
        <a class="{{ $section === 'general' ? 'active' : '' }}" href="{{ route('admin.settings') }}">Profil & absensi</a>
        <a class="{{ $section === 'classes' ? 'active' : '' }}" href="{{ route('admin.settings.classes') }}">Kelas</a>
        <a class="{{ $section === 'academic' ? 'active' : '' }}" href="{{ route('admin.settings.academic') }}">Periode akademik</a>
        <a class="{{ $section === 'iot' ? 'active' : '' }}" href="{{ route('admin.settings.iot') }}">Perangkat IoT</a>
    </nav>

    @if(session('device_token'))<div class="alert">Token perangkat baru: <strong>{{ session('device_token') }}</strong><br>Token hanya ditampilkan sekali. Simpan di firmware atau konfigurasi perangkat.</div>@endif

    @if($section === 'general')
        <section class="panel" style="max-width:900px">
            <form class="stack" method="post" action="{{ route('admin.settings.update') }}">@csrf
                <label>Nama sekolah<input name="school_name" value="{{ old('school_name', $setting->school_name) }}" required></label>
                <div class="form-grid">
                    <label>Latitude sekolah<input name="latitude" value="{{ old('latitude', $setting->latitude) }}"></label>
                    <label>Longitude sekolah<input name="longitude" value="{{ old('longitude', $setting->longitude) }}"></label>
                    <label>Maks. radius absensi (meter)<input name="attendance_radius_meters" type="number" min="10" max="5000" value="{{ old('attendance_radius_meters', $setting->attendance_radius_meters) }}" required></label>
                    <label>Maks. galat GPS (meter)<input name="max_location_accuracy_meters" type="number" min="5" max="1000" value="{{ old('max_location_accuracy_meters', $setting->max_location_accuracy_meters) }}" required></label>
                </div>
                <h2 style="margin:8px 0 0">Jadwal absensi</h2>
                <div class="form-grid">
                    <label>Absensi dibuka<input name="attendance_open_time" type="time" value="{{ old('attendance_open_time', substr($setting->attendance_open_time, 0, 5)) }}" required></label>
                    <label>Jam masuk<input name="start_time" type="time" value="{{ old('start_time', substr($setting->start_time, 0, 5)) }}" required></label>
                    <label>Batas tepat waktu<input name="late_after" type="time" value="{{ old('late_after', substr($setting->late_after, 0, 5)) }}" required></label>
                    <label>Absensi ditutup<input name="attendance_close_time" type="time" value="{{ old('attendance_close_time', substr($setting->attendance_close_time, 0, 5)) }}" required></label>
                </div>
                <button class="btn primary" type="submit">Simpan pengaturan</button>
            </form>
        </section>
    @elseif($section === 'classes')
        <div class="grid two">
            <section class="panel">
                <h2>Tambah kelas</h2>
                <form class="stack" method="post" action="{{ route('admin.classes.store') }}">@csrf
                    <label>Nama kelas<input name="name" placeholder="VII A" required></label>
                    <label>Tingkat<input name="grade_level" type="number" min="1" max="12"></label>
                    <button class="btn primary" type="submit">Tambah kelas</button>
                </form>
            </section>
            <section class="panel">
                <h2>Daftar kelas</h2>
                <div class="table-scroll"><table><thead><tr><th>Kelas</th><th>Tingkat</th><th>Wali kelas</th></tr></thead><tbody>
                    @forelse($classes as $class)<tr><td><strong>{{ $class->name }}</strong></td><td>{{ $class->grade_level ?: '-' }}</td><td><form method="post" action="{{ route('admin.classes.homeroom', $class) }}">@csrf<select name="homeroom_teacher_id" onchange="this.form.submit()" aria-label="Wali kelas {{ $class->name }}"><option value="">Belum ada wali kelas</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected($class->homeroom_teacher_id === $teacher->id)>{{ $teacher->name }}</option>@endforeach</select></form></td></tr>
                    @empty<tr><td colspan="3" class="muted">Belum ada kelas.</td></tr>@endforelse
                </tbody></table></div>
            </section>
        </div>
    @elseif($section === 'academic')
        <div class="grid two">
            <section class="panel"><h2>Tahun ajaran</h2>
                <form class="stack" method="post" action="{{ route('admin.academic-years.store') }}">@csrf
                    <label>Nama<input name="name" placeholder="2027/2028" required></label><div class="form-grid"><label>Mulai<input name="starts_on" type="date" required></label><label>Selesai<input name="ends_on" type="date" required></label></div>
                    <label class="check-row"><input name="is_active" type="checkbox" value="1"> Jadikan tahun ajaran aktif</label><button class="btn primary" type="submit">Tambah tahun ajaran</button>
                </form>
                <table style="margin-top:18px">@foreach($academicYears as $year)<tr><td><strong>{{ $year->name }}</strong><br><span class="muted">{{ $year->starts_on?->format('d M Y') }}–{{ $year->ends_on?->format('d M Y') }}</span></td><td><span class="badge">{{ $year->is_active ? 'aktif' : 'arsip' }}</span></td></tr>@endforeach</table>
            </section>
            <section class="panel"><h2>Semester</h2>
                <form class="stack" method="post" action="{{ route('admin.semesters.store') }}">@csrf
                    <label>Tahun ajaran<select name="academic_year_id" required>@foreach($academicYears as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></label>
                    <label>Nama<input name="name" placeholder="Ganjil" required></label><div class="form-grid"><label>Mulai<input name="starts_on" type="date" required></label><label>Selesai<input name="ends_on" type="date" required></label></div>
                    <label class="check-row"><input name="is_active" type="checkbox" value="1"> Jadikan semester aktif</label><button class="btn primary" type="submit">Tambah semester</button>
                </form>
                <table style="margin-top:18px">@foreach($semesters as $semester)<tr><td><strong>{{ $semester->name }}</strong><br><span class="muted">{{ $semester->academicYear?->name }}</span></td><td><span class="badge">{{ $semester->is_active ? 'aktif' : 'arsip' }}</span></td></tr>@endforeach</table>
            </section>
        </div>
    @else
        <div class="grid two">
            <section class="panel"><h2>Daftarkan perangkat</h2>
                <form class="stack" method="post" action="{{ route('admin.iot-devices.store') }}">@csrf
                    <label>Nama perangkat<input name="name" placeholder="Fingerprint Gerbang Utama" required></label>
                    <label>Device identifier<input name="identifier" placeholder="FP-GERBANG-01" required></label>
                    <label>Kelas terkait<select name="school_class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></label>
                    <button class="btn primary" type="submit">Daftarkan perangkat</button>
                </form>
                <div class="alert" style="margin:18px 0 0"><strong>Endpoint perangkat</strong><br><code>POST /api/iot/attendance</code></div>
            </section>
            <section class="panel"><h2>Perangkat terdaftar</h2>
                <div class="table-scroll"><table><thead><tr><th>Perangkat</th><th>ID</th><th>Status</th><th>Terakhir aktif</th></tr></thead><tbody>
                    @forelse($devices as $device)<tr><td><strong>{{ $device->name }}</strong></td><td>{{ $device->identifier }}</td><td><span class="badge">{{ $device->is_active ? 'aktif' : 'nonaktif' }}</span></td><td>{{ $device->last_seen_at?->format('d M Y H:i') ?? '-' }}</td></tr>@empty<tr><td colspan="4" class="muted">Belum ada perangkat.</td></tr>@endforelse
                </tbody></table></div>
            </section>
        </div>
    @endif
</x-layouts.app>
