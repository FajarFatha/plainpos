<aside
    class="flex flex-col bg-pos-900 transition-all duration-300 ease-in-out"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>
    <div class="flex h-16 items-center justify-center border-b border-pos-500/30 px-4">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-pos-500 text-white font-bold text-lg shrink-0">
            P
        </div>
        <span
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="ml-3 text-white font-semibold text-lg whitespace-nowrap"
        >
            PlainPOS
        </span>
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