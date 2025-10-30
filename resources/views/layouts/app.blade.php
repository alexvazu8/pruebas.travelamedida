<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ config('app.name', 'Laravel') }} - Servicios de traslados, hoteles y tours">
    <meta name="keywords" content="traslados, hoteles, tours, viajes, turismo">
    <meta name="author" content="VISION MUNDO PY">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
    <meta property="og:description" content="Servicios de traslados, hoteles y tours">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ __('Servicios de Viaje') }}</title>
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="{{ asset('css/styles.css') }}" as="style">
    <link rel="preload" href="https://fonts.bunny.net/css?family=Nunito" as="style">
    
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:300,400,600,700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    
    @yield('styles')
    @vite(['resources/sass/app.scss'])
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link btn btn-primary position-absolute top-0 start-0 m-2" style="transform: translateY(-100%); transition: transform 0.3s;">
        {{ __('Saltar al contenido principal') }}
    </a>

    <div id="app" class="d-flex flex-column flex-grow-1">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-lg" aria-label="{{ __('Navegación principal') }}">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Laravel') }} - Inicio">
                    <div class="logo-container bg-white rounded-3 p-2 shadow-sm me-2" style="height: 80px; width: 100px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('travel.svg') }}" alt="" style="height: 50px; width: auto;" class="d-block">
                    </div>
                  
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Content -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Main Menu -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light rounded-pill px-4 py-2 text-white border-2 transition-all hover:bg-white hover:text-primary shadow-sm" 
                            href="{{ url('/traslados') }}" 
                            aria-current="{{ request()->is('traslados') ? 'page' : 'false' }}">
                                <i class="fas fa-car-side me-2" aria-hidden="true"></i>
                                {{ __('Traslados') }}
                            </a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light rounded-pill px-4 py-2 text-white border-2 transition-all hover:bg-white hover:text-primary shadow-sm" 
                            href="{{ url('/hoteles') }}"
                            aria-current="{{ request()->is('hoteles') ? 'page' : 'false' }}">
                                <i class="fas fa-hotel me-2" aria-hidden="true"></i>
                                {{ __('Hoteles') }}
                            </a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light rounded-pill px-4 py-2 text-white border-2 transition-all hover:bg-white hover:text-primary shadow-sm" 
                            href="{{ url('/tours') }}"
                            aria-current="{{ request()->is('tours') ? 'page' : 'false' }}">
                                <i class="fas fa-map-marked-alt me-2" aria-hidden="true"></i>
                                {{ __('Tours') }}
                            </a>
                        </li>
                        
                        <!-- Shopping Cart -->
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light rounded-pill px-4 py-2 text-white border-2 transition-all hover:bg-white hover:text-primary shadow-sm" 
                            href="{{ url('/carritos/show') }}"
                            aria-label="{{ __('Ver carrito de compras') }}">
                                <i class="fas fa-shopping-cart me-2" aria-hidden="true"></i>
                                {{ __('Carrito') }}
                            </a>
                        </li>
                        
                        <!-- Reservations -->
                        @auth
                        <li class="nav-item me-2">
                            <a class="btn btn-success rounded-pill px-4 py-2 text-white border-0 transition-all hover:bg-success-dark hover:shadow-md" 
                            href="{{ url('/reservas') }}"
                            aria-current="{{ request()->is('reservas') ? 'page' : 'false' }}">
                                <i class="fas fa-calendar-check me-2" aria-hidden="true"></i>
                                {{ __('Reservas') }}
                            </a>
                        </li>
                        @endauth
                    </ul>
                    <!-- Right Side Menu -->
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <!-- Language Selector -->
                        <li class="nav-item dropdown me-3">
                            <a id="idiomaDropdown" class="nav-link dropdown-toggle d-flex align-items-center text-white" 
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                               aria-label="{{ __('Seleccionar idioma') }}">
                                <i class="fas fa-globe me-2" aria-hidden="true"></i>
                                {{ strtoupper(App::getLocale()) }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="idiomaDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('lang.switch', 'es') }}">
                                        <span class="flag-icon flag-icon-es me-2"></span>
                                        Español
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('lang.switch', 'pt') }}">
                                        <span class="flag-icon flag-icon-pt me-2"></span>
                                        Português
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('lang.switch', 'en') }}">
                                        <span class="flag-icon flag-icon-gb me-2"></span>
                                        English
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item me-2">
                                    <a class="btn btn-outline-light btn-sm rounded-pill px-3" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1" aria-hidden="true"></i>
                                        {{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-accent btn-sm rounded-pill px-3" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1" aria-hidden="true"></i>
                                        {{ __('Registro') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center text-white" 
                                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                                    <div class="avatar-sm bg-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user text-primary" aria-hidden="true"></i>
                                    </div>
                                    <span class="d-none d-md-inline">{{ Str::limit(Auth::user()->name, 15) }}</span>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">

                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 text-danger" 
                                           href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>
                                            {{ __('Cerrar Sesión') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main id="main-content" class="flex-grow-1 py-4 bg-light">
            <div class="container">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">{{ __('Inicio') }}</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>

                <!-- Page Content -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Cerrar') }}"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>{{ __('Por favor corrige los siguientes errores:') }}</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Cerrar') }}"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-white mt-auto">
            <div class="container py-5">
                <div class="row g-4">
                    <!-- Company Information -->
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-container bg-white rounded-2 p-1 me-3" style="height: 50px; width: 50px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('travel.svg') }}" alt="" style="height: 35px; width: auto;" class="d-block">
                            </div>
                            <h5 class="mb-0 text-accent fw-bold">{{ config('app.name', 'Laravel') }}</h5>
                        </div>
                        <p class="text-light mb-3">{{ __('Tu partner de confianza para experiencias de viaje inolvidables.') }}</p>
                        <div class="d-flex align-items-center text-light">
                            <i class="fas fa-phone-alt text-accent me-2" aria-hidden="true"></i>
                            <span>+591 72220016</span>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <h6 class="text-accent mb-3 fw-semibold">{{ __('Empresa') }}</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="/nosotros" class="text-light text-decoration-none transition-all d-inline-flex align-items-center">
                                    <i class="fas fa-chevron-right small me-1 text-accent" aria-hidden="true"></i>
                                    {{ __('Nosotros') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="col-lg-2 col-md-6">
                        <h6 class="text-accent mb-3 fw-semibold">{{ __('Legal') }}</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="/terminos" class="text-light text-decoration-none transition-all d-inline-flex align-items-center">
                                    <i class="fas fa-chevron-right small me-1 text-accent" aria-hidden="true"></i>
                                    {{ __('Términos') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="/privacidad" class="text-light text-decoration-none transition-all d-inline-flex align-items-center">
                                    <i class="fas fa-chevron-right small me-1 text-accent" aria-hidden="true"></i>
                                    {{ __('Privacidad') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Newsletter -->
                    <div class="col-lg-4 col-md-6">
                        <div class="mt-3">
                            <h6 class="text-accent mb-2 fw-semibold">{{ __('Síguenos') }}</h6>
                            <div class="d-flex gap-2">
                                <a href="#" class="social-icon bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" 
                                   style="width: 36px; height: 36px;" aria-label="{{ __('Facebook') }}">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="social-icon bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" 
                                   style="width: 36px; height: 36px;" aria-label="{{ __('Instagram') }}">
                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="social-icon bg-accent text-white rounded-circle d-flex align-items-center justify-content-center" 
                                   style="width: 36px; height: 36px;" aria-label="{{ __('Twitter') }}">
                                    <i class="fab fa-twitter" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0 small text-light">
                            &copy; {{ date('Y') }} VISION MUNDO PY. {{ __('Todos los derechos reservados.') }}
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="d-flex justify-content-center justify-content-md-end align-items-center gap-3">
                            <svg width="86" height="30" viewBox="0 0 80 24" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="compactCard" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#3B82F6" />
                                        <stop offset="100%" stop-color="#8B5CF6" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Tarjeta -->
                                <g transform="translate(8, 4)">
                                    <rect x="0" y="0" width="24" height="16" rx="3" fill="url(#compactCard)"/>
                                    <rect x="4" y="6" width="6" height="4" rx="1" fill="#FFD700"/>
                                    <rect x="12" y="6" width="8" height="1.5" rx="0.5" fill="white"/>
                                    <rect x="12" y="8" width="6" height="1.5" rx="0.5" fill="white" fill-opacity="0.7"/>
                                    <text x="12" y="14" font-family="Arial" font-size="4" fill="white" text-anchor="middle">CARD</text>
                                </g>
                                
                                <!-- USDT -->
                                <g transform="translate(40, 4)">
                                    <circle cx="12" cy="8" r="8" fill="#26A17B"/>
                                    <text x="12" y="11" font-family="Arial" font-size="8" font-weight="bold" fill="white" text-anchor="middle">$</text>
                                    <text x="12" y="19" font-family="Arial" font-size="4" fill="#1F2937" text-anchor="middle">USDT</text>
                                </g>
                            </svg>
                            <span class="badge bg-success rounded-pill">
                                <i class="fas fa-lock me-1" aria-hidden="true"></i>
                                {{ __('Sitio Seguro') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- Custom JavaScript for enhanced UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Skip link functionality
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) {
                skipLink.addEventListener('focus', function() {
                    this.style.transform = 'translateY(0)';
                });
                
                skipLink.addEventListener('blur', function() {
                    this.style.transform = 'translateY(-100%)';
                });
            }

            // Add active state to current page navigation
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active', 'bg-accent');
                }
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Performance: Lazy load images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>

    @yield('scripts')
</body>
</html>