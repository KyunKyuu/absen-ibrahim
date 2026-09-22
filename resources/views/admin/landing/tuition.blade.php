<x-layouts.app title="Biaya Pendidikan">
    <style>
        .tuition-layout{display:grid;grid-template-columns:minmax(320px,.8fr) minmax(0,1.2fr);gap:18px;align-items:start}.sticky-form{position:sticky;top:24px}.panel-head{display:flex;justify-content:space-between;gap:15px;align-items:flex-start;margin-bottom:17px}
        @media(max-width:960px){.tuition-layout{grid-template-columns:1fr}.sticky-form{position:static}}
    </style>
    @include('admin.landing.partials.header', [
        'title' => 'Biaya pendidikan',
        'description' => 'Atur paket biaya, fasilitas, dan pilihan yang ingin ditonjolkan kepada calon orang tua.',
        'current' => 'Biaya pendidikan',
    ])

    <div class="tuition-layout">
        <section class="panel sticky-form">
            <span class="eyebrow">Paket baru</span>
            <h2 style="margin-top:7px">Tambah paket biaya</h2>
            <form class="stack" method="post" action="{{ route('admin.landing.tuition.store') }}">
                @csrf
                <div class="form-grid">
                    <label>Nama paket<input name="name" value="{{ old('name') }}" placeholder="Paket Reguler" required></label>
                    <label>Nominal (Rp)<input name="price" type="number" min="0" value="{{ old('price') }}" required></label>
                    <label>Periode pembayaran<input name="billing_period" value="{{ old('billing_period', 'per bulan') }}" required></label>
                    <label>Urutan<input name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', 0) }}" required></label>
                </div>
                <label>Deskripsi singkat<textarea name="description">{{ old('description') }}</textarea></label>
                <label>Daftar fasilitas<textarea name="features_text" placeholder="Pembelajaran akademik&#10;Program tahfiz&#10;Ekstrakurikuler">{{ old('features_text') }}</textarea><span class="field-help">Satu fasilitas per baris.</span></label>
                <div class="form-grid">
                    <label>Teks tombol<input name="cta_label" value="{{ old('cta_label', 'Tanya paket ini') }}" required></label>
                    <label>Tujuan tombol<input name="cta_url" value="{{ old('cta_url', '#pendaftaran') }}"></label>
                </div>
                <div class="actions">
                    <label class="check-row"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', true))> Tampilkan</label>
                    <label class="check-row"><input name="is_featured" type="checkbox" value="1" @checked(old('is_featured'))> Rekomendasi</label>
                </div>
                <button class="btn primary" type="submit">Tambah paket</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-head"><div><h2>Paket tersimpan</h2><span class="muted">{{ $tuitionPackages->count() }} paket · klik untuk mengedit</span></div><span class="badge">{{ $tuitionPackages->where('is_active', true)->count() }} tayang</span></div>
            <div class="cms-list">
                @forelse($tuitionPackages as $package)
                    <details class="cms-item">
                        <summary>
                            <span><strong>{{ $package->name }}</strong><small>Rp{{ number_format($package->price, 0, ',', '.') }} · {{ $package->billing_period }}</small></span>
                            @if($package->is_featured)<span class="badge">rekomendasi</span>@else<span></span>@endif
                            <span class="badge {{ $package->is_active ? '' : 'priority' }}">{{ $package->is_active ? 'tayang' : 'draf' }}</span>
                        </summary>
                        <div class="cms-edit">
                            <form class="stack" method="post" action="{{ route('admin.landing.tuition.update', $package) }}">
                                @csrf @method('put')
                                <div class="form-grid">
                                    <label>Nama paket<input name="name" value="{{ $package->name }}" required></label>
                                    <label>Nominal (Rp)<input name="price" type="number" min="0" value="{{ $package->price }}" required></label>
                                    <label>Periode<input name="billing_period" value="{{ $package->billing_period }}" required></label>
                                    <label>Urutan<input name="sort_order" type="number" min="0" max="999" value="{{ $package->sort_order }}" required></label>
                                    <label class="wide">Deskripsi<textarea name="description">{{ $package->description }}</textarea></label>
                                    <label class="wide">Daftar fasilitas<textarea name="features_text">{{ implode("\n", $package->features ?? []) }}</textarea></label>
                                    <label>Teks tombol<input name="cta_label" value="{{ $package->cta_label }}" required></label>
                                    <label>Tujuan tombol<input name="cta_url" value="{{ $package->cta_url }}"></label>
                                </div>
                                <div class="actions">
                                    <label class="check-row"><input name="is_active" type="checkbox" value="1" @checked($package->is_active)> Tampilkan</label>
                                    <label class="check-row"><input name="is_featured" type="checkbox" value="1" @checked($package->is_featured)> Rekomendasi</label>
                                </div>
                                <button class="btn" type="submit">Simpan perubahan</button>
                            </form>
                            <form class="cms-delete" method="post" action="{{ route('admin.landing.tuition.destroy', $package) }}" onsubmit="return confirm('Hapus paket biaya ini?')">
                                @csrf @method('delete')<button class="btn warn small" type="submit">Hapus paket</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <div class="empty-state"><strong>Belum ada paket biaya</strong><p>Tambahkan paket pertama melalui formulir di sebelah kiri.</p></div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
