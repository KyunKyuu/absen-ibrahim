<x-layouts.app title="Penilaian">
    <div class="topbar">
        <div>
            <h1>Penilaian Guru</h1>
            <p class="muted">Input sikap, pencapaian kelas, prestasi, atau pelanggaran. Poin siswa dihitung otomatis.</p>
        </div>
    </div>

    <div class="grid two">
        <section class="panel">
            <h2>Penilaian Sikap</h2>
            <form class="stack" method="post" action="{{ route('teacher.assessments.attitude') }}">
                @csrf
                <label>Siswa
                    <select name="student_user_id" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->studentProfile?->schoolClass?->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="form-grid">
                    <label>Aspek
                        <select name="aspect" required>
                            <option>Disiplin</option>
                            <option>Tanggung Jawab</option>
                            <option>Kerja Sama</option>
                            <option>Kejujuran</option>
                            <option>Sopan Santun</option>
                            <option>Kepemimpinan</option>
                        </select>
                    </label>
                    <label>Skor
                        <select name="score" required>
                            <option value="5">5 - Sangat Baik</option>
                            <option value="4">4 - Baik</option>
                            <option value="3">3 - Cukup</option>
                            <option value="2">2 - Kurang</option>
                            <option value="1">1 - Buruk</option>
                        </select>
                    </label>
                </div>
                <label>Catatan <textarea name="notes"></textarea></label>
                <button class="btn primary" type="submit">Simpan Sikap</button>
            </form>
        </section>

        <section class="panel">
            <h2>Pencapaian / Prestasi</h2>
            <form class="stack" method="post" action="{{ route('teacher.assessments.achievement') }}">
                @csrf
                <label>Siswa
                    <select name="student_user_id" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->studentProfile?->schoolClass?->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Judul <input name="title" placeholder="Aktif bertanya di kelas" required></label>
                <div class="form-grid">
                    <label>Kategori
                        <select name="category" required>
                            <option value="achievement">Prestasi/Pencapaian</option>
                            <option value="violation">Pelanggaran</option>
                        </select>
                    </label>
                    <label>Poin <input name="points" type="number" min="-100" max="100" value="10" required></label>
                </div>
                <label>Catatan <textarea name="notes"></textarea></label>
                <button class="btn primary" type="submit">Simpan Poin</button>
            </form>
        </section>
    </div>
</x-layouts.app>
