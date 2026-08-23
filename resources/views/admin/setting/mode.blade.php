{{-- resources/views/admin/setting/mode.blade.php --}}
@extends('layouts.app')

@section('title', 'Setting - Mode')
@section('page-title', 'Setting')

@section('content')
<div x-data="settingModePage({{ $currentModeId ?? 'null' }})" class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-pos-900">Pengaturan Aplikasi</h2>
        <p class="mt-1 text-sm text-slate-500">Kelola parameter dan konfigurasi umum yang digunakan aplikasi.</p>
    </div>
    
    <div class="rounded-3xl border border-pos-200/60 bg-white shadow-sm">
        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-pos-50 text-pos-500">
                    <i class="mdi mdi-swap-horizontal text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-pos-900">Mode Transaksi</p>
                    <p class="mt-0.5 max-w-md text-sm text-slate-500">
                        Menentukan apakah transaksi memotong stok (Complex Mode) atau tidak (Simple Mode). Hanya satu mode yang bisa aktif dalam satu waktu.
                    </p>
                </div>
            </div>
            
            <div class="w-full shrink-0 sm:w-64">
                <select
                    x-model="selectedModeId"
                    @change="confirmChange()"
                    :disabled="saving"
                    class="w-full rounded-xl border-slate-300 py-2.5 pl-4 pr-9 text-sm font-medium text-pos-900 focus:border-pos-500 focus:ring-pos-500 disabled:opacity-50"
                >
                    @foreach($modes as $mode)
                        <option value="{{ $mode->id }}">{{ $mode->mode }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="border-t border-pos-200/60 bg-pos-50/50 px-6 py-4">
            <p class="flex items-center gap-2 text-sm text-pos-900">
                <i class="mdi mdi-information-outline text-base text-pos-500"></i>
                Saat ini menggunakan
                <span class="font-semibold" x-text="activeModeName"></span>
            </p>
        </div>
    </div>
    
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
            @click="cancelChange()"
        ></div>

        <div
            x-show="confirmOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative z-10 w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-50">
                <i class="mdi mdi-swap-horizontal-bold text-2xl text-amber-500"></i>
            </div>
            <h3 class="mt-4 text-lg font-bold text-pos-900">Ganti mode transaksi?</h3>
            <p class="mt-1 text-sm text-slate-500">
                Mode akan diubah menjadi <span class="font-semibold text-pos-900" x-text="pendingModeName"></span>.
                Perubahan ini berlaku untuk seluruh transaksi baru setelahnya.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="cancelChange()"
                    class="rounded-full border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                >Batal</button>
                <button
                    @click="applyChange()"
                    class="rounded-full bg-pos-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-pos-900"
                >Ya, Ganti</button>
            </div>
        </div>
    </div>
    </template>
    
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
function settingModePage(initialModeId) {
    return {
        modes: @json($modes->map(fn ($m) => ['id' => $m->id, 'mode' => $m->mode])),

        activeModeId: initialModeId,
        selectedModeId: initialModeId,
        pendingModeId: null,

        confirmOpen: false,
        saving: false,

        toast: { show: false, message: '', type: 'success' },

        routes: {
            update: "{{ route('admin.setting.mode.update') }}",
        },

        csrfToken: document.querySelector('meta[name="csrf-token"]').content,

        get activeModeName() {
            const found = this.modes.find((m) => m.id === this.activeModeId);
            return found ? found.mode : '-';
        },

        get pendingModeName() {
            const found = this.modes.find((m) => m.id === this.pendingModeId);
            return found ? found.mode : '-';
        },

        confirmChange() {
            if (this.selectedModeId === this.activeModeId) return;
            this.pendingModeId = this.selectedModeId;
            this.confirmOpen = true;
        },

        cancelChange() {
            this.confirmOpen = false;
            this.selectedModeId = this.activeModeId;
            this.pendingModeId = null;
        },

        async applyChange() {
            this.saving = true;
            this.confirmOpen = false;

            try {
                const res = await fetch(this.routes.update, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ mode_id: this.pendingModeId }),
                });

                const json = await res.json();

                if (!json.success) {
                    this.showToast(json.message || 'Terjadi kesalahan.', 'error');
                    this.selectedModeId = this.activeModeId;
                    return;
                }

                this.activeModeId = json.mode_id;
                this.selectedModeId = json.mode_id;
                this.showToast(json.message, 'success');
            } catch (err) {
                this.showToast('Tidak dapat terhubung ke server.', 'error');
                this.selectedModeId = this.activeModeId;
            } finally {
                this.saving = false;
                this.pendingModeId = null;
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