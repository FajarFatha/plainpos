{{-- resources/views/admin/mapping-user/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mapping User')
@section('page-title', 'Mapping User')

@section('content')
<div x-data="mappingUserPage()" x-init="init()" class="space-y-6">

    {{-- ==================== CARD: pilih user + group info ==================== --}}
    <div class="rounded-3xl border border-pos-200/60 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_auto]">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Pilih User (searchable dropdown custom) --}}
                <div class="relative">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">User</label>
                    <button
                        type="button"
                        @click="userDropdownOpen = !userDropdownOpen"
                        class="flex w-full items-center justify-between rounded-xl border border-slate-300 px-4 py-2.5 text-left text-sm text-slate-700 focus:border-pos-500 focus:outline-none focus:ring-2 focus:ring-pos-500/30"
                    >
                        <span :class="selectedUser ? 'text-slate-700' : 'text-slate-400'" x-text="selectedUser ? selectedUser.label : 'Pilih User'"></span>
                        <i class="mdi mdi-chevron-down text-lg text-slate-400 transition-transform" :class="userDropdownOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <div
                        x-show="userDropdownOpen"
                        x-cloak
                        @click.outside="userDropdownOpen = false"
                        x-transition:enter="ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-pos-200/60 bg-white shadow-lg"
                    >
                        <div class="border-b border-pos-200/60 p-2">
                            <input
                                type="text"
                                x-model="userSearch"
                                @input.debounce.300ms="searchUsers()"
                                placeholder="Cari nama atau username..."
                                class="w-full rounded-lg border-slate-200 px-3 py-2 text-sm focus:border-pos-500 focus:ring-pos-500"
                                x-ref="userSearchInput"
                            >
                        </div>

                        <ul class="max-h-64 overflow-y-auto py-1">
                            <template x-if="userOptions.length === 0">
                                <li class="px-4 py-3 text-sm text-slate-400">Tidak ada user ditemukan.</li>
                            </template>
                            <template x-for="opt in userOptions" :key="opt.id">
                                <li>
                                    <button
                                        type="button"
                                        @click="selectUser(opt)"
                                        class="block w-full px-4 py-2.5 text-left text-sm text-slate-700 transition-colors hover:bg-pos-50"
                                        x-text="opt.label"
                                    ></button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- Info group saat ini (read-only) --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Group</label>
                    <div class="flex h-[42px] items-center rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-500">
                        <span x-text="selectedUser ? selectedUser.group_name : '-- Pilih user untuk melihat group --'"></span>
                    </div>
                </div>
            </div>

            {{-- Tombol simpan --}}
            <div class="flex items-end">
                <button
                    type="button"
                    @click="saveMapping()"
                    :disabled="!selectedUser || saving"
                    class="inline-flex h-[42px] items-center justify-center gap-2 rounded-xl bg-pos-500 px-6 text-sm font-semibold text-white shadow-sm transition-all hover:bg-pos-900 hover:shadow-md active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <i class="mdi text-lg" :class="saving ? 'mdi-loading mdi-spin' : 'mdi-content-save-outline'"></i>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
            </div>
        </div>

        <p x-show="!selectedUser" class="mt-3 text-sm text-slate-400">Pilih user untuk melihat dan mengatur hak akses menunya.</p>
    </div>

    {{-- ==================== CARD: Map User To Modul ==================== --}}
    <div x-show="selectedUser" x-cloak class="rounded-3xl border border-pos-200/60 bg-white p-6 shadow-sm">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-bold text-pos-900">Map User To Modul</h3>

            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-pos-50 px-4 py-1.5 text-sm font-medium text-pos-900">
                    <i class="mdi mdi-check-circle-outline"></i>
                    <span x-text="totalChecked"></span> terpilih
                </span>

                <button
                    type="button"
                    @click="selectAll()"
                    class="inline-flex items-center gap-1.5 rounded-full border border-pos-200 px-4 py-1.5 text-sm font-medium text-pos-500 transition-colors hover:bg-pos-50"
                >
                    <i class="mdi mdi-checkbox-marked-outline"></i> Select All
                </button>

                <button
                    type="button"
                    @click="deselectAll()"
                    class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-4 py-1.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50"
                >
                    <i class="mdi mdi-checkbox-blank-outline"></i> Deselect All
                </button>

                <div class="relative">
                    <i class="mdi mdi-magnify pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="text"
                        x-model="menuFilter"
                        placeholder="Cari modul..."
                        class="w-48 rounded-full border-slate-300 py-1.5 pl-9 pr-4 text-sm focus:border-pos-500 focus:ring-pos-500"
                    >
                </div>
            </div>
        </div>

        {{-- Loading state --}}
        <div x-show="loadingMenus" class="flex flex-col items-center justify-center py-16 text-slate-400">
            <i class="mdi mdi-loading mdi-spin text-3xl"></i>
            <p class="mt-3 text-sm">Memuat menu...</p>
        </div>

        {{-- Grid card menu per kategori --}}
        <div x-show="!loadingMenus" class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            <template x-for="menu in filteredMenus" :key="menu.id">
                <div class="overflow-hidden rounded-2xl border border-pos-200/60" x-show="menu.visible">

                    {{-- Header kategori --}}
                    <button
                        type="button"
                        @click="toggleCategory(menu)"
                        class="flex w-full items-center justify-between bg-pos-50 px-5 py-3.5 text-left transition-colors hover:bg-pos-200/40"
                    >
                        <span class="flex items-center gap-2.5 font-semibold text-pos-900">
                            <i
                                class="mdi text-lg"
                                :class="isCategoryFullyChecked(menu) ? 'mdi-check-circle text-pos-500' : (isCategoryPartiallyChecked(menu) ? 'mdi-minus-circle text-pos-500/60' : 'mdi-checkbox-blank-circle-outline text-slate-300')"
                                @click.stop="toggleAllInCategory(menu)"
                            ></i>
                            <span x-text="menu.realname"></span>
                        </span>
                        <i class="mdi mdi-chevron-up text-slate-400 transition-transform" :class="menu.expanded ? '' : 'rotate-180'"></i>
                    </button>

                    {{-- Daftar submenu (scrollable) --}}
                    <div x-show="menu.expanded" x-collapse class="max-h-60 overflow-y-auto p-2">
                        <template x-if="menu.children.length === 0">
                            <p class="px-3 py-3 text-sm text-slate-400">Tidak ada sub menu.</p>
                        </template>

                        <template x-for="child in menu.children" :key="child.id">
                            <div>
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-600 transition-colors hover:bg-pos-50">
                                    <input
                                        type="checkbox"
                                        x-model="child.checked"
                                        @change="onChildToggle(menu, child)"
                                        class="h-4 w-4 rounded border-slate-300 text-pos-500 focus:ring-pos-500"
                                    >
                                    <i class="mdi mdi-folder-outline text-amber-400"></i>
                                    <span x-text="child.realname"></span>
                                </label>

                                {{-- Anak level ke-3 (jika ada), indented --}}
                                <template x-if="child.children && child.children.length > 0">
                                    <div class="ml-7 space-y-0.5 border-l border-pos-200/60 pl-3">
                                        <template x-for="grandchild in child.children" :key="grandchild.id">
                                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-pos-50">
                                                <input
                                                    type="checkbox"
                                                    x-model="grandchild.checked"
                                                    @change="onChildToggle(menu, grandchild)"
                                                    class="h-4 w-4 rounded border-slate-300 text-pos-500 focus:ring-pos-500"
                                                >
                                                <i class="mdi mdi-file-outline text-slate-400"></i>
                                                <span x-text="grandchild.realname"></span>
                                            </label>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Footer tag nama modul --}}
                    <div class="border-t border-pos-200/60 px-4 py-2.5">
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-500">
                            <i class="mdi mdi-tag-outline"></i>
                            <span x-text="menu.name"></span>*
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>

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
</div>
@endsection

