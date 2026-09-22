<div class="page-heading">
    <div class="page-heading-copy">
        <div class="breadcrumb"><a href="{{ route('admin.landing.index') }}">Landing Page</a><span>/</span><span>{{ $current ?? 'Ringkasan' }}</span></div>
        <h1>{{ $title }}</h1>
        <p class="muted">{{ $description }}</p>
    </div>
    <a class="btn" href="{{ route('landing') }}" target="_blank">Lihat situs publik <span>↗</span></a>
</div>

<nav class="section-tabs" aria-label="Bagian landing page">
    <a class="{{ request()->routeIs('admin.landing.index') ? 'active' : '' }}" href="{{ route('admin.landing.index') }}">Ringkasan</a>
    <a class="{{ request()->routeIs('admin.landing.hero') ? 'active' : '' }}" href="{{ route('admin.landing.hero') }}">Hero & tombol</a>
    <a class="{{ request()->routeIs('admin.landing.profile') ? 'active' : '' }}" href="{{ route('admin.landing.profile') }}">Profil sekolah</a>
    <a class="{{ request()->routeIs('admin.landing.admission') ? 'active' : '' }}" href="{{ route('admin.landing.admission') }}">Admisi & kontak</a>
    <a class="{{ request()->routeIs('admin.landing.content') ? 'active' : '' }}" href="{{ route('admin.landing.content', 'program') }}">Konten</a>
    <a class="{{ request()->routeIs('admin.landing.tuition') ? 'active' : '' }}" href="{{ route('admin.landing.tuition') }}">Biaya pendidikan</a>
</nav>
