@php
    $meta = [
        'hero' => ['Hero & tombol', 'Atur kesan pertama pengunjung: pesan utama, gambar sampul, dan tombol navigasi.'],
        'profile' => ['Profil sekolah', 'Atur profil, video besar setelah hero, serta visi dan misi sekolah.'],
        'admission' => ['Admisi & kontak', 'Kelola ajakan pendaftaran dan kanal yang dapat dihubungi calon orang tua.'],
    ][$section];
@endphp
<x-layouts.app :title="$meta[0]">
    @include('admin.landing.partials.header', ['title' => $meta[0], 'description' => $meta[1], 'current' => $meta[0]])

    <div class="grid two">
        <section class="panel">
            <form class="stack" method="post" action="{{ route('admin.landing.sections.update', $section) }}">
                @csrf @method('put')
                @if($section === 'hero')
                    <label>Label kecil<input name="eyebrow" value="{{ old('eyebrow', $page->eyebrow) }}" maxlength="80" required><span class="field-help">Teks pendek di atas judul utama, misalnya “Sekolah Islam Terpadu”.</span></label>
                    <label>Judul utama<input name="headline" value="{{ old('headline', $page->headline) }}" maxlength="160" required></label>
                    <label>Pengantar<textarea name="intro" maxlength="600" required>{{ old('intro', $page->intro) }}</textarea></label>
                    <label>URL foto hero<input name="hero_image_url" type="url" value="{{ old('hero_image_url', $page->hero_image_url) }}" placeholder="https://..."><span class="field-help">Gunakan foto horizontal beresolusi besar.</span></label>
                    <div class="form-grid">
                        <label>Teks tombol utama<input name="primary_cta_label" value="{{ old('primary_cta_label', $page->primary_cta_label) }}" required></label>
                        <label>Tujuan tombol utama<input name="primary_cta_url" value="{{ old('primary_cta_url', $page->primary_cta_url) }}" required><span class="field-help">Contoh: #pendaftaran atau URL lengkap.</span></label>
                        <label>Teks tombol kedua<input name="secondary_cta_label" value="{{ old('secondary_cta_label', $page->secondary_cta_label) }}" required></label>
                        <label>Tujuan tombol kedua<input name="secondary_cta_url" value="{{ old('secondary_cta_url', $page->secondary_cta_url) }}" required></label>
                    </div>
                @elseif($section === 'profile')
                    <label>Judul bagian tentang<input name="about_title" value="{{ old('about_title', $page->about_title) }}" maxlength="160" required></label>
                    <label>Narasi sekolah<textarea name="about_body" maxlength="1500" style="min-height:190px" required>{{ old('about_body', $page->about_body) }}</textarea><span class="field-help">Fokus pada nilai, cara belajar, dan hal konkret yang membedakan sekolah.</span></label>
                    <hr style="border:0;border-top:1px solid var(--line);width:100%">
                    <h3>Video sekolah</h3>
                    <label>Judul video<input name="video_title" value="{{ old('video_title', $page->video_title) }}" maxlength="160" placeholder="Mengenal Sekolah Ibrahim"></label>
                    <label>URL video<input name="video_url" type="url" value="{{ old('video_url', $page->video_url) }}" placeholder="https://www.youtube.com/watch?v=..."><span class="field-help">Masukkan tautan YouTube, Vimeo, atau tautan video langsung. Bagian ini tampil tepat setelah hero.</span></label>
                    <hr style="border:0;border-top:1px solid var(--line);width:100%">
                    <h3>Visi dan misi</h3>
                    <label>Visi sekolah<textarea name="vision" maxlength="1500">{{ old('vision', $page->vision) }}</textarea></label>
                    <label>Misi sekolah<textarea name="mission" maxlength="3000" style="min-height:150px">{{ old('mission', $page->mission) }}</textarea><span class="field-help">Tulis satu misi per baris.</span></label>
                @else
                    <label>Judul ajakan pendaftaran<input name="admission_title" value="{{ old('admission_title', $page->admission_title) }}" maxlength="160" required></label>
                    <label>Keterangan pendaftaran<textarea name="admission_body" maxlength="1000" required>{{ old('admission_body', $page->admission_body) }}</textarea></label>
                    <div class="form-grid">
                        <label>WhatsApp admisi<input name="whatsapp" value="{{ old('whatsapp', $page->whatsapp) }}" placeholder="6281234567890"><span class="field-help">Gunakan kode negara tanpa tanda +.</span></label>
                        <label>Email publik<input name="email" type="email" value="{{ old('email', $page->email) }}" placeholder="info@sekolah.sch.id"></label>
                        <label class="wide">Instagram<input name="instagram_url" type="url" value="{{ old('instagram_url', $page->instagram_url) }}" placeholder="https://instagram.com/..."></label>
                        <label class="wide">Alamat sekolah<textarea name="address">{{ old('address', $page->address) }}</textarea></label>
                    </div>
                @endif
                <div class="actions"><button class="btn primary" type="submit">Simpan perubahan</button><a class="btn" href="{{ route('admin.landing.index') }}">Batal</a></div>
            </form>
        </section>
        <aside class="panel">
            <span class="eyebrow">Pratinjau isi</span>
            @if($section === 'hero')
                <h2 style="font-size:25px;margin:12px 0 8px">{{ $page->headline }}</h2><p class="muted">{{ $page->intro }}</p>
            @elseif($section === 'profile')
                <h2 style="font-size:23px;margin:12px 0 8px">{{ $page->about_title }}</h2><p class="muted">{{ $page->about_body }}</p>
            @else
                <h2 style="font-size:23px;margin:12px 0 8px">{{ $page->admission_title }}</h2><p class="muted">{{ $page->admission_body }}</p>
                <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--line)"><strong>{{ $page->whatsapp ?: 'WhatsApp belum diisi' }}</strong><p class="muted" style="margin:4px 0">{{ $page->email ?: 'Email belum diisi' }}</p></div>
            @endif
            <p class="field-help" style="margin-top:22px">Pratinjau lengkap tersedia melalui tombol “Lihat situs publik”.</p>
        </aside>
    </div>
</x-layouts.app>
