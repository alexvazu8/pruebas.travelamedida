<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:300,400,600,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vinculando el archivo CSS -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    @yield('styles')

     @vite(['resources/sass/app.scss']) {{-- Bootstrap y estilos --}}

</head>
<body class="bg-neutral text-primary">
    <div id="app">
        <nav class="navbar navbar-expand-md bg-primary  shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Laravel') }} - Inicio">
                    <div class="logo-container bg-white rounded-3 p-2 shadow-sm me-2" style="height: 90px; width: 110px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('travel.svg') }}" alt="" style="height: 75px; width: auto;" class="d-block">
                    </div>
                  
                </a>
                <button class="navbar-toggler text-neutral" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link nav-btn border rounded text-white" href="{{ url('/traslados') }}">
                                <i class="fas fa-car me-2"></i>
                                {{ __('Traslados') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-btn border rounded text-white" href="{{ url('/hoteles') }}">
                                <i class="fas fa-bed me-2"></i>
                                {{ __('Hoteles') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-btn border rounded text-white" href="{{ url('/tours') }}">
                                <i class="fas fa-map-marked-alt me-2"></i>
                                {{ __('Tours') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white d-flex align-items-center" href="{{ url('/carritos/show') }}">
                                <i class="fas fa-shopping-cart me-2"></i>
                                {{ __('Carrito') }}
                            </a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link text-white d-flex align-items-center" href="{{ url('/reservas') }}">
                                <i class="fas fa-calendar-check me-2"></i>
                                {{ __('Reservas') }}
                            </a>
                        </li>
                        @endauth
                    </ul>

                    <!-- Right Side Menu -->
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <!-- Currency Selector -->
                        <li class="nav-item dropdown me-3">
                            <a id="monedaDropdown" class="nav-link dropdown-toggle d-flex align-items-center text-white" 
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                               aria-label="{{ __('Seleccionar moneda') }}">
                                <i class="fas fa-money-bill-wave me-2" aria-hidden="true"></i>
                                {{ strtoupper(session('moneda', 'USD')) }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="monedaDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" 
                                       href="/cambiar-moneda/USD" 
                                       onclick="event.preventDefault(); document.getElementById('currency-form-USD').submit();">
                                        <span class="me-2 fw-bold">$</span>
                                        USD - Dólar Estadounidense
                                        <form id="currency-form-USD" action="/cambiar-moneda/USD" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" 
                                       href="/cambiar-moneda/BOB" 
                                       onclick="event.preventDefault(); document.getElementById('currency-form-BOB').submit();">
                                        <span class="me-2 fw-bold">Bs</span>
                                        BOB - Boliviano
                                        <form id="currency-form-BOB" action="/cambiar-moneda/BOB" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" 
                                       href="/cambiar-moneda/BRL" 
                                       onclick="event.preventDefault(); document.getElementById('currency-form-BRL').submit();">
                                        <span class="me-2 fw-bold">R$</span>
                                        BRL - Real Brasileño
                                        <form id="currency-form-BRL" action="/cambiar-moneda/BRL" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </a>
                                </li>
                            </ul>
                        </li>

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
                                    <a  class="btn btn-outline-light btn-sm rounded-pill px-3" href="{{ route('register') }}">
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
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                            @csrf
                                            <button type="submit" 
                                                    class="dropdown-item d-flex align-items-center py-2 text-danger w-100 border-0 bg-transparent text-start p-0">
                                                <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>
                                                {{ __('Cerrar Sesión') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
                
        <main class="py-4 bg-neutral">
            <div class="container mx-auto p-4 bg-white shadow rounded-lg">
                @yield('content')
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
    @vite(['resources/js/app.js']) {{-- JavaScript aquí si depende del DOM --}}
    @yield('scripts')
</body>
</html>