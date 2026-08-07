{{-- resources/views/layouts/partials/sidebar-item.blade.php --}}
@php
    $hasChildren = $menu->children && $menu->children->count() > 0;
    $isActive = $menu->route && request()->routeIs($menu->route);
    $childActive = $hasChildren && $menu->children->contains(function ($c) {
        return $c->route && request()->routeIs($c->route);
    });
@endphp

<li x-data="{ open: {{ $childActive ? 'true' : 'false' }} }">
    @if ($hasChildren)
        <button
            type="button"
            @click="open = !open"
            class="group flex w-full items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ $childActive ? 'bg-pos-500 text-white' : 'text-pos-50 hover:bg-pos-500/40' }}"
        >
            <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                <i class="{{ $menu->icon ?? 'mdi mdi-circle-small' }} text-xl"></i>
            </span>

            <span
                x-show="sidebarExpanded"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="ml-3 flex-1 whitespace-nowrap text-left"
            >{{ $menu->realname }}</span>

            <svg
                x-show="sidebarExpanded"
                :class="open ? 'rotate-90' : ''"
                class="h-4 w-4 shrink-0 transition-transform duration-200"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <ul
            x-show="open && sidebarExpanded"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-1 space-y-1 pl-9"
        >
            @foreach ($menu->children as $child)
                <li>
                    <a
                        href="{{ $child->route ? route($child->route) : '#' }}"
                        class="block rounded-lg px-3 py-2 text-sm transition-colors {{ $child->route && request()->routeIs($child->route) ? 'bg-pos-500 text-white font-medium' : 'text-pos-50/80 hover:bg-pos-500/30 hover:text-white' }}"
                    >{{ $child->realname }}</a>
                </li>
            @endforeach
        </ul>
    @else
        <a
            href="{{ $menu->route ? route($menu->route) : '#' }}"
            class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ $isActive ? 'bg-pos-500 text-white' : 'text-pos-50 hover:bg-pos-500/40' }}"
        >
            <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                <i class="{{ $menu->icon ?? 'mdi mdi-circle-small' }} text-xl"></i>
            </span>

            <span
                x-show="sidebarExpanded"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="ml-3 whitespace-nowrap"
            >{{ $menu->realname }}</span>
        </a>
    @endif
</li>