<x-layouts.app title="Landing Page">
    <style>
        .overview-hero{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(280px,.7fr);gap:18px;margin-bottom:18px}
        .site-preview{min-height:230px;display:flex;flex-direction:column;justify-content:flex-end;padding:28px;border-radius:14px;color:#fff;background:linear-gradient(110deg,rgba(10,39,29,.92),rgba(10,39,29,.35)),url('{{ $page->hero_image_url }}') center/cover}
        .site-preview span{font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.site-preview strong{display:block;max-width:520px;margin-top:9px;font-size:28px;line-height:1.15;letter-spacing:-.035em}
        .quick-card{display:flex;flex-direction:column;justify-content:space-between}.quick-list{display:grid;gap:5px;margin-top:8px}.quick-list a{display:flex;justify-content:space-between;padding:10px;border-radius:8px;font-weight:680}.quick-list a:hover{background:var(--accent-soft);color:var(--accent)}
        .content-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.section-card{display:flex;flex-direction:column;min-height:160px;padding:18px;border:1px solid var(--line);border-radius:12px;background:#fff;box-shadow:var(--shadow)}.section-card:hover{border-color:#b8cbc2}.section-card .card-icon{display:grid;place-items:center;width:35px;height:35px;margin-bottom:24px;border-radius:9px;color:var(--accent);background:var(--accent-soft);font-weight:900}.section-card p{margin:6px 0 14px;font-size:12px}.section-card .card-foot{display:flex;justify-content:space-between;align-items:center;margin-top:auto;color:var(--muted);font-size:11px}.section-card .arrow{color:var(--accent);font-size:17px}
        @media(max-width:950px){.overview-hero{grid-template-columns:1fr}.content-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.content-grid{grid-template-columns:1fr}}
    </style>

    @include('admin.landing.partials.header', [
        'title' => 'Kelola landing page',
        'description' => 'Pilih satu bagian untuk diedit. Setiap halaman hanya berisi pengaturan yang saling berkaitan.',
        'current' => 'Ringkasan',
    ])

    <div class="overview-hero">
        <div class="site-preview">
            <span>{{ $page->eyebrow }}</span>
            <strong>{{ $page->headline }}</strong>
        </div>
        <section class="panel quick-card">
            <div>
                <span class="eyebrow">Akses cepat</span>
                <h2 style="margin-top:7px">Apa yang ingin diubah?</h2>
                <p class="muted">Masuk langsung ke bagian yang dibutuhkan tanpa menggulir formulir panjang.</p>
            </div>
            <div class="quick-list">
                <a href="{{ route('admin.landing.hero') }}"><span>Judul & gambar utama</span><span>→</span></a>
                <a href="{{ route('admin.landing.profile') }}"><span>Atur video, visi-misi, dan podium poin</span><span>→</span></a>
                <a href="{{ route('admin.landing.content', 'champion') }}"><span>Tambah juara sekolah</span><span>→</span></a>
                <a href="{{ route('admin.landing.tuition') }}"><span>Atur biaya pendidikan</span><span>→</span></a>
            </div>
        </section>
    </div>

    <div class="content-grid">
        <a class="section-card" href="{{ route('admin.landing.hero') }}"><span class="card-icon">H</span><h3>Hero & tombol</h3><p class="muted">Judul utama, pengantar, gambar, dan dua tombol ajakan.</p><div class="card-foot"><span>Bagian teratas situs</span><span class="arrow">→</span></div></a>
        <a class="section-card" href="{{ route('admin.landing.profile') }}"><span class="card-icon">P</span><h3>Profil, video & visi-misi</h3><p class="muted">Narasi sekolah, video besar setelah hero, visi, misi, dan pengaturan podium poin.</p><div class="card-foot"><span>Tentang sekolah</span><span class="arrow">→</span></div></a>
        <a class="section-card" href="{{ route('admin.landing.admission') }}"><span class="card-icon">A</span><h3>Admisi & kontak</h3><p class="muted">Ajakan pendaftaran, WhatsApp, email, Instagram, dan alamat.</p><div class="card-foot"><span>Informasi publik</span><span class="arrow">→</span></div></a>
        @foreach($kinds as $kind => $label)
            @php($count = $itemCounts->get($kind))
            <a class="section-card" href="{{ route('admin.landing.content', $kind) }}"><span class="card-icon">{{ strtoupper(substr($label, 0, 1)) }}</span><h3>{{ $label }}</h3><p class="muted">Tambah, susun, terbitkan, atau simpan konten sebagai draf.</p><div class="card-foot"><span>{{ (int) ($count?->active_total ?? 0) }} tayang dari {{ (int) ($count?->total ?? 0) }}</span><span class="arrow">→</span></div></a>
        @endforeach
        <a class="section-card" href="{{ route('admin.landing.tuition') }}"><span class="card-icon">Rp</span><h3>Biaya pendidikan</h3><p class="muted">Kelola nominal, fasilitas, dan paket yang direkomendasikan.</p><div class="card-foot"><span>{{ $activeTuitionCount }} tayang dari {{ $tuitionCount }}</span><span class="arrow">→</span></div></a>
    </div>
</x-layouts.app>
