{{-- resources/views/admin/pegawai/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('page-title', 'Data Pegawai')

@section('content')
<div x-data="pegawaiPage()" x-init="init()" class="space-y-6">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-bold text-pos-900">Kelola Pegawai &amp; Akun</h2>
            <p class="mt-1 text-sm text-slate-500">Menambahkan pegawai baru akan otomatis membuat akun login untuknya.</p>
        </div>

        <button
            type="button"
            @click="openCreateModal()"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-pos-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-pos-900 hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-pos-500 focus:ring-offset-2"
        >
            <i class="mdi mdi-account-plus text-lg"></i>
            Tambah Pegawai
        </button>
    </div>

    {{-- ==================== CARD TABEL ==================== --}}
    <div class="rounded-3xl border border-pos-200/60 bg-white shadow-sm">
        
        <div class="flex flex-row items-center justify-between gap-8 border-b border-pos-200/60 p-6">
            <div class="flex shrink-0 items-center gap-3 whitespace-nowrap text-sm text-slate-500">
                <label for="per-page-select">Tampilkan</label>
                <select
                    id="per-page-select"
                    x-model.number="perPage"
                    @change="page = 1; load()"
                    class="rounded-full border-slate-300 py-2 pl-4 pr-9 text-sm text-slate-700 focus:border-pos-500 focus:ring-pos-500"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>baris</span>
            </div>

            <div class="relative w-full max-w-md">
                <i class="mdi mdi-magnify pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.350ms="page = 1; load()"
                    placeholder="Cari NIP, nama, username, group..."
                    class="w-full rounded-full border-slate-300 py-3 pl-11 pr-5 text-sm placeholder-slate-400 focus:border-pos-500 focus:ring-pos-500"
                >
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto px-2">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-pos-50 text-left text-xs font-semibold uppercase tracking-wide text-pos-900">
                        <th class="cursor-pointer select-none rounded-tl-2xl px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('nip')">
                            <span class="inline-flex items-center gap-2">NIP <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="cursor-pointer select-none px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('nama_pegawai')">
                            <span class="inline-flex items-center gap-2">Nama Pegawai <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="px-6 py-4">Jenis Kelamin</th>
                        <th class="cursor-pointer select-none px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('username')">
                            <span class="inline-flex items-center gap-2">Username <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="cursor-pointer select-none px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('nama_group')">
                            <span class="inline-flex items-center gap-2">Group <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="px-6 py-4">Status</th>
                        <th class="rounded-tr-2xl px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pos-200/40">
                    {{-- Loading state --}}
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-slate-400">
                                <i class="mdi mdi-loading mdi-spin text-2xl"></i>
                                <p class="mt-3 text-sm">Memuat data...</p>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && rows.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-slate-400">
                                <i class="mdi mdi-account-search-outline text-3xl"></i>
                                <p class="mt-3 text-sm">Tidak ada data ditemukan.</p>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-for="row in rows" :key="row.pegawai_id">
                        <tr class="transition-colors hover:bg-pos-50/60" x-show="!loading">
                            <td class="px-6 py-4 text-slate-600" x-text="row.nip"></td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-pos-900" x-text="row.nama_pegawai"></span>
                                <span
                                    x-show="row.is_superadmin"
                                    class="ml-2 inline-block rounded-full bg-pos-900 px-3 py-1 align-middle text-[10px] font-semibold tracking-wide text-white"
                                >SUPERADMIN</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600" x-text="row.jenis_kelamin"></td>
                            <td class="px-6 py-4 text-slate-600" x-text="row.username"></td>
                            <td class="px-6 py-4 text-slate-600" x-text="row.nama_group"></td>
                            <td class="px-6 py-4">
                                <span
                                    x-show="row.is_active"
                                    class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700"
                                ><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif</span>
                                <span
                                    x-show="!row.is_active"
                                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-500"
                                ><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Nonaktif</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="openEditModal(row.pegawai_id)"
                                        title="Edit"
                                        class="flex h-10 w-10 items-center justify-center rounded-full text-pos-500 transition-colors hover:bg-pos-50"
                                    ><i class="mdi mdi-pencil-outline text-lg"></i></button>
                                    <button
                                        @click="confirmDelete(row.pegawai_id, row.nama_pegawai)"
                                        title="Hapus"
                                        class="flex h-10 w-10 items-center justify-center rounded-full text-red-500 transition-colors hover:bg-red-50"
                                    ><i class="mdi mdi-trash-can-outline text-lg"></i></button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Footer: info + pagination --}}
        <div class="flex flex-row items-center justify-between gap-4 border-t border-pos-200/60 p-6">
            <p class="text-sm text-slate-500" x-text="infoText"></p>

            <div class="flex items-center gap-2">
                <button
                    @click="if (page > 1) { page--; load(); }"
                    :disabled="page === 1"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-pos-50 disabled:opacity-30 disabled:hover:bg-transparent"
                ><i class="mdi mdi-chevron-left"></i></button>

                <template x-for="p in pageButtons" :key="p">
                    <button
                        @click="page = p; load()"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-medium transition-colors"
                        :class="p === page ? 'bg-pos-500 text-white' : 'text-slate-600 hover:bg-pos-50'"
                        x-text="p"
                    ></button>
                </template>

                <button
                    @click="if (page < totalPages) { page++; load(); }"
                    :disabled="page === totalPages"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-pos-50 disabled:opacity-30 disabled:hover:bg-transparent"
                ><i class="mdi mdi-chevron-right"></i></button>
            </div>
        </div>
    </div>

    {{-- ==================== MODAL FORM (create & edit) ====================
         x-teleport memindahkan elemen ini ke akhir <body> saat runtime, supaya
         `fixed inset-0` benar-benar relatif ke viewport (bukan ke <main> yang
         overflow-y-auto, yang tanpa teleport bisa membuat backdrop tidak
         menutupi topbar/sidebar). --}}
    <template x-teleport="body">
    <div
        x-show="modalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            x-show="modalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
            @click="modalOpen = false"
        ></div>

        <div
            x-show="modalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 max-h-[88vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
        >
            {{-- Header modal --}}
            <div class="sticky top-0 z-10 flex items-center justify-between rounded-t-2xl border-b border-pos-200/60 bg-pos-50 px-6 py-4">
                <h3 class="text-lg font-bold text-pos-900" x-text="isEdit ? 'Edit Pegawai' : 'Tambah Pegawai'"></h3>
                <button
                    type="button"
                    @click="modalOpen = false"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-white hover:text-pos-900"
                ><i class="mdi mdi-close text-xl"></i></button>
            </div>

            <form @submit.prevent="submitForm()" class="px-6 py-6">
                <div class="space-y-7">
                    {{-- Section: Data Pegawai --}}
                    <div>
                        <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-pos-900">
                            <i class="mdi mdi-badge-account-outline text-base text-pos-500"></i>
                            Data Pegawai
                        </h4>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div >
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                                <input
                                    type="text"
                                    x-model="form.nama_pegawai"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                                <p class="mt-1 text-xs text-red-600" x-text="errors.nama_pegawai?.[0]"></p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                                <select
                                    x-model="form.jenis_kelamin"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-text="errors.jenis_kelamin?.[0]"></p>
                            </div>

                            

                            <div class="flex items-center gap-2 sm:col-span-2">
                                <input
                                    type="checkbox"
                                    x-model="form.pegawai_is_active"
                                    id="pegawai_is_active"
                                    class="h-4 w-4 rounded border-slate-300 text-pos-500 focus:ring-pos-500"
                                >
                                <label for="pegawai_is_active" class="text-sm text-slate-700">Pegawai aktif</label>
                            </div>
                        </div>
                    </div>

                    <hr class="border-pos-200/60">

                    {{-- Section: Akun Login --}}
                    <div>
                        <h4 class="mb-4 flex items-center gap-2 text-sm font-semibold text-pos-900">
                            <i class="mdi mdi-account-key-outline text-base text-pos-500"></i>
                            Akun Login
                        </h4>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Username</label>
                                <input
                                    type="text"
                                    x-model="form.username"
                                    autocomplete="off"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                                <p class="mt-1 text-xs text-red-600" x-text="errors.username?.[0]"></p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Group / Hak Akses</label>
                                <select
                                    x-model="form.groupfk"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                                    <option value="">-- Pilih Group --</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->nama_group }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-text="errors.groupfk?.[0]"></p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                    Password
                                    <span x-show="isEdit" class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
                                </label>
                                <input
                                    type="password"
                                    x-model="form.password"
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                                <p class="mt-1 text-xs text-red-600" x-text="errors.password?.[0]"></p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                                <input
                                    type="password"
                                    x-model="form.password_confirmation"
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                            </div>

                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    x-model="form.user_is_active"
                                    id="user_is_active"
                                    class="h-4 w-4 rounded border-slate-300 text-pos-500 focus:ring-pos-500"
                                >
                                <label for="user_is_active" class="text-sm text-slate-700">Akun aktif</label>
                            </div>

                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    x-model="form.is_superadmin"
                                    id="is_superadmin"
                                    class="h-4 w-4 rounded border-slate-300 text-pos-500 focus:ring-pos-500"
                                >
                                <label for="is_superadmin" class="text-sm text-slate-700">Jadikan superadmin</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer form --}}
                <div class="mt-7 flex items-center justify-end gap-3 border-t border-pos-200/60 pt-5">
                    <button
                        type="button"
                        @click="modalOpen = false"
                        class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                    >Batal</button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 rounded-full bg-pos-500 px-6 py-2.5 text-sm font-semibold text-white transition-all hover:bg-pos-900 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <i class="mdi text-lg" :class="submitting ? 'mdi-loading mdi-spin' : 'mdi-content-save-outline'"></i>
                        <span x-text="submitting ? 'Menyimpan...' : (isEdit ? 'Perbarui' : 'Simpan')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- ==================== TOAST NOTIFIKASI (pengganti alert) ====================
         Juga di-teleport supaya posisinya fixed relatif ke viewport, bukan ke <main>. --}}
    <template x-teleport="body">
    <div
        x-show="toast.show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed bottom-6 right-6 z-[60] flex items-center gap-3 rounded-full px-6 py-3.5 text-sm font-medium text-white shadow-lg"
        :class="toast.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'"
    >
        <i class="mdi text-lg" :class="toast.type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'"></i>
        <span x-text="toast.message"></span>
    </div>
    </template>

    {{-- ==================== KONFIRMASI HAPUS ==================== --}}
    <template x-teleport="body">
    <div
        x-show="confirmOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            x-show="confirmOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
            @click="confirmOpen = false"
        ></div>

        <div
            x-show="confirmOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative z-10 w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50">
                <i class="mdi mdi-trash-can-outline text-2xl text-red-500"></i>
            </div>
            <h3 class="mt-4 text-lg font-bold text-pos-900">Hapus pegawai ini?</h3>
            <p class="mt-1 text-sm text-slate-500">
                <span x-text="confirmTargetName"></span> beserta akun login-nya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="confirmOpen = false"
                    class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                >Batal</button>
                <button
                    @click="doDelete()"
                    class="rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700"
                >Ya, Hapus</button>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function pegawaiPage() {
    return {
        // ---- State tabel ----
        rows: [],
        loading: true,
        page: 1,
        perPage: 10,
        search: '',
        sortColumn: 'nama_pegawai',
        sortDir: 'asc',
        recordsFiltered: 0,
        infoText: 'Memuat...',

        // ---- State modal form ----
        modalOpen: false,
        isEdit: false,
        submitting: false,
        errors: {},
        form: {
            pegawai_id: '',
            nama_pegawai: '',
            jenis_kelamin: '',
            pegawai_is_active: true,
            username: '',
            groupfk: '',
            password: '',
            password_confirmation: '',
            user_is_active: true,
            is_superadmin: false,
        },

        // ---- State toast ----
        toast: { show: false, message: '', type: 'success' },

        // ---- State konfirmasi hapus ----
        confirmOpen: false,
        confirmTargetId: null,
        confirmTargetName: '',

        routes: {
            data: "{{ route('admin.pegawai.data') }}",
            store: "{{ route('admin.pegawai.store') }}",
            show: (id) => `{{ url('admin/pegawai') }}/${id}`,
            update: (id) => `{{ url('admin/pegawai') }}/${id}`,
            destroy: (id) => `{{ url('admin/pegawai') }}/${id}`,
        },

        csrfToken: document.querySelector('meta[name="csrf-token"]').content,

        get totalPages() {
            return Math.max(1, Math.ceil(this.recordsFiltered / this.perPage));
        },

        get pageButtons() {
            const maxButtons = 5;
            let start = Math.max(1, this.page - Math.floor(maxButtons / 2));
            let end = Math.min(this.totalPages, start + maxButtons - 1);
            start = Math.max(1, end - maxButtons + 1);

            const buttons = [];
            for (let p = start; p <= end; p++) buttons.push(p);
            return buttons;
        },

        init() {
            this.load();
        },

        sort(column) {
            if (this.sortColumn === column) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDir = 'asc';
            }
            this.load();
        },

        async load() {
            this.loading = true;

            const columnMap = ['nip', 'nama_pegawai', 'jenis_kelamin', 'username', 'nama_group', 'is_active'];
            const params = new URLSearchParams({
                draw: 1,
                start: (this.page - 1) * this.perPage,
                length: this.perPage,
                'search[value]': this.search,
                'order[0][column]': columnMap.indexOf(this.sortColumn),
                'order[0][dir]': this.sortDir,
            });

            try {
                const res = await fetch(`${this.routes.data}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();

                this.rows = json.data;
                this.recordsFiltered = json.recordsFiltered;
                this.page = Math.min(this.page, this.totalPages);

                const start = this.recordsFiltered === 0 ? 0 : (this.page - 1) * this.perPage + 1;
                const end = Math.min(this.page * this.perPage, this.recordsFiltered);
                this.infoText = `Menampilkan ${start}-${end} dari ${this.recordsFiltered} data`;
            } catch (err) {
                this.showToast('Gagal memuat data.', 'error');
            } finally {
                this.loading = false;
            }
        },

        resetForm() {
            this.form = {
                pegawai_id: '',
                nama_pegawai: '',
                jenis_kelamin: '',
                pegawai_is_active: true,
                username: '',
                groupfk: '',
                password: '',
                password_confirmation: '',
                user_is_active: true,
                is_superadmin: false,
            };
            this.errors = {};
        },

        openCreateModal() {
            this.resetForm();
            this.isEdit = false;
            this.modalOpen = true;
        },

        async openEditModal(id) {
            try {
                const res = await fetch(this.routes.show(id), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!res.ok) {
                    this.showToast('Tidak bisa memuat data pegawai.', 'error');
                    return;
                }

                const json = await res.json();

                this.resetForm();
                this.isEdit = true;
                this.form.pegawai_id = json.pegawai.id;
                this.form.nama_pegawai = json.pegawai.nama_pegawai;
                this.form.jenis_kelamin = json.pegawai.jenis_kelamin;
                this.form.pegawai_is_active = !!json.pegawai.is_active;

                if (json.user) {
                    this.form.username = json.user.username;
                    this.form.groupfk = json.user.groupfk;
                    this.form.is_superadmin = !!json.user.is_superadmin;
                    this.form.user_is_active = !!json.user.is_active;
                }

                this.modalOpen = true;
            } catch (err) {
                this.showToast('Tidak dapat terhubung ke server.', 'error');
            }
        },

        async submitForm() {
            this.errors = {};
            this.submitting = true;

            const isEdit = this.isEdit;
            const id = this.form.pegawai_id;
            const url = isEdit ? this.routes.update(id) : this.routes.store;

            const formData = new FormData();
            Object.entries(this.form).forEach(([key, value]) => {
                if (typeof value === 'boolean') {
                    formData.append(key, value ? '1' : '0');
                } else {
                    formData.append(key, value ?? '');
                }
            });

            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const json = await res.json();

                if (res.status === 422) {
                    this.errors = json.errors;
                    return;
                }

                if (!json.success) {
                    this.showToast(json.message || 'Terjadi kesalahan.', 'error');
                    return;
                }

                this.modalOpen = false;
                this.showToast(json.message, 'success');
                this.load();
            } catch (err) {
                this.showToast('Tidak dapat terhubung ke server.', 'error');
            } finally {
                this.submitting = false;
            }
        },

        confirmDelete(id, name) {
            this.confirmTargetId = id;
            this.confirmTargetName = name;
            this.confirmOpen = true;
        },

        async doDelete() {
            const id = this.confirmTargetId;
            this.confirmOpen = false;

            try {
                const res = await fetch(this.routes.destroy(id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ _method: 'DELETE' }),
                });

                const json = await res.json();

                if (!json.success) {
                    this.showToast(json.message || 'Terjadi kesalahan.', 'error');
                    return;
                }

                this.showToast(json.message, 'success');
                this.load();
            } catch (err) {
                this.showToast('Tidak dapat terhubung ke server.', 'error');
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        },
    };
}
</script>
@endpush