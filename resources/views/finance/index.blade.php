@php
    $managerPages = [
        'bills' => ['Daftar tagihan', 'Pantau tagihan, tunggakan, dan catat pembayaran siswa.'],
        'issue' => ['Terbitkan tagihan', 'Buat tagihan untuk seluruh siswa dalam satu kelas.'],
        'fee-types' => ['Jenis tagihan', 'Kelola master tagihan dan nominal default.'],
        'proposals' => ['Usulan biaya', 'Tinjau pengajuan biaya kegiatan dari wali kelas.'],
        'record-payment' => ['Catat pembayaran', 'Rekam pembayaran tunai atau transfer yang diterima tata usaha.'],
        'confirmations' => ['Verifikasi pembayaran', 'Periksa bukti pembayaran yang dikirim siswa atau orang tua.'],
        'payments' => ['Riwayat pembayaran', 'Lihat seluruh transaksi pembayaran dan status verifikasinya.'],
    ];
    $teacherPages = [
        'bills' => ['Ajukan biaya kelas', 'Kirim usulan kegiatan atau tagihan kelas kepada tata usaha.'],
        'proposals' => ['Riwayat usulan', 'Pantau status pengajuan biaya kelas yang pernah dikirim.'],
    ];
    $payerPages = [
        'bills' => [$user->hasRole('parent') ? 'Tagihan anak' : 'Tagihan saya', 'Lihat sisa tagihan dan kirim konfirmasi pembayaran.'],
        'payments' => ['Riwayat pembayaran', 'Pantau konfirmasi pembayaran dan hasil verifikasi tata usaha.'],
    ];
    [$pageTitle, $pageDescription] = ($canManage ? $managerPages : ($isPayer ? $payerPages : $teacherPages))[$section];
