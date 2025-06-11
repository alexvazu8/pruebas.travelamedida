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
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
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
                <a class="navbar-brand text-white font-bold" href="{{ url('/') }}">
                  
                    <img src="{{ asset('travel.svg') }}" alt="Logo" style="height: 80px;">
                </a>
                <button class="navbar-toggler text-neutral" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link border rounded text-white  hover:bg-danger" href="{{ url('/traslados') }}">{{ __('Traslados') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link border rounded text-white  hover:text-neutral" href="{{ url('/hoteles') }}">{{ __('Hoteles') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link border rounded  text-white hover:text-accent" href="{{ url('/tours') }}">{{ __('Tours') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white hover:text-accent" href="{{ url('/carritos/show') }}">
                                <i class="fas fa-shopping-cart">{{ __('Carrito') }}</i> <!-- Ícono de carrito -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.29 9.09a2 2 0 0 1-1.98 1.91H7.27a2 2 0 0 1-1.98-1.91L3 3zm5.5 14a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0zm8 0a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0z"/>
                                </svg>

                            </a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link text-white hover:text-accent" href="{{ url('/reservas') }}">
                                <i class="fas fa-shopping-cart">{{ __('Reservas') }}</i> <!-- Ícono de carrito -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                    <path d="M9 7h6M9 11h6M9 15h4" />
                                </svg>
                            </a>
                        </li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <!-- Botones de idioma -->

                        <li class="nav-item dropdown">
                            <a id="idiomaDropdown" class="nav-link dropdown-toggle text-white hover:text-accent" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ strtoupper(App::getLocale()) }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end bg-primary" aria-labelledby="idiomaDropdown">
                                <a class="dropdown-item text-white hover:text-accent" href="{{ route('lang.switch', 'es') }}">Español</a>
                                <a class="dropdown-item text-white hover:text-accent" href="{{ route('lang.switch', 'pt') }}">Português</a>
                                <a class="dropdown-item text-white hover:text-accent" href="{{ route('lang.switch', 'en') }}">English</a>
                            </div>
                        </li>
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link text-white hover:text-accent" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link text-neutral text-white hover:text-accent" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-white hover:text-accent" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end bg-primary" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item text-neutral hover:text-accent" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
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
<!-- Footer Bootstrap desde cero: Empresa a la izquierda, Legal a la derecha -->
<footer class="bg-primary text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Columna: Empresa -->
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <h5 class="text-accent border-bottom border-accent pb-2 mb-3">{{ __('Empresa') }}</h5>
                <ul class="list-unstyled text-light">
                    <li>
                        <a href="/nosotros" class="text-light text-decoration-none">{{ __('Nosotros') }}</a>
                    </li>
                    <li class="mt-2 d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-accent" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592z"/>
                        </svg>
                        <span>+591 72220016</span>
                    </li>
                </ul>
            </div>

            <!-- Columna: Legal -->
            <div class="col-12 col-md-6 text-md-end">
                <h5 class="text-accent border-bottom border-accent pb-2 mb-3">{{ __('Legal') }}</h5>
                <ul class="list-unstyled text-light">
                    <li>
                        <a href="/terminos" class="text-light text-decoration-none">{{ __('Terminos') }}</a>
                    </li>
                    <li class="mt-2">
                        <a href="/privacidad" class="text-light text-decoration-none">{{ __('Privacidad') }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-gray-600 my-4">

        <div class="text-center small">
            &copy; {{ date('Y') }} VISION MUNDO PY.
        </div>
    </div>
</footer>


    </div>
    @vite(['resources/js/app.js']) {{-- JavaScript aquí si depende del DOM --}}
    @yield('scripts')
</body>
</html>