@push('scripts')
<script>
function mappingUserPage() {
    return {
        // ---- State pemilihan user ----
        userDropdownOpen: false,
        userSearch: '',
        userOptions: [],
        selectedUser: null,

        // ---- State menu ----
        loadingMenus: false,
        menus: [],
        menuFilter: '',
        saving: false,

        // ---- Toast ----
        toast: { show: false, message: '', type: 'success' },

        routes: {
            users: "{{ route('admin.mapping-user.users') }}",
            menus: (id) => `{{ url('admin/mapping-user') }}/${id}/menus`,
            save: (id) => `{{ url('admin/mapping-user') }}/${id}/save`,
        },

        csrfToken: document.querySelector('meta[name="csrf-token"]').content,

        get totalChecked() {
            let count = 0;
            const countDeep = (nodes) => {
                nodes.forEach((n) => {
                    if (n.checked) count++;
                    if (n.children) countDeep(n.children);
                });
            };
            countDeep(this.menus);
            return count;
        },

        get filteredMenus() {
            const keyword = this.menuFilter.trim().toLowerCase();

            return this.menus.map((menu) => {
                if (!keyword) {
                    menu.visible = true;
                    return menu;
                }

                const matchSelf = menu.realname.toLowerCase().includes(keyword);
                const matchChild = menu.children.some((c) =>
                    c.realname.toLowerCase().includes(keyword) ||
                    (c.children || []).some((gc) => gc.realname.toLowerCase().includes(keyword))
                );

                menu.visible = matchSelf || matchChild;
                if (menu.visible && matchChild) menu.expanded = true;

                return menu;
            });
        },

        init() {
            this.searchUsers();
        },

        async searchUsers() {
            const params = new URLSearchParams({ q: this.userSearch });
            try {
                const res = await fetch(`${this.routes.users}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.userOptions = await res.json();
            } catch (err) {
                this.showToast('Gagal memuat daftar user.', 'error');
            }
        },

        async selectUser(opt) {
            this.selectedUser = opt;
            this.userDropdownOpen = false;
            await this.loadMenus(opt.id);
        },

        async loadMenus(userId) {
            this.loadingMenus = true;
            this.menus = [];

            try {
                const res = await fetch(this.routes.menus(userId), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();

                this.menus = json.menus.map((menu) => ({
                    ...menu,
                    expanded: false,
                    visible: true,
                }));
            } catch (err) {
                this.showToast('Gagal memuat menu.', 'error');
            } finally {
                this.loadingMenus = false;
            }
        },

        toggleCategory(menu) {
            menu.expanded = !menu.expanded;
        },

        isCategoryFullyChecked(menu) {
            const flatten = (nodes) => nodes.flatMap((n) => [n, ...(n.children ? flatten(n.children) : [])]);
            const all = flatten(menu.children);
            return all.length > 0 && all.every((n) => n.checked);
        },

        isCategoryPartiallyChecked(menu) {
            const flatten = (nodes) => nodes.flatMap((n) => [n, ...(n.children ? flatten(n.children) : [])]);
            const all = flatten(menu.children);
            const checkedCount = all.filter((n) => n.checked).length;
            return checkedCount > 0 && checkedCount < all.length;
        },

        toggleAllInCategory(menu) {
            const shouldCheck = !this.isCategoryFullyChecked(menu);
            const setDeep = (nodes) => {
                nodes.forEach((n) => {
                    n.checked = shouldCheck;
                    if (n.children) setDeep(n.children);
                });
            };
            menu.checked = shouldCheck;
            setDeep(menu.children);
        },

        onChildToggle(menu, child) {
            if (child.checked) menu.expanded = true;
        },

        selectAll() {
            const setDeep = (nodes) => {
                nodes.forEach((n) => {
                    n.checked = true;
                    if (n.children) setDeep(n.children);
                });
            };
            setDeep(this.menus);
        },

        deselectAll() {
            const setDeep = (nodes) => {
                nodes.forEach((n) => {
                    n.checked = false;
                    if (n.children) setDeep(n.children);
                });
            };
            setDeep(this.menus);
        },

        collectCheckedIds() {
            const ids = [];
            const collect = (nodes) => {
                nodes.forEach((n) => {
                    if (n.checked) ids.push(n.id);
                    if (n.children) collect(n.children);
                });
            };
            collect(this.menus);
            return ids;
        },

        async saveMapping() {
            if (!this.selectedUser) return;

            this.saving = true;
            const menuIds = this.collectCheckedIds();

            try {
                const res = await fetch(this.routes.save(this.selectedUser.id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ menu_ids: menuIds }),
                });

                const json = await res.json();

                if (!json.success) {
                    this.showToast(json.message || 'Terjadi kesalahan.', 'error');
                    return;
                }

                this.showToast(json.message, 'success');
            } catch (err) {
                this.showToast('Tidak dapat terhubung ke server.', 'error');
            } finally {
                this.saving = false;
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