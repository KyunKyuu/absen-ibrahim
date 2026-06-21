<x-layouts.app title="Sekolah dan IoT">
    <div class="topbar">
        <div>
            <h1>Sekolah & IoT</h1>
            <p class="muted">Atur radius absensi GPS dan siapkan perangkat fingerprint untuk jalur absensi IoT.</p>
        </div>
    </div>

    @if(session('device_token'))
        <div class="alert">
            Token perangkat baru: <strong>{{ session('device_token') }}</strong><br>
            Token hanya ditampilkan sekali. Simpan di firmware atau konfigurasi perangkat.
        </div>
    @endif

    <div class="grid two">
        <section class="panel">
            <h2>Pengaturan Lokasi</h2>
            <form class="stack" method="post" action="{{ route('admin.settings.update') }}">
                @csrf
                <label>Nama sekolah <input name="school_name" value="{{ old('school_name', $setting->school_name) }}" required></label>
                <div class="form-grid">
                    <label>Latitude sekolah <input name="latitude" value="{{ old('latitude', $setting->latitude) }}"></label>
                    <label>Longitude sekolah <input name="longitude" value="{{ old('longitude', $setting->longitude) }}"></label>
                    <label>Radius meter <input name="attendance_radius_meters" type="number" min="10" value="{{ old('attendance_radius_meters', $setting->attendance_radius_meters) }}" required></label>
                    <label>Jam masuk <input name="start_time" type="time" value="{{ old('start_time', substr($setting->start_time, 0, 5)) }}" required></label>
                    <label>Batas tepat waktu <input name="late_after" type="time" value="{{ old('late_after', substr($setting->late_after, 0, 5)) }}" required></label>
                </div>
                <button class="btn primary" type="submit">Simpan Pengaturan</button>
            </form>
        </section>

        <section class="panel">
            <h2>Kelas</h2>
            <form class="stack" method="post" action="{{ route('admin.classes.store') }}">
                @csrf
                <div class="form-grid">
                    <label>Nama kelas <input name="name" required></label>
                    <label>Tingkat <input name="grade_level" type="number" min="1" max="12"></label>
                </div>
                <button class="btn" type="submit">Tambah Kelas</button>
            </form>
            <table style="margin-top:14px">
                @foreach($classes as $class)
                    <tr><td>{{ $class->name }}</td><td>{{ $class->grade_level }}</td></tr>
                @endforeach
            </table>
        </section>
    </div>

    <div class="grid two" style="margin-top:18px">
        <section class="panel">
            <h2>Perangkat Fingerprint IoT</h2>
            <form class="stack" method="post" action="{{ route('admin.iot-devices.store') }}">
                @csrf
                <label>Nama perangkat <input name="name" placeholder="Fingerprint Gerbang Utama" required></label>
                <label>Device identifier <input name="identifier" placeholder="FP-GERBANG-01" required></label>
                <label>Kelas terkait
                    <select name="school_class_id">
                        <option value="">Semua kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="btn primary" type="submit">Daftarkan Perangkat</button>
            </form>
        </section>
        <section class="panel">
            <h2>Kontrak API IoT</h2>
            <p class="muted">Perangkat mengirim POST JSON ke <code>/api/iot/attendance</code>. Field yang wajib: <code>device_identifier</code>, <code>api_token</code>, <code>fingerprint_user_id</code>. Field opsional: <code>scanned_at</code>.</p>
            <table>
                <thead><tr><th>Perangkat</th><th>ID</th><th>Status</th><th>Last seen</th></tr></thead>
                @foreach($devices as $device)
                    <tr>
                        <td>{{ $device->name }}</td>
                        <td>{{ $device->identifier }}</td>
                        <td><span class="badge">{{ $device->is_active ? 'aktif' : 'nonaktif' }}</span></td>
                        <td>{{ $device->last_seen_at?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                @endforeach
            </table>
        </section>
    </div>
</x-layouts.app>
