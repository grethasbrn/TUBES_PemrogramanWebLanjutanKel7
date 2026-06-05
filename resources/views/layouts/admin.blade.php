<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmbee</title>

    <!-- WAJIB UNTUK FETCH -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&family=DM+Sans&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app">
    
    @include('components.sidebarAd')

    <div class="main">
        <div class="content">
            @yield('content')
        </div>
    </div>

</div>

{{-- PENTING: BIAR JS DI PAGE JALAN --}}
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>