@endphp
<x-layouts.app :title="$pageTitle">
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><span>Keuangan</span><span>/</span><span>{{ $pageTitle }}</span></div><h1>{{ $pageTitle }}</h1><p class="muted">{{ $pageDescription }}</p></div></div>
    <nav class="section-tabs" aria-label="Bagian keuangan">
        @if($canManage)
            <a class="{{ $section === 'bills' ? 'active' : '' }}" href="{{ route('finance.index') }}">Tagihan</a>
            <a class="{{ $section === 'record-payment' ? 'active' : '' }}" href="{{ route('finance.record-payment') }}">Catat pembayaran</a>
            <a class="{{ $section === 'confirmations' ? 'active' : '' }}" href="{{ route('finance.confirmations') }}">Verifikasi pembayaran</a>
            <a class="{{ $section === 'issue' ? 'active' : '' }}" href="{{ route('finance.issue') }}">Terbitkan tagihan</a>
            <a class="{{ $section === 'fee-types' ? 'active' : '' }}" href="{{ route('finance.fee-types') }}">Jenis tagihan</a>
            <a class="{{ $section === 'proposals' ? 'active' : '' }}" href="{{ route('finance.proposals') }}">Usulan biaya</a>
            <a class="{{ $section === 'payments' ? 'active' : '' }}" href="{{ route('finance.payment-history') }}">Riwayat</a>
        @elseif($isPayer)
            <a class="{{ $section === 'bills' ? 'active' : '' }}" href="{{ route('finance.index') }}">{{ $user->hasRole('parent') ? 'Tagihan anak' : 'Tagihan saya' }}</a>
            <a class="{{ $section === 'payments' ? 'active' : '' }}" href="{{ route('finance.payment-history') }}">Riwayat pembayaran</a>
        @else
            <a class="{{ $section === 'bills' ? 'active' : '' }}" href="{{ route('finance.index') }}">Ajukan biaya</a>
            <a class="{{ $section === 'proposals' ? 'active' : '' }}" href="{{ route('finance.proposals') }}">Riwayat usulan</a>
        @endif
    </nav>

    @if($isPayer && $section === 'bills')
        <div class="stack">
            @forelse($bills as $bill)
                @php($hasPending = $bill->payments->contains('status', 'pending'))
                <section class="panel">
                    <div class="topbar" style="margin-bottom:14px"><div><span class="eyebrow">{{ $bill->student?->name }}</span><h2 style="margin-top:6px">{{ $bill->title }}</h2><span class="muted">{{ $bill->billing_period ?: 'Tanpa periode' }} · jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }}</span></div><span class="badge {{ $bill->is_arrears ? 'priority' : '' }}">{{ $bill->status }}</span></div>
                    <div class="grid stats" style="grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:16px"><div class="metric"><span class="muted">Total</span><strong style="font-size:20px">Rp{{ number_format($bill->amount, 0, ',', '.') }}</strong></div><div class="metric"><span class="muted">Terbayar</span><strong style="font-size:20px">Rp{{ number_format($bill->paid_amount, 0, ',', '.') }}</strong></div><div class="metric"><span class="muted">Sisa</span><strong style="font-size:20px">Rp{{ number_format($bill->outstanding_amount, 0, ',', '.') }}</strong></div></div>
                    @if($bill->status !== 'paid' && ! $hasPending)
                        <details class="cms-item"><summary><span><strong>Konfirmasi pembayaran</strong><small>Isi data setelah melakukan pembayaran</small></span><span></span><span>⌄</span></summary><div class="cms-edit">
                            <form class="stack" method="post" enctype="multipart/form-data" action="{{ route('finance.payments.confirm', $bill) }}">@csrf
                                <div class="form-grid"><label>Nama siswa<input value="{{ $bill->student?->name }}" disabled></label><label>Bayar untuk<input value="{{ $bill->title }}" disabled></label><label>Nominal<input name="amount" type="number" min="1" max="{{ $bill->outstanding_amount }}" value="{{ $bill->outstanding_amount }}" required></label><label>Tanggal pembayaran<input name="paid_on" type="date" max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required></label><label>Metode<select name="payment_method" required><option value="transfer">Transfer</option><option value="cash">Tunai</option><option value="other">Lainnya</option></select></label><label>Nomor referensi<input name="reference" placeholder="Opsional"></label><label class="wide">Foto bukti pembayaran<input name="proof" type="file" accept="image/jpeg,image/png,image/webp"><span class="field-help">Opsional, JPG/PNG/WebP maksimal 5 MB.</span></label><label class="wide">Catatan<textarea name="notes" placeholder="Opsional"></textarea></label></div>
                                <button class="btn primary" type="submit">Kirim untuk diverifikasi</button>
                            </form></div>
                        </details>
                    @elseif($hasPending)<div class="alert" style="margin:0">Pembayaran sedang menunggu verifikasi tata usaha.</div>@endif
                </section>
            @empty<div class="empty-state">Belum ada tagihan untuk akun ini.</div>@endforelse
        </div>
    @elseif(! $canManage && ! $isPayer && $section === 'bills')
        <section class="panel" style="max-width:780px">
            <form class="stack" method="post" action="{{ route('finance.proposals.store') }}">@csrf
                <label>Kelas wali<select name="school_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></label>
                <label>Nama kegiatan / tagihan<input name="title" value="{{ old('title') }}" placeholder="Biaya camping" required></label>
                <label>Keterangan<textarea name="description">{{ old('description') }}</textarea></label>
                <div class="form-grid">
                    <label>Skema<select name="billing_mode" required><option value="per_student">Nominal per siswa</option><option value="collective">Target total kolektif</option></select></label>
                    <label>Nominal<input name="amount" type="number" min="1" value="{{ old('amount') }}" required></label>
                    <label>Jatuh tempo<input name="due_date" type="date" value="{{ old('due_date') }}"></label>
                </div>
                <button class="btn primary" type="submit" @disabled($classes->isEmpty())>Ajukan ke tata usaha</button>
                @if($classes->isEmpty())<div class="alert errors" style="margin:0">Admin belum menetapkan Anda sebagai wali kelas.</div>@endif
            </form>
        </section>
    @elseif(! $canManage && ! $isPayer && $section === 'proposals')
        <section class="panel"><div class="table-scroll"><table><thead><tr><th>Usulan</th><th>Kelas</th><th>Nominal</th><th>Status</th><th>Catatan</th></tr></thead><tbody>
            @forelse($proposals as $proposal)<tr><td><strong>{{ $proposal->title }}</strong></td><td>{{ $proposal->schoolClass?->name }}</td><td>Rp {{ number_format($proposal->amount, 0, ',', '.') }}</td><td><span class="badge">{{ $proposal->status }}</span></td><td>{{ $proposal->review_notes ?: '-' }}</td></tr>@empty<tr><td colspan="5" class="muted">Belum ada usulan.</td></tr>@endforelse
        </tbody></table></div></section>
    @elseif($section === 'bills')
        <section class="panel">
            <div class="table-scroll"><table>
                <thead><tr><th>Siswa</th><th>Tagihan</th><th>Kelas saat terbit</th><th>Total</th><th>Terbayar</th><th>Sisa</th><th>Status</th><th>Input pembayaran</th></tr></thead>
                <tbody>@forelse($bills as $bill)<tr>
                    <td><strong>{{ $bill->student?->name }}</strong></td><td>{{ $bill->title }}<br><span class="muted">{{ $bill->billing_period }}</span></td><td>{{ $bill->schoolClass?->name ?? '-' }}</td>
                    <td>Rp {{ number_format($bill->amount, 0, ',', '.') }}</td><td>Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td><td>Rp {{ number_format($bill->outstanding_amount, 0, ',', '.') }}</td>
                    <td><span class="badge {{ $bill->is_arrears ? 'priority' : '' }}">{{ $bill->is_arrears && $bill->status !== 'paid' ? 'tunggakan / ' : '' }}{{ $bill->status }}</span></td>
                    <td>@if($bill->status !== 'paid')<a class="btn small" href="{{ route('finance.record-payment', ['bill' => $bill->id]) }}">Catat pembayaran</a>@else<span class="muted">Lunas</span>@endif</td>
                </tr>@empty<tr><td colspan="8"><div class="empty-state">Belum ada tagihan.</div></td></tr>@endforelse</tbody>
            </table></div><div style="margin-top:14px">{{ $bills->links() }}</div>
        </section>
    @elseif($section === 'issue')
        <section class="panel" style="max-width:800px">
            <form class="stack" method="post" action="{{ route('finance.bills.issue') }}">@csrf
                <div class="form-grid">
                    <label>Jenis tagihan<select name="school_fee_type_id" id="fee-type" required>@foreach($feeTypes as $type)<option value="{{ $type->id }}" data-amount="{{ $type->default_amount }}">{{ $type->name }}</option>@endforeach</select></label>
                    <label>Kelas<select name="school_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></label>
                    <label>Periode<input name="billing_period" placeholder="2026-09 atau 2026/2027" required></label>
                    <label>Nominal per siswa<input id="bill-amount" name="amount" type="number" min="1" value="{{ $feeTypes->first()?->default_amount }}" required></label>
                    <label>Jatuh tempo<input name="due_date" type="date"></label>
                </div>
                <button class="btn primary" type="submit" @disabled($feeTypes->isEmpty() || $classes->isEmpty())>Terbitkan ke semua siswa</button>
                @if($feeTypes->isEmpty())<p class="muted">Buat jenis tagihan terlebih dahulu sebelum menerbitkan tagihan.</p>@endif
            </form>
        </section>
        <script>const feeType=document.getElementById('fee-type');feeType?.addEventListener('change',()=>{document.getElementById('bill-amount').value=feeType.selectedOptions[0]?.dataset.amount??'';});</script>
    @elseif($section === 'fee-types')
        <div class="grid two">
            <section class="panel"><h2>Tambah jenis tagihan</h2><form class="stack" method="post" action="{{ route('finance.fee-types.store') }}">@csrf
                <label>Nama<input name="name" placeholder="SPP" required></label><label>Kode<input name="code" placeholder="SPP" required></label>
                <label>Siklus<select name="billing_cycle" required><option value="monthly">Bulanan</option><option value="annual">Tahunan</option><option value="once">Sekali</option></select></label>
                <label>Nominal default<input name="default_amount" type="number" min="1" required></label><button class="btn primary" type="submit">Tambah jenis</button>
            </form></section>
            <section class="panel"><h2>Jenis aktif</h2><table><thead><tr><th>Nama</th><th>Kode</th><th>Siklus</th><th>Nominal</th></tr></thead><tbody>@forelse($feeTypes as $type)<tr><td><strong>{{ $type->name }}</strong></td><td>{{ $type->code }}</td><td>{{ $type->billing_cycle }}</td><td>Rp {{ number_format($type->default_amount, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="4" class="muted">Belum ada jenis tagihan.</td></tr>@endforelse</tbody></table></section>
        </div>
    @elseif($section === 'proposals')
        <section class="panel"><div class="table-scroll"><table><thead><tr><th>Usulan</th><th>Kelas</th><th>Skema</th><th>Nominal</th><th>Status</th><th>Tindakan</th></tr></thead><tbody>
            @forelse($proposals as $proposal)<tr><td><strong>{{ $proposal->title }}</strong><br><span class="muted">{{ $proposal->proposer?->name }}</span></td><td>{{ $proposal->schoolClass?->name }}</td><td>{{ $proposal->billing_mode === 'collective' ? 'Target kolektif' : 'Per siswa' }}</td><td>Rp {{ number_format($proposal->amount, 0, ',', '.') }}</td><td><span class="badge">{{ $proposal->status }}</span></td><td>
                @if($proposal->status === 'pending')<div class="actions"><form method="post" action="{{ route('finance.proposals.approve', $proposal) }}">@csrf<button class="btn primary small" type="submit">Setujui</button></form><form method="post" action="{{ route('finance.proposals.reject', $proposal) }}">@csrf<input name="review_notes" placeholder="Alasan penolakan" required><button class="btn warn small" type="submit">Tolak</button></form></div>@else<span class="muted">{{ $proposal->review_notes ?: 'Sudah ditinjau' }}</span>@endif
            </td></tr>@empty<tr><td colspan="6" class="muted">Belum ada usulan biaya.</td></tr>@endforelse
        </tbody></table></div></section>
    @elseif($section === 'record-payment')
        @php($selectedBill = $availableBills->firstWhere('id', (int) request('bill')) ?? $availableBills->first())
        <section class="panel" style="max-width:850px">
            @if($selectedBill)
                <form class="stack" method="post" enctype="multipart/form-data" action="{{ route('finance.payments.store', $selectedBill) }}">@csrf
                    <label>Tagihan siswa<select id="payment-bill" onchange="location.href='{{ route('finance.record-payment') }}?bill='+this.value">@foreach($availableBills as $bill)<option value="{{ $bill->id }}" @selected($selectedBill->id === $bill->id)>{{ $bill->student?->name }} — {{ $bill->title }} — sisa Rp{{ number_format($bill->outstanding_amount, 0, ',', '.') }}</option>@endforeach</select></label>
                    <div class="form-grid">
                        <label>Nama siswa<input value="{{ $selectedBill->student?->name }}" disabled></label><label>Bayar untuk<input value="{{ $selectedBill->title }}" disabled></label>
                        <label>Nominal pembayaran<input name="amount" type="number" min="1" max="{{ $selectedBill->outstanding_amount }}" value="{{ $selectedBill->outstanding_amount }}" required></label>
                        <label>Tanggal pembayaran<input name="paid_on" type="date" max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required></label>
                        <label>Metode<select name="payment_method" required><option value="cash">Tunai</option><option value="transfer">Transfer</option><option value="other">Lainnya</option></select></label>
                        <label>Nomor referensi<input name="reference" placeholder="Nomor transfer/kuitansi (opsional)"></label>
                        <label class="wide">Foto bukti pembayaran<input name="proof" type="file" accept="image/jpeg,image/png,image/webp"><span class="field-help">Opsional, JPG/PNG/WebP maksimal 5 MB.</span></label>
                        <label class="wide">Catatan<textarea name="notes" placeholder="Catatan tambahan (opsional)"></textarea></label>
                    </div>
                    <button class="btn primary" type="submit">Simpan pembayaran</button>
                </form>
            @else<div class="empty-state">Tidak ada tagihan yang masih harus dibayar.</div>@endif
        </section>
    @elseif($section === 'confirmations')
        <section class="panel"><div class="table-scroll"><table><thead><tr><th>Siswa / tagihan</th><th>Pengirim</th><th>Pembayaran</th><th>Bukti</th><th>Tindakan</th></tr></thead><tbody>
            @forelse($payments as $payment)<tr>
                <td><strong>{{ $payment->bill?->student?->name }}</strong><br><span class="muted">{{ $payment->bill?->title }}</span></td><td>{{ $payment->submittedBy?->name }}<br><span class="muted">{{ $payment->created_at?->format('d M Y H:i') }}</span></td>
                <td><strong>Rp{{ number_format($payment->amount, 0, ',', '.') }}</strong><br><span class="muted">{{ ucfirst($payment->payment_method) }} · {{ $payment->paid_on?->format('d M Y') }}</span></td>
                <td>@if($payment->proof_path)<a class="btn small" target="_blank" href="{{ route('finance.payments.proof', $payment) }}">Lihat bukti</a>@else<span class="muted">Tidak ada</span>@endif</td>
                <td><div class="actions"><form method="post" action="{{ route('finance.payments.approve', $payment) }}">@csrf<button class="btn primary small" type="submit">Verifikasi</button></form><form method="post" action="{{ route('finance.payments.reject', $payment) }}">@csrf<input name="review_notes" placeholder="Alasan penolakan" required><button class="btn warn small" type="submit">Tolak</button></form></div></td>
            </tr>@empty<tr><td colspan="5"><div class="empty-state">Tidak ada pembayaran yang menunggu verifikasi.</div></td></tr>@endforelse
        </tbody></table></div></section>
    @elseif($section === 'payments')
        <section class="panel"><div class="table-scroll"><table><thead><tr><th>Siswa</th><th>Tagihan</th><th>Nominal</th><th>Tanggal / metode</th><th>Status</th><th>Bukti</th><th>Catatan</th></tr></thead><tbody>
            @forelse($payments as $payment)<tr><td><strong>{{ $payment->bill?->student?->name }}</strong></td><td>{{ $payment->bill?->title }}</td><td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td><td>{{ $payment->paid_on?->format('d M Y') }}<br><span class="muted">{{ ucfirst($payment->payment_method) }}</span></td><td><span class="badge {{ $payment->status === 'rejected' ? 'priority' : '' }}">{{ $payment->status }}</span></td><td>@if($payment->proof_path)<a href="{{ route('finance.payments.proof', $payment) }}" target="_blank">Lihat bukti</a>@else-@endif</td><td>{{ $payment->review_notes ?: $payment->notes ?: '-' }}</td></tr>@empty<tr><td colspan="7" class="muted">Belum ada riwayat pembayaran.</td></tr>@endforelse
        </tbody></table></div><div style="margin-top:14px">{{ $payments->links() }}</div></section>
    @endif
</x-layouts.app>
