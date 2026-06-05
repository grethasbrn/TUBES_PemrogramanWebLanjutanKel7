<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="dokter-name" content="{{ Auth::user()->name ?? 'Dokter' }}">
    <title>Pharmbee</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app">

        @include('components.sidebarD')

        <div class="main">
            <div class="content">
                @yield('content')
            </div>
        </div>

    </div>

    {{-- Script global dokter dimuat SETELAH body agar DOM siap --}}
    <script src="{{ asset('js/dokter-global.js') }}"></script>

    @yield('scripts')
</body>
</html>