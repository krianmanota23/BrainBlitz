<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Arena Display') - BrainBlitz</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #0f0f1a; 
            color: white;
            overflow: hidden !important;
            height: 100vh;
            width: 100vw;
            animation: fadeIn 0.8s ease-in;
            font-size: 1.25rem; /* Larger base font for TV */
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .text-gradient { background: linear-gradient(to bottom right, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .card { background: rgba(26, 26, 46, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="antialiased select-none">
    @yield('content')

    @stack('scripts')
</body>
</html>
