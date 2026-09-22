@php
    $pages = [
        'accounts' => ['Daftar akun', 'Cari dan kelola role akun yang sudah terdaftar.'],
        'create' => ['Buat akun', 'Tambahkan akun guru, siswa, orang tua, atau staf sekolah.'],
        'import' => ['Import akun', 'Tambahkan banyak akun sekaligus dari Google Sheet.'],
        'roles' => ['Role & akses', 'Atur kelompok role dan izin yang dimiliki setiap role.'],
        'parents' => ['Relasi orang tua', 'Hubungkan akun orang tua dengan siswa.'],
    ];
    [$pageTitle, $pageDescription] = $pages[$section];
@endphp
<x-layouts.app :title="$pageTitle">
    <style>
        .account-filters{display:grid;grid-template-columns:minmax(240px,1.5fr) minmax(170px,.65fr) 110px auto;gap:10px;align-items:end;margin-bottom:18px}.account-filters .btn{width:100%}
        .result-meta{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px}.pagination{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:18px}.pagination-pages{display:flex;gap:5px}.pagination a,.pagination span{display:inline-flex;align-items:center;justify-content:center;min-width:36px;min-height:36px;padding:7px 10px;border:1px solid var(--line);border-radius:8px;background:#fff;font-size:12px;font-weight:700}.pagination .active{border-color:var(--accent);color:#fff;background:var(--accent)}.pagination .disabled{color:#aeb8b3;background:#f5f7f5}.role-fields[hidden]{display:none}
        @media(max-width:850px){.account-filters{grid-template-columns:1fr 1fr}.pagination{align-items:flex-start;flex-direction:column}}@media(max-width:540px){.account-filters{grid-template-columns:1fr}.pagination-pages{flex-wrap:wrap}}
    </style>
    <div class="page-heading">
        <div class="page-heading-copy"><div class="breadcrumb"><span>Akun & akses</span><span>/</span><span>{{ $pageTitle }}</span></div><h1>{{ $pageTitle }}</h1><p class="muted">{{ $pageDescription }}</p></div>
        @if($section === 'accounts')<a class="btn primary" href="{{ route('admin.users.create') }}">+ Buat akun</a>@endif
    </div>
    <nav class="section-tabs" aria-label="Bagian akun">
        <a class="{{ $section === 'accounts' ? 'active' : '' }}" href="{{ route('admin.users') }}">Daftar akun</a>
        <a class="{{ $section === 'create' ? 'active' : '' }}" href="{{ route('admin.users.create') }}">Buat akun</a>
        <a class="{{ $section === 'import' ? 'active' : '' }}" href="{{ route('admin.users.import') }}">Import</a>
        <a class="{{ $section === 'roles' ? 'active' : '' }}" href="{{ route('admin.users.roles') }}">Role & akses</a>
        <a class="{{ $section === 'parents' ? 'active' : '' }}" href="{{ route('admin.users.parents') }}">Relasi orang tua</a>
    </nav>

    @if($section === 'accounts')
        <section class="panel">
            <form class="account-filters" method="get" action="{{ route('admin.users') }}">
                <label>Cari akun<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama, username, atau email..."></label>
                <label>Role<select name="role"><option value="">Semua role</option>@foreach($roles as $role)<option value="{{ $role->name }}" @selected(($filters['role'] ?? '') === $role->name)>{{ $role->label }}</option>@endforeach</select></label>
                <label>Per halaman<select name="per_page"><option value="20" @selected(($filters['per_page'] ?? 20) == 20)>20</option><option value="50" @selected(($filters['per_page'] ?? 20) == 50)>50</option><option value="100" @selected(($filters['per_page'] ?? 20) == 100)>100</option></select></label>
                <button class="btn primary" type="submit">Terapkan</button>
            </form>
            <div class="result-meta"><div><h2 style="margin-bottom:4px">{{ filled($filters['role'] ?? null) ? 'Akun '.($roles->firstWhere('name', $filters['role'])?->label ?? '') : 'Semua akun' }}</h2><span class="muted">{{ $users->total() }} akun ditemukan</span></div>@if(array_filter($filters))<a class="btn small" href="{{ route('admin.users') }}">Hapus filter</a>@endif</div>
            <div class="table-scroll"><table>
                <thead><tr><th>Nama</th><th>Login</th><th>Role</th><th>Kelas</th></tr></thead>
                <tbody>@forelse($users as $account)<tr>
                    <td><strong>{{ $account->name }}</strong></td><td>{{ $account->username ?? $account->email }}</td>
                    <td><form method="post" action="{{ route('admin.users.role', $account) }}">@csrf<select name="role_id" onchange="this.form.submit()" aria-label="Role {{ $account->name }}">@foreach($roles as $role)<option value="{{ $role->id }}" @selected($account->hasRole($role->name))>{{ $role->label }}</option>@endforeach</select></form></td>
                    <td>{{ $account->studentProfile?->schoolClass?->name ?? '-' }}</td>
                </tr>@empty<tr><td colspan="4" class="muted">Belum ada akun.</td></tr>@endforelse</tbody>
            </table></div>
            @if($users->hasPages())
                <nav class="pagination" aria-label="Pagination akun">
                    <span>Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>
                    <div class="pagination-pages">
                        @if($users->onFirstPage())<span class="disabled">←</span>@else<a href="{{ $users->previousPageUrl() }}" aria-label="Halaman sebelumnya">←</a>@endif
                        @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                            @if($page === $users->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
                        @endforeach
                        @if($users->hasMorePages())<a href="{{ $users->nextPageUrl() }}" aria-label="Halaman berikutnya">→</a>@else<span class="disabled">→</span>@endif
                    </div>
                </nav>
            @endif
        </section>
    @elseif($section === 'create')
        @php
            $selectedRole = $roles->firstWhere('id', (int) old('role_id')) ?? $roles->first();
            $selectedRoleName = $selectedRole?->name;
        @endphp
        <section class="panel" style="max-width:900px">
            <form class="stack" method="post" action="{{ route('admin.users.store') }}">@csrf
                <div class="form-grid">
                    <label>Nama<input name="name" value="{{ old('name') }}" required></label>
                    <label>Username<input name="username" value="{{ old('username') }}" placeholder="nama.siswa"></label>
                    <label>Email <span class="field-help">Opsional jika ada username</span><input name="email" type="email" value="{{ old('email') }}"></label>
                    <label>Password awal<input name="password" type="password" required minlength="8"></label>
                    <label>Role<select id="account-role" name="role_id" required>@foreach($roles as $role)<option value="{{ $role->id }}" data-role="{{ $role->name }}" @selected($selectedRole?->id === $role->id)>{{ $role->label }}</option>@endforeach</select></label>
                    <label class="role-fields" data-role-fields="student" @if($selectedRoleName !== 'student') hidden @endif>Kelas siswa<select name="school_class_id" data-required="true"><option value="">Pilih kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
                    <label class="role-fields" data-role-fields="student" @if($selectedRoleName !== 'student') hidden @endif>NIS / ID siswa<input name="nis" value="{{ old('nis') }}" data-required="true" placeholder="Nomor induk siswa"></label>
                    <label class="role-fields" data-role-fields="teacher" @if($selectedRoleName !== 'teacher') hidden @endif>NIP guru<input name="employee_number" value="{{ old('employee_number') }}" data-required="true" placeholder="Nomor induk pegawai"></label>
                    <label class="role-fields" data-role-fields="parent" @if($selectedRoleName !== 'parent') hidden @endif>No. HP orang tua<input name="phone" value="{{ old('phone') }}" placeholder="08... atau 628..."></label>
                </div>
                <p class="muted">Akun baru wajib mengganti password saat login pertama.</p>
                <div class="actions"><button class="btn primary" type="submit">Buat akun</button><a class="btn" href="{{ route('admin.users') }}">Batal</a></div>
            </form>
        </section>
        <script>
            (() => {
                const role = document.getElementById('account-role');
                const groups = document.querySelectorAll('[data-role-fields]');
                const syncRoleFields = () => {
                    const selectedRole = role?.selectedOptions[0]?.dataset.role;
                    groups.forEach((group) => {
                        const visible = group.dataset.roleFields === selectedRole;
                        group.hidden = !visible;
                        group.querySelectorAll('input, select, textarea').forEach((field) => {
                            field.disabled = !visible;
                            field.required = visible && field.dataset.required === 'true';
                        });
                    });
                };
                role?.addEventListener('change', syncRoleFields);
                syncRoleFields();
            })();
        </script>
    @elseif($section === 'import')
        <div class="grid two">
        <section class="panel">
            <span class="eyebrow">Direkomendasikan</span><h2 style="margin-top:7px">Upload Excel / CSV</h2>
            <form class="stack" method="post" enctype="multipart/form-data" action="{{ route('admin.users.import-spreadsheet') }}">@csrf
                <label>File akun<input name="spreadsheet" type="file" accept=".xlsx,.csv" required><span class="field-help">Format XLSX atau CSV, maksimal 5 MB dan 1.000 baris.</span></label>
                <label>Role seluruh baris<select name="role_id" required>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->label }}</option>@endforeach</select></label>
                <div class="alert" style="margin:0"><strong>Ya, data dibuat otomatis.</strong><br>Jika role Siswa dipilih, sistem membuat akun login, profil siswa, NIS, penempatan kelas, dan riwayat kelas sekaligus. Header wajib: <code>nama</code>, <code>nis</code>, <code>kelas</code>. Username dan email boleh dikosongkan.</div>
                <button class="btn primary" type="submit">Import file</button>
            </form>
        </section>
        <section class="panel">
            <h2>Import dari Google Sheet</h2>
            <form class="stack" method="post" action="{{ route('admin.users.import-google-sheet') }}">@csrf
                <label>Link Google Sheet<input name="sheet_url" type="url" value="{{ old('sheet_url') }}" placeholder="https://docs.google.com/spreadsheets/d/..." required></label>
                <label>Role seluruh baris<select name="role_id" required>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->label }}</option>@endforeach</select></label>
                <div class="alert" style="margin:0">Header yang dikenali: <code>nama</code>, <code>username</code>, <code>email</code>, <code>nis</code>, <code>kelas</code>, <code>nip</code>, <code>phone</code>. Password awal: <strong>{{ $importDefaultPassword }}</strong>.</div>
                <button class="btn primary" type="submit">Import akun</button>
            </form>
            @if(session('import_errors'))<details style="margin-top:16px"><summary>Baris yang dilewati</summary><ul>@foreach(session('import_errors') as $error)<li>{{ $error }}</li>@endforeach</ul></details>@endif
        </section>
        </div>
    @elseif($section === 'roles')
        <div class="grid two">
            <section class="panel">
                <h2>Buat role baru</h2>
                <form class="stack" method="post" action="{{ route('admin.roles.store') }}">@csrf
                    <div class="form-grid"><label>Nama sistem<input name="name" placeholder="kepala_sekolah" required></label><label>Label<input name="label" placeholder="Kepala Sekolah" required></label></div>
                    <div class="permission-grid">@foreach($permissions as $permission)<label><span><input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" style="width:auto"> {{ $permission->label }}</span></label>@endforeach</div>
                    <button class="btn primary" type="submit">Buat role</button>
                </form>
            </section>
            <section class="panel">
                <h2>Role tersedia</h2>
                <div class="role-list">@foreach($roles as $role)<details><summary><strong>{{ $role->label }}</strong> <span>{{ $role->permissions->count() }} izin</span></summary>
                    <form class="stack" method="post" action="{{ route('admin.roles.update', $role) }}">@csrf
                        <label>Label<input name="label" value="{{ $role->label }}" required></label>
                        <div class="permission-grid">@foreach($permissions as $permission)<label><span><input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" style="width:auto" @checked($role->permissions->contains($permission))> {{ $permission->label }}</span></label>@endforeach</div>
                        <button class="btn" type="submit">Simpan izin</button>
                    </form>
                </details>@endforeach</div>
            </section>
        </div>
    @else
        <section class="panel" style="max-width:760px">
            <h2>Hubungkan orang tua ke siswa</h2>
            <form class="stack" method="post" action="{{ route('admin.users.link-parent') }}">@csrf
                <label>Orang tua<select name="parent_user_id" required>@foreach($parents as $parent)<option value="{{ $parent->id }}">{{ $parent->name }}</option>@endforeach</select></label>
                <label>Siswa<select name="student_user_id" required>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->name }}</option>@endforeach</select></label>
                <label>Relasi<input name="relationship" value="parent" required></label>
                <button class="btn primary" type="submit">Hubungkan akun</button>
            </form>
        </section>
    @endif
</x-layouts.app>
