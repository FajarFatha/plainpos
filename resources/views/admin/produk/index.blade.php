{{-- resources/views/admin/produk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Produk')
@section('page-title', 'Produk')

@section('content')
<div x-data="produkPage()" x-init="init()" class="space-y-6">

    {{-- ==================== HEADER: judul kiri, tombol kanan (1 row) ==================== --}}
    <div class="flex flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-bold text-pos-900">Master Produk</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola daftar produk yang dijual beserta harga dan stoknya.</p>
        </div>

        <button
            type="button"
            @click="openCreateModal()"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-pos-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-pos-900 hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-pos-500 focus:ring-offset-2"
        >
            <i class="mdi mdi-package-variant-closed-plus text-lg"></i>
            Tambah Produk
        </button>
    </div>

    {{-- ==================== CARD TABEL ==================== --}}
    <div class="rounded-3xl border border-pos-200/60 bg-white shadow-sm">

        {{-- Toolbar: per-page kiri, search kanan lebar --}}
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
                    placeholder="Cari nama produk..."
                    class="w-full rounded-full border-slate-300 py-3 pl-11 pr-5 text-sm placeholder-slate-400 focus:border-pos-500 focus:ring-pos-500"
                >
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto px-2">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-pos-50 text-left text-xs font-semibold uppercase tracking-wide text-pos-900">
                        <th class="rounded-tl-2xl px-6 py-4">Gambar</th>
                        <th class="cursor-pointer select-none px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('nama_produk')">
                            <span class="inline-flex items-center gap-2">Nama Produk <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="cursor-pointer select-none px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('harga_jual')">
                            <span class="inline-flex items-center gap-2">Harga Jual <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="cursor-pointer select-none px-6 py-4 transition-colors hover:bg-pos-200/40" @click="sort('stok')">
                            <span class="inline-flex items-center gap-2">Stok <i class="mdi mdi-unfold-more-horizontal text-sm opacity-50"></i></span>
                        </th>
                        <th class="px-6 py-4">Status</th>
                        <th class="rounded-tr-2xl px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pos-200/40">
                    {{-- Loading state --}}
                    <template x-if="loading">
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center text-slate-400">
                                <i class="mdi mdi-loading mdi-spin text-2xl"></i>
                                <p class="mt-3 text-sm">Memuat data...</p>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && rows.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center text-slate-400">
                                <i class="mdi mdi-package-variant text-3xl"></i>
                                <p class="mt-3 text-sm">Belum ada produk. Klik "Tambah Produk" untuk mulai menambahkan.</p>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-for="row in rows" :key="row.id">
                        <tr class="transition-colors hover:bg-pos-50/60" x-show="!loading">
                            <td class="px-6 py-3">
                                <template x-if="row.gambar_url">
                                    <img :src="row.gambar_url" class="h-12 w-12 rounded-xl object-cover ring-1 ring-pos-200/60">
                                </template>
                                <template x-if="!row.gambar_url">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pos-50 text-pos-200">
                                        <i class="mdi mdi-image-off-outline text-xl"></i>
                                    </div>
                                </template>
                            </td>
                            <td class="px-6 py-4 font-medium text-pos-900" x-text="row.nama_produk"></td>
                            <td class="px-6 py-4 text-slate-600" x-text="formatRupiah(row.harga_jual)"></td>
                            <td class="px-6 py-4">
                                <span
                                    class="font-medium"
                                    :class="row.stok > 0 ? 'text-slate-600' : 'text-red-500'"
                                    x-text="row.stok"
                                ></span>
                            </td>
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
                                        @click="openEditModal(row.id)"
                                        title="Edit"
                                        class="flex h-10 w-10 items-center justify-center rounded-full text-pos-500 transition-colors hover:bg-pos-50"
                                    ><i class="mdi mdi-pencil-outline text-lg"></i></button>
                                    <button
                                        @click="confirmDelete(row.id, row.nama_produk)"
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

    {{-- ==================== MODAL FORM (create & edit) ==================== --}}
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
                <h3 class="text-lg font-bold text-pos-900" x-text="isEdit ? 'Edit Produk' : 'Tambah Produk'"></h3>
                <button
                    type="button"
                    @click="modalOpen = false"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-white hover:text-pos-900"
                ><i class="mdi mdi-close text-xl"></i></button>
            </div>

            <form @submit.prevent="submitForm()" class="px-6 py-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-[220px_1fr]">

                    {{-- Upload & preview gambar --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Gambar Produk</label>

                        <div
                            @click="$refs.fileInput.click()"
                            @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false"
                            @drop.prevent="handleDrop($event)"
                            class="group relative flex aspect-square w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed transition-colors"
                            :class="dragOver ? 'border-pos-500 bg-pos-50' : 'border-pos-200 bg-pos-50/50 hover:border-pos-500 hover:bg-pos-50'"
                        >
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="absolute inset-0 h-full w-full object-cover">
                            </template>

                            <template x-if="previewUrl">
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/0 opacity-0 transition-opacity group-hover:bg-slate-900/40 group-hover:opacity-100">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1.5 text-xs font-medium text-pos-900">
                                        <i class="mdi mdi-pencil-outline"></i> Ganti gambar
                                    </span>
                                </div>
                            </template>

                            <template x-if="!previewUrl">
                                <div class="flex flex-col items-center gap-1.5 px-3 text-center text-pos-500">
                                    <i class="mdi mdi-image-plus-outline text-3xl"></i>
                                    <span class="text-xs font-medium">Klik atau tarik gambar ke sini</span>
                                    <span class="text-[11px] text-slate-400">JPG, PNG, WEBP — maks 2MB</span>
                                </div>
                            </template>

                            <button
                                type="button"
                                x-show="previewUrl"
                                @click.stop="removeImage()"
                                class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-red-500 shadow-sm transition-colors hover:bg-white"
                            ><i class="mdi mdi-close text-base"></i></button>
                        </div>

                        <input
                            type="file"
                            x-ref="fileInput"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                            @change="handleFileSelect($event)"
                        >
                        <p class="mt-1 text-xs text-red-600" x-text="errors.gambar?.[0]"></p>
                    </div>

                    {{-- Field-field lainnya --}}
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Produk</label>
                            <input
                                type="text"
                                x-model="form.nama_produk"
                                class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                            >
                            <p class="mt-1 text-xs text-red-600" x-text="errors.nama_produk?.[0]"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Harga Jual</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        x-model="form.harga_jual"
                                        class="w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-4 text-sm focus:border-pos-500 focus:ring-pos-500"
                                    >
                                </div>
                                <p class="mt-1 text-xs text-red-600" x-text="errors.harga_jual?.[0]"></p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Stok Awal</label>
                                <input
                                    type="number"
                                    min="0"
                                    x-model="form.stok"
                                    class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-pos-500 focus:ring-pos-500"
                                >
                                <p class="mt-1 text-xs text-red-600" x-text="errors.stok?.[0]"></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input
                                type="checkbox"
                                x-model="form.is_active"
                                id="produk_is_active"
                                class="h-4 w-4 rounded border-slate-300 text-pos-500 focus:ring-pos-500"
                            >
                            <label for="produk_is_active" class="text-sm text-slate-700">Produk aktif (tampil di kasir)</label>
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

    {{-- ==================== TOAST NOTIFIKASI ==================== --}}
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
            <h3 class="mt-4 text-lg font-bold text-pos-900">Hapus produk ini?</h3>
            <p class="mt-1 text-sm text-slate-500">
                <span x-text="confirmTargetName"></span> beserta gambar dan data stoknya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="confirmOpen = false"
                    class="rounded-full border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                >Batal</button>
                <button
                    @click="doDelete()"
                    class="rounded-full bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700"
                >Ya, Hapus</button>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function produkPage() {
    return {
        // ---- State tabel ----
        rows: [],
        loading: true,
        page: 1,
        perPage: 10,
        search: '',
        sortColumn: 'nama_produk',
        sortDir: 'asc',
        recordsFiltered: 0,
        infoText: 'Memuat...',

        // ---- State modal form ----
        modalOpen: false,
        isEdit: false,
        submitting: false,
        errors: {},
        dragOver: false,
        previewUrl: null,
        form: {
            id: '',
            nama_produk: '',
            harga_jual: '',
            stok: 0,
            is_active: true,
            gambarFile: null,
        },

        // ---- State toast ----
        toast: { show: false, message: '', type: 'success' },

        // ---- State konfirmasi hapus ----
        confirmOpen: false,
        confirmTargetId: null,
        confirmTargetName: '',

        routes: {
            data: "{{ route('produk.data') }}",
            store: "{{ route('produk.store') }}",
            show: (id) => `{{ url('produk') }}/${id}`,
            update: (id) => `{{ url('produk') }}/${id}`,
            destroy: (id) => `{{ url('produk') }}/${id}`,
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

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
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

            const columnMap = ['nama_produk', 'harga_jual', 'stok', 'is_active'];
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
                id: '',
                nama_produk: '',
                harga_jual: '',
                stok: 0,
                is_active: true,
                gambarFile: null,
            };
            this.previewUrl = null;
            this.errors = {};
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
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
                    this.showToast('Tidak bisa memuat data produk.', 'error');
                    return;
                }

                const json = await res.json();

                this.resetForm();
                this.isEdit = true;
                this.form.id = json.id;
                this.form.nama_produk = json.nama_produk;
                this.form.harga_jual = json.harga_jual;
                this.form.stok = json.stok;
                this.form.is_active = !!json.is_active;
                this.previewUrl = json.gambar_url;

                this.modalOpen = true;
            } catch (err) {
                this.showToast('Tidak dapat terhubung ke server.', 'error');
            }
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) this.setImageFile(file);
        },

        handleDrop(event) {
            this.dragOver = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) this.setImageFile(file);
        },

        setImageFile(file) {
            this.form.gambarFile = file;
            this.previewUrl = URL.createObjectURL(file);
        },

        removeImage() {
            this.form.gambarFile = null;
            this.previewUrl = null;
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        },

        async submitForm() {
            this.errors = {};
            this.submitting = true;

            const isEdit = this.isEdit;
            const id = this.form.id;
            const url = isEdit ? this.routes.update(id) : this.routes.store;

            const formData = new FormData();
            formData.append('nama_produk', this.form.nama_produk ?? '');
            formData.append('harga_jual', this.form.harga_jual ?? '');
            formData.append('stok', this.form.stok ?? 0);
            formData.append('is_active', this.form.is_active ? '1' : '0');

            if (this.form.gambarFile) {
                formData.append('gambar', this.form.gambarFile);
            }

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