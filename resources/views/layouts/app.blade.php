<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmbee</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&family=DM+Sans&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

    @vite(['resources/css/style1.css', 'resources/js/app.js'])
</head>
<body>

    {{-- Topbar di LUAR .app --}}
    @include('components.topbar')

    <div class="app">

        {{-- Hanya sidebar --}}
        @include('components.sidebar')

        <div class="main">
            <div class="content">
                @yield('content')
            </div>
        </div>

    </div>

    @yield('scripts')

</body>
</html>