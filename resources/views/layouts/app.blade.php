<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Catálogo de Libros')</title>

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex flex-column">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('web.home') }}">
                <i class="bi bi-book-half me-2"></i>Biblioteca
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.books.*') ? 'active' : '' }}" href="{{ route('web.books.index') }}">
                            <i class="bi bi-journal-bookmark me-1"></i>Libros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.categories.*') ? 'active' : '' }}" href="{{ route('web.categories.index') }}">
                            <i class="bi bi-tags me-1"></i>Categorías
                        </a>
                    </li>
                </ul>
                <a class="btn btn-outline-light btn-sm" href="/docs/api" target="_blank" rel="noopener">
                    <i class="bi bi-file-earmark-code me-1"></i>API Docs
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        <div id="global-alert"></div>
        @yield('content')
    </main>

    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            Gestión de Libros y Categorías · Blade + jQuery + AJAX + Bootstrap 5
        </div>
    </footer>

    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
