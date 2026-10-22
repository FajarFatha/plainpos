{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" x-data="{
    sidebarOpen: false,   // desktop/tablet: sidebar dikunci terbuka lewat hamburger
    sidebarHover: false,  // desktop: sidebar melebar saat di-hover mouse
    mobileOpen: false,    // HP: drawer sedang terbuka
    isMobile: window.matchMedia('(max-width: 767px)').matches,

    // Dipakai sidebar & sidebar-item untuk memutuskan tampil label atau icon saja.
    get sidebarExpanded() {
        return this.isMobile ? this.mobileOpen : (this.sidebarOpen || this.sidebarHover);
    },

    toggleSidebar() {
        if (this.isMobile) {
            this.mobileOpen = !this.mobileOpen;
        } else {
            this.sidebarOpen = !this.sidebarOpen;
        }
    },

    init() {
        const mq = window.matchMedia('(max-width: 767px)');
        mq.addEventListener('change', (e) => {
            this.isMobile = e.matches;
            this.mobileOpen = false;
        });
    },
}" @keydown.escape.window="mobileOpen = false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS') }} - @yield('title', 'Dashboard')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }

        :root { --app-main-pad: 24px; }

        .app-shell { height: 100vh; height: 100dvh; }
        .app-content { min-width: 0; }
        .app-main { padding: var(--app-main-pad); }

        .app-topbar { padding: 0 24px; }
        .app-topbar-left { min-width: 0; }
        .app-topbar h1,
        .app-topbar p { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .date-short { display: none; }
        
        .app-sidebar {
            flex-shrink: 0;
            width: 5rem;
            transition: width .3s ease-in-out, transform .3s ease-in-out;
        }
        .app-sidebar.is-expanded { width: 16rem; }
        .app-sidebar-close { display: none; margin-left: auto; }
        
        @media (max-width: 767px) {
            :root { --app-main-pad: 16px; }

            .app-topbar { padding: 0 14px; }

            .app-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 50;
                width: 16rem;
                transform: translateX(-100%);
            }
            .app-sidebar.is-mobile-open {
                transform: translateX(0);
                box-shadow: 0 0 40px rgba(15, 23, 42, .35);
            }
            .app-sidebar-close { display: flex; }

            .app-backdrop {
                position: fixed;
                inset: 0;
                z-index: 40;
                background: rgba(15, 23, 42, .5);
            }
        }
        
        @media (max-width: 479px) {
            .date-full { display: none; }
            .date-short { display: inline; }
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="app-shell flex overflow-hidden bg-pos-50">

        <div
            class="app-backdrop"
            x-show="mobileOpen"
            x-cloak
            x-transition.opacity
            @click="mobileOpen = false"
        ></div>

        @include('layouts.partials.sidebar')

        <div class="app-content flex flex-1 flex-col overflow-hidden">

            @include('layouts.partials.topbar')

            <main class="app-main flex-1 overflow-y-auto">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>