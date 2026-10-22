{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<aside
    class="app-sidebar flex flex-col bg-pos-900"
    :class="{ 'is-expanded': sidebarExpanded, 'is-mobile-open': mobileOpen }"
    
    @pointerenter="sidebarHover = ($event.pointerType === 'mouse')"
    @pointerleave="sidebarHover = false"
>
    <div class="flex h-16 shrink-0 items-center border-b border-pos-500/30 px-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-pos-500 text-lg font-bold text-white">
            P
        </div>
        <span
            x-show="sidebarExpanded"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="ml-3 whitespace-nowrap text-lg font-semibold text-white"
        >{{ config('app.name', 'POS') }}</span>

        <button
            type="button"
            class="app-sidebar-close h-10 w-10 shrink-0 items-center justify-center rounded-full text-white transition-colors hover:bg-pos-500/40"
            @click="mobileOpen = false"
            aria-label="Tutup menu"
        >
            <i class="mdi mdi-close text-xl"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
        <ul class="space-y-1 px-3">
            @forelse($sidebarMenus ?? [] as $menu)
                @include('layouts.partials.sidebar-item', ['menu' => $menu])
            @empty
            
            @endforelse
        </ul>
    </nav>
</aside>