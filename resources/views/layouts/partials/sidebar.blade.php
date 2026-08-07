<aside
    class="flex flex-col bg-pos-900 transition-all duration-300 ease-in-out"
    :class="sidebarExpanded ? 'w-64' : 'w-20'"
    @mouseenter="sidebarHover = true"
    @mouseleave="sidebarHover = false"
>
    <div class="flex h-16 items-center border-b border-pos-500/30 px-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-pos-500 text-white font-bold text-lg">
            P
        </div>
        <span
            x-show="sidebarExpanded"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="ml-3 whitespace-nowrap text-lg font-semibold text-white"
        >{{ config('app.name', 'POS') }}</span>
    </div>
    
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
        <ul class="space-y-1 px-3">
            @forelse($sidebarMenus ?? [] as $menu)
                @include('layouts.partials.sidebar-item', ['menu' => $menu])
            @empty
                {{-- Tidak ada menu yang bisa diakses --}}
            @endforelse
        </ul>
    </nav>
</aside>