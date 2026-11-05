<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Tableau de bord')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS (si tu l'utilises encore globalement, sinon tu peux le supprimer si les composants l'importent déjà) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ton CSS Custom -->
    <link rel="stylesheet" href="{{ asset('css/custom-dashboard.css') }}">

</head>
<body class="font-sans antialiased">
    <div class="app-layout"> <!-- Enveloppe globale si besoin, mais pas critique si sidebar est fixed -->

        <!-- Sidebar Component -->
        <x-sidebar /> {{-- Ton composant sidebar --}}

        <!-- Main Content Wrapper -->
        <main class="main-content"> {{-- Prend l'espace à droite de la sidebar --}}

            <!-- Top Navbar Component -->
            {{-- Assure-toi d'avoir la variable $user disponible ou passe Auth::user() --}}
            <x-top-navbar :user="Auth::user()" /> {{-- Notre nouveau composant top navbar --}}

            <!-- Zone de contenu principale sous la top navbar -->
            <div class="content-area"> {{-- padding pour l'espacement --}}
                {{-- Optionnel : Titre de la page spécifique si besoin en plus de la top-navbar --}}
                {{--
                <header class="page-specific-header mb-4">
                    <h2>@yield('page-title', 'Contenu')</h2>
                </header>
                --}}
                @yield('content')
            </div>

        </main>
    </div>

    <!-- Scripts JS (Bootstrap Bundle est nécessaire pour les dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') {{-- Pour les scripts spécifiques à une page (ex: Chart.js) --}}
</body>
</html>