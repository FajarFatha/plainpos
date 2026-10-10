{{-- resources/views/transaksi/kasir.blade.php --}}
@extends('layouts.app')

@section('title', 'Kasir')
@section('page-title', 'Kasir')

@section('content')

<style>
    .kasir-root {
        display: flex;
        height: calc(100vh - 4rem);
        margin: -1.5rem;
        overflow: hidden;
    }

    /* ---------- Panel kiri ---------- */
    .kasir-left {
        display: flex;
        flex-direction: column;
        min-width: 320px;
        overflow: hidden;
    }
    .kasir-search-wrap { flex-shrink: 0; padding: 24px 24px 16px 24px; }
    .kasir-grid-wrap { flex: 1; overflow-y: auto; padding: 0 24px 24px 24px; }
    .kasir-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }

    /* ---------- Card produk ---------- */
    .kasir-card-body {
        display: flex;
        flex: 1;
        flex-direction: column;
        padding: 12px; 
    }
    .kasir-card-img {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        border-radius: 14px;
        background: rgb(227, 242, 253);
    }
    .kasir-card-img img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .kasir-card-info {
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 4px;
        margin-top: 12px;
        padding: 0 2px;
    }
    .kasir-card:disabled { cursor: not-allowed; opacity: .5; }

    /* ---------- Divider ---------- */
    .kasir-divider { position: relative; flex-shrink: 0; width: 8px; cursor: col-resize; user-select: none; }

    /* ---------- Panel kanan (kartu bulat) ---------- */
    .kasir-right {
        display: flex;
        flex: 1;
        min-width: 360px;
        overflow: hidden;
        padding: 24px 24px 24px 0;
    }
    .kasir-right-card {
        display: flex;
        flex: 1;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(144, 202, 249, .6);
        border-radius: 24px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
    }
    .kasir-right-head { flex-shrink: 0; padding: 24px; border-bottom: 1px solid rgba(144, 202, 249, .6); }
    .kasir-cart-list {
        flex: 1;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 16px;
        container-type: inline-size;
        container-name: kasir-cart;
    }
    .kasir-right-foot {
        flex-shrink: 0;
        padding: 24px;
        border-top: 1px solid rgba(144, 202, 249, .6);
        background: rgba(227, 242, 253, .5);
    }
    .kasir-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 24px;
        text-align: center;
    }
    
    .kasir-cart-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding: 12px 20px 12px 12px;
        border: 1px solid rgba(144, 202, 249, .6);
        border-radius: 18px;
    }
    .kasir-thumb {
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        overflow: hidden;
        border-radius: 12px;
        background: rgb(227, 242, 253);
    }
    .kasir-thumb img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .kasir-thumb-empty { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
    .kasir-cart-name { flex: 1; min-width: 0; }
    .kasir-qty {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        gap: 6px;
        padding: 4px;
        border: 1px solid rgba(144, 202, 249, .6);
        border-radius: 9999px;
    }
    .kasir-qty-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 9999px;
        color: rgb(33, 150, 243);
        transition: background-color .15s;
    }
    .kasir-qty-btn:hover { background: rgb(227, 242, 253); }
    .kasir-qty-btn:disabled { cursor: not-allowed; opacity: .3; }
    .kasir-qty-btn:disabled:hover { background: transparent; }
    .kasir-cart-end { display: flex; flex-shrink: 0; flex-direction: column; align-items: flex-end; gap: 2px; min-width: 76px; }
    .kasir-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 9999px;
        color: #ef4444;
        transition: background-color .15s;
    }
    .kasir-icon-btn:hover { background: #fef2f2; }
    
    .kasir-name {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
        line-height: 1.3;
    }
    .kasir-price-unit {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* ---------- Layout RINGKAS saat panel pesanan sempit ---------- */
    @container kasir-cart (max-width: 440px) {
        .kasir-cart-item {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr) auto;
            grid-template-areas:
                "thumb name qty"
                "thumb name end";
            align-items: center;
            column-gap: 12px;
            row-gap: 6px;
        }
        .kasir-thumb { grid-area: thumb; width: 48px; height: 48px; }
        .kasir-cart-name { grid-area: name; }
        .kasir-qty { grid-area: qty; justify-self: end; }
        .kasir-cart-end {
            grid-area: end;
            flex-direction: row;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            min-width: 0;
        }
    }
</style>

<div x-data="kasirPage()" x-init="init()" x-ref="root" class="kasir-root">

    {{-- ==================== PANEL KIRI: Daftar Produk ==================== --}}
    <div class="kasir-left" :style="`width: ${leftPct}%`">

        {{-- Search --}}
        <div class="kasir-search-wrap">
            <div class="relative">
                <i class="mdi mdi-magnify pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Cari produk..."
                    class="w-full rounded-full border-slate-300 py-3 pl-11 pr-5 text-sm placeholder-slate-400 focus:border-pos-500 focus:ring-pos-500"
                >
            </div>
        </div>

        {{-- Grid produk (scrollable) --}}
        <div class="kasir-grid-wrap">

            <div x-show="loading" class="flex flex-col items-center justify-center py-16 text-slate-400">
                <i class="mdi mdi-loading mdi-spin text-3xl"></i>
                <p class="mt-3 text-sm">Memuat produk...</p>
            </div>

            <div x-show="!loading && filteredProducts.length === 0" class="flex flex-col items-center justify-center py-16 text-slate-400">
                <i class="mdi mdi-package-variant text-3xl"></i>
                <p class="mt-3 text-sm">Produk tidak ditemukan.</p>
            </div>

            <div x-show="!loading" class="kasir-grid">
                <template x-for="p in filteredProducts" :key="p.id">
                    <button
                        type="button"
                        @click="addToCart(p)"
                        :disabled="p.stok <= 0"
                        class="kasir-card flex flex-col rounded-2xl border border-pos-200/60 bg-white text-left shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md active:scale-[0.98]"
                    >
                        <div class="kasir-card-body">
                            {{-- Gambar --}}
                            <div class="kasir-card-img">
                                <template x-if="p.gambar_url">
                                    <img :src="p.gambar_url" :alt="p.nama_produk">
                                </template>
                                <template x-if="!p.gambar_url">
                                    <div class="kasir-thumb-empty text-pos-200">
                                        <i class="mdi mdi-image-off-outline text-3xl"></i>
                                    </div>
                                </template>

                                {{-- Badge qty di cart --}}
                                <template x-if="qtyInCart(p.id) > 0">
                                    <span class="absolute right-2 top-2 flex h-6 min-w-6 items-center justify-center rounded-full bg-pos-500 px-1.5 text-xs font-bold text-white shadow-sm" x-text="qtyInCart(p.id)"></span>
                                </template>

                                {{-- Badge stok habis --}}
                                <template x-if="p.stok <= 0">
                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40">
                                        <span class="rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-slate-700">Stok Habis</span>
                                    </div>
                                </template>
                            </div>

                            {{-- Info --}}
                            <div class="kasir-card-info">
                                <p class="line-clamp-2 text-sm font-medium leading-snug text-pos-900" x-text="p.nama_produk"></p>
                                <p class="mt-auto text-sm font-semibold text-pos-500" x-text="formatRupiah(p.harga_jual)"></p>
                                <p class="text-xs text-slate-400">Stok: <span x-text="p.stok"></span></p>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ==================== DIVIDER (resizable handle) ==================== --}}
    <div @mousedown="startResize($event)" class="kasir-divider group">
        <div class="absolute inset-y-0 left-1/2 w-px -translate-x-1/2 bg-pos-200/60 transition-colors group-hover:bg-pos-500"></div>
        <div class="absolute left-1/2 top-1/2 flex h-10 w-5 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white opacity-0 shadow-sm ring-1 ring-pos-200/60 transition-opacity group-hover:opacity-100">
            <i class="mdi mdi-drag-vertical text-slate-400"></i>
        </div>
    </div>

    {{-- ==================== PANEL KANAN: Detail Pesanan ==================== --}}
    <div class="kasir-right">
        <div class="kasir-right-card">

            {{-- Header --}}
            <div class="kasir-right-head">
                <h3 class="text-lg font-bold text-pos-900">Detail Pesanan</h3>
                <p class="mt-0.5 text-sm text-slate-500">
                    <span x-text="cart.length"></span> item dipilih
                </p>
            </div>

            {{-- List item pesanan --}}
            <div class="kasir-cart-list">

                <div x-show="cart.length === 0" class="kasir-empty text-slate-400">
                    <i class="mdi mdi-cart-outline text-4xl"></i>
                    <p class="mt-3 text-sm">Belum ada produk dipilih.</p>
                    <p class="text-xs text-slate-400">Klik produk di sebelah kiri untuk menambahkan.</p>
                </div>

                <template x-for="item in cart" :key="item.id">
                    <div class="kasir-cart-item">

                        {{-- Gambar seragam 1:1 --}}
                        <div class="kasir-thumb">
                            <template x-if="item.gambar_url">
                                <img :src="item.gambar_url" :alt="item.nama_produk">
                            </template>
                            <template x-if="!item.gambar_url">
                                <div class="kasir-thumb-empty text-pos-200">
                                    <i class="mdi mdi-image-off-outline text-lg"></i>
                                </div>
                            </template>
                        </div>

                        {{-- Nama + harga satuan --}}
                        <div class="kasir-cart-name">
                            <p class="kasir-name text-sm font-medium text-pos-900" x-text="item.nama_produk"></p>
                            <p class="kasir-price-unit text-xs text-slate-400" x-text="formatRupiah(item.harga_jual) + ' / item'"></p>
                        </div>

                        {{-- Qty control --}}
                        <div class="kasir-qty">
                            <button type="button" @click="decrementQty(item.id)" class="kasir-qty-btn">
                                <i class="mdi mdi-minus text-base"></i>
                            </button>

                            <span class="w-6 text-center text-sm font-semibold text-pos-900" x-text="item.qty"></span>

                            <button type="button" @click="incrementQty(item.id)" :disabled="item.qty >= item.stok" class="kasir-qty-btn">
                                <i class="mdi mdi-plus text-base"></i>
                            </button>
                        </div>

                        {{-- Subtotal + hapus (icon) --}}
                        <div class="kasir-cart-end">
                            <span class="text-sm font-semibold text-pos-900" x-text="formatRupiah(item.harga_jual * item.qty)"></span>
                            <button type="button" @click="removeFromCart(item.id)" title="Hapus dari pesanan" class="kasir-icon-btn">
                                <i class="mdi mdi-trash-can-outline text-lg"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer: total + checkout --}}
            <div class="kasir-right-foot">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-600">Total Belanja</span>
                    <span class="text-xl font-bold text-pos-900" x-text="formatRupiah(grandTotal)"></span>
                </div>

                <button
                    type="button"
                    @click="checkout()"
                    :disabled="cart.length === 0"
                    class="flex w-full items-center justify-center gap-2 rounded-full bg-pos-500 py-3.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-pos-900 hover:shadow-md active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <i class="mdi mdi-cash-register text-lg"></i>
                    Checkout
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== TOAST ==================== --}}
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
function kasirPage() {
    return {
        products: [],
        loading: true,
        search: '',
        
        cart: [],
        
        leftPct: 62,
        
        toast: { show: false, message: '', type: 'success' },

        routes: {
            produk: "{{ route('transaksi.kasir.produk') }}",
        },

        get filteredProducts() {
            const keyword = this.search.trim().toLowerCase();
            if (!keyword) return this.products;

            return this.products.filter((p) => p.nama_produk.toLowerCase().includes(keyword));
        },

        get grandTotal() {
            return this.cart.reduce((sum, item) => sum + (item.harga_jual * item.qty), 0);
        },

        init() {
            this.loadProducts();
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
        },

        async loadProducts() {
            this.loading = true;
            try {
                const res = await fetch(this.routes.produk, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                this.products = json.data;
            } catch (err) {
                this.showToast('Gagal memuat produk.', 'error');
            } finally {
                this.loading = false;
            }
        },

        qtyInCart(productId) {
            const item = this.cart.find((c) => c.id === productId);
            return item ? item.qty : 0;
        },

        addToCart(product) {
            if (product.stok <= 0) return;

            const existing = this.cart.find((c) => c.id === product.id);

            if (existing) {
                if (existing.qty < product.stok) {
                    existing.qty++;
                } else {
                    this.showToast(`Stok "${product.nama_produk}" tidak cukup.`, 'error');
                }
                return;
            }

            this.cart.push({
                id: product.id,
                nama_produk: product.nama_produk,
                gambar_url: product.gambar_url,
                harga_jual: product.harga_jual,
                stok: product.stok,
                qty: 1,
            });
        },

        incrementQty(productId) {
            const item = this.cart.find((c) => c.id === productId);
            if (item && item.qty < item.stok) {
                item.qty++;
            }
        },

        decrementQty(productId) {
            const item = this.cart.find((c) => c.id === productId);
            if (!item) return;

            if (item.qty <= 1) {
                this.removeFromCart(productId);
            } else {
                item.qty--;
            }
        },

        removeFromCart(productId) {
            this.cart = this.cart.filter((c) => c.id !== productId);
        },

        checkout() {
            this.showToast('Fitur checkout akan segera tersedia.', 'success');
        },
        
        startResize(event) {
            event.preventDefault();
            const rect = this.$refs.root.getBoundingClientRect();
            const minPct = (320 / rect.width) * 100;
            const maxPct = 100 - ((360 + 8) / rect.width) * 100;

            document.body.style.userSelect = 'none';

            const onMouseMove = (e) => {
                const pct = ((e.clientX - rect.left) / rect.width) * 100;
                this.leftPct = Math.max(minPct, Math.min(maxPct, pct));
            };

            const onMouseUp = () => {
                document.body.style.userSelect = '';
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            };

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        },
    };
}
</script>
@endpush