<x-layouts.app :title="$kindLabel">
    <style>
        .content-layout{display:grid;grid-template-columns:minmax(310px,.75fr) minmax(0,1.25fr);gap:18px;align-items:start}
        .sticky-form{position:sticky;top:24px}.panel-head{display:flex;justify-content:space-between;align-items:flex-start;gap:15px;margin-bottom:17px}.panel-head h2{margin-bottom:4px}
        @media(max-width:960px){.content-layout{grid-template-columns:1fr}.sticky-form{position:static}}
    </style>
    @include('admin.landing.partials.header', [
        'title' => $kindLabel,
        'description' => 'Kelola satu jenis konten saja agar proses menambah dan menyusun informasi tetap fokus.',
        'current' => $kindLabel,
    ])

    <nav class="section-tabs" aria-label="Jenis konten" style="margin-top:-12px">
        @foreach(['program' => 'Program', 'activity' => 'Kegiatan', 'champion' => 'Juara', 'testimonial' => 'Testimoni', 'statistic' => 'Statistik'] as $tabKind => $tabLabel)
            <a class="{{ $kind === $tabKind ? 'active' : '' }}" href="{{ route('admin.landing.content', $tabKind) }}">{{ $tabLabel }}</a>
        @endforeach
    </nav>

    <div class="content-layout">
        <section class="panel sticky-form">
            <div class="panel-head"><div><span class="eyebrow">Konten baru</span><h2 style="margin-top:6px">Tambah {{ strtolower($kindLabel) }}</h2></div></div>
            <form class="stack" method="post" action="{{ route('admin.landing.items.store') }}">
                @csrf
                <input type="hidden" name="kind" value="{{ $kind }}">
                <label>{{ $kind === 'statistic' ? 'Nilai statistik' : (in_array($kind, ['testimonial', 'champion']) ? 'Nama' : 'Judul') }}<input name="title" value="{{ old('title') }}" required></label>
                <label>{{ $kind === 'testimonial' ? 'Peran' : 'Label kecil' }}<input name="kicker" value="{{ old('kicker') }}" placeholder="{{ $kind === 'testimonial' ? 'Orang tua siswa' : 'Opsional' }}"></label>
                <label>{{ $kind === 'testimonial' ? 'Kutipan' : 'Deskripsi' }}<textarea name="body">{{ old('body') }}</textarea></label>
                @if(in_array($kind, ['program', 'activity'], true))
                    <label>URL gambar<input name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://..."></label>
                    <label>URL lanjutan<input name="link_url" type="url" value="{{ old('link_url') }}" placeholder="https://..."></label>
                @endif
                <div class="form-grid">
                    <label>Urutan<input name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', 0) }}" required></label>
                    <label>Tanggal terbit<input name="published_at" type="date" value="{{ old('published_at') }}"></label>
                </div>
                <label class="check-row"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', true))> Langsung tampilkan</label>
                <button class="btn primary" type="submit">Tambah konten</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head">
                <div><h2>Daftar {{ strtolower($kindLabel) }}</h2><span class="muted">{{ $items->count() }} konten · klik untuk mengedit</span></div>
                <span class="badge">{{ $items->where('is_active', true)->count() }} tayang</span>
            </div>
            <div class="cms-list">
                @forelse($items as $item)
                    <details class="cms-item">
                        <summary>
                            <span><strong>{{ $item->title }}</strong><small>{{ $item->kicker ?: 'Tanpa label' }}</small></span>
                            <span class="badge">urutan {{ $item->sort_order }}</span>
                            <span class="badge {{ $item->is_active ? '' : 'priority' }}">{{ $item->is_active ? 'tayang' : 'draf' }}</span>
                        </summary>
                        <div class="cms-edit">
                            <form class="stack" method="post" action="{{ route('admin.landing.items.update', $item) }}">
                                @csrf @method('put')
                                <input type="hidden" name="kind" value="{{ $item->kind }}">
                                <div class="form-grid">
                                    <label>Judul / nilai<input name="title" value="{{ $item->title }}" required></label>
                                    <label>Label / peran<input name="kicker" value="{{ $item->kicker }}"></label>
                                    <label class="wide">Isi<textarea name="body">{{ $item->body }}</textarea></label>
                                    <label>URL gambar<input name="image_url" type="url" value="{{ $item->image_url }}"></label>
                                    <label>URL lanjutan<input name="link_url" type="url" value="{{ $item->link_url }}"></label>
                                    <label>Urutan<input name="sort_order" type="number" min="0" max="999" value="{{ $item->sort_order }}" required></label>
                                    <label>Tanggal terbit<input name="published_at" type="date" value="{{ $item->published_at?->format('Y-m-d') }}"></label>
                                </div>
                                <label class="check-row"><input name="is_active" type="checkbox" value="1" @checked($item->is_active)> Tampilkan di landing page</label>
                                <button class="btn" type="submit">Simpan perubahan</button>
                            </form>
                            <form class="cms-delete" method="post" action="{{ route('admin.landing.items.destroy', $item) }}" onsubmit="return confirm('Hapus konten ini?')">
                                @csrf @method('delete')<button class="btn warn small" type="submit">Hapus konten</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <div class="empty-state"><strong>Belum ada {{ strtolower($kindLabel) }}</strong><p>Gunakan formulir di sebelah kiri untuk menambahkan konten pertama.</p></div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
