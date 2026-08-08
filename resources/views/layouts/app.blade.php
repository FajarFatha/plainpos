{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" x-data="{
    sidebarOpen: false,
    sidebarHover: false,
    get sidebarExpanded() { return this.sidebarOpen || this.sidebarHover }
}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS') }} - @yield('title', 'Dashboard')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen overflow-hidden bg-pos-50">

        @include('layouts.partials.sidebar')

        <div class="flex flex-1 flex-col overflow-hidden">

            @include('layouts.partials.topbar')

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>