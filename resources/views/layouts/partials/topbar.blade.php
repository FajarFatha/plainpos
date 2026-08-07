<header class="flex h-16 shrink-0 items-center justify-between border-b border-pos-200 bg-white px-6">
    <div class="flex items-center gap-4">
        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-pos-900 hover:bg-pos-50 transition-colors"
        >
            <svg
                x-show="!sidebarExpanded"
                class="h-6 w-6"
                fill="none" viewBox="0 0 24 24" stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>

            <svg
                x-show="sidebarExpanded"
                x-cloak
                class="h-6 w-6"
                fill="none" viewBox="0 0 24 24" stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div>
            <h1 class="text-lg font-bold text-pos-900">@yield('page-title', 'Dashboard')</h1>
            <p class="text-xs text-pos-500">{{ now()->translatedFormat('l, j F Y') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-4">
        {{-- Search --}}
        <div class="relative hidden sm:block">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-pos-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
            </svg>
            <input
                type="text"
                placeholder="Cari..."
                class="w-64 rounded-lg border-none bg-pos-50 py-2 pl-9 pr-4 text-sm text-pos-900 placeholder-pos-500 focus:ring-2 focus:ring-pos-500"
            >
        </div>

        {{-- Notification --}}
        <button type="button" class="relative flex h-9 w-9 items-center justify-center rounded-lg bg-pos-50 text-pos-900 hover:bg-pos-200 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500"></span>
        </button>

        {{-- User avatar dropdown --}}
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-pos-900 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 2)) }}
            </button>

            <div
                x-show="userMenuOpen"
                @click.outside="userMenuOpen = false"
                x-transition
                class="absolute right-0 mt-2 w-48 rounded-lg border border-pos-200 bg-white py-1 shadow-lg"
                style="display: none;"
            >
                <div class="border-b border-pos-50 px-4 py-2">
                    <p class="text-sm font-medium text-pos-900">{{ auth()->user()->username ?? '' }}</p>
                    <p class="text-xs text-pos-500">{{ auth()->user()->group->nama_group ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-pos-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>