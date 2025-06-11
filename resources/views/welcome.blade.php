@extends('layouts.app')

@section('content')
<div class="welcome-container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">{{ __('text.Bienvenido_a') }}  {{ config('app.name', 'Travel Experience') }}</h1>
            <p class="hero-subtitle">{{ __('text.Texto_bienvenida') }} </p>
            
            <div class="hero-actions">
                <a href="{{ url('/traslados') }}" class="btn btn-primary btn-hero">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-2a1 1 0 00-.293-.707l-3-3A1 1 0 0016 7h-1V5a1 1 0 00-1-1H3z" />
                    </svg>
                    {{ __('Traslados') }}
                </a>
                <a href="{{ url('/hoteles') }}" class="btn btn-secondary btn-hero">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17V9a4 4 0 014-4h4a4 4 0 014 4v8m-2 0V9a2 2 0 00-2-2h-4a2 2 0 00-2 2v8m-4 0h16" />
                    </svg>
                    {{ __('Hoteles') }}
                </a>

                <a href="{{ url('/tours') }}" class="btn btn-outline-info btn-hero">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.618a2 2 0 011.553-1.894L9 2m0 18l6-3m-6 3v-18m6 15l5.447-2.724A2 2 0 0021 15.382V6.618a2 2 0 00-1.553-1.894L15 2m0 18v-18" />
                    </svg>
                    {{ __('Tours') }}
                </a>

            </div>
        </div>
        
        <div class="hero-image">
            <img src="{{ asset('images/banner.jpg') }}" alt="Experiencias de viaje" class="img-hero">
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <h2 class="section-title">{{ __('text.Titulo_por_que') }}</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3>{{ __('text.Rapido') }}</h3>
                <p>{{ __('text.Detalle_rapido') }}</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3>{{ __('text.Seguro') }}</h3>
                <p>{{ __('text.Detalle_seguro') }}</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3>{{ __('text.Flexible') }}</h3>
                <p>{{ __('text.Detalle_flexible') }}</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h3>{{ __('text.Soporte') }}</h3>
                <p>{{ __('text.Detalle_soporte') }}</p>
            </div>
        </div>
    </section>

    <!-- Popular Destinations -->
    <section class="destinations-section">
        <h2 class="section-title">{{ __('text.Destinos_populares') }}</h2>
        <div class="destinations-grid">
            <div class="destination-card">
                <img src="{{ asset('images/destination1.jpg') }}" alt="Samaipata">
                <div class="destination-overlay">
                    <h3>Fuerte de Samaipata</h3>
                    <a href="#" class="btn btn-outline">Explorar</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="{{ asset('images/destination2.jpg') }}" alt="Santa Cruz de la Sierra">
                <div class="destination-overlay">
                    <h3>Santa Cruz</h3>
                    <a href="#" class="btn btn-outline">Explorar</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="{{ asset('images/destination3.jpg') }}" alt="Salar de Uyuni">
                <div class="destination-overlay">
                    <h3>Uyuni</h3>
                    <a href="#" class="btn btn-outline">Explorar</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <h2 class="section-title">{{ __('text.Dicen_los_clientes') }}</h2>
        <div class="testimonials-slider">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="testimonial-rating">
                        ★★★★★
                    </div>
                    <p>"Excelente servicio desde el primer momento. Los traslados fueron puntuales y los hoteles superaron nuestras expectativas."</p>
                </div>
                <div class="testimonial-author">
                    <img src="{{ asset('images/user1.jpg') }}" alt="María González">
                    <div>
                        <h4>María González</h4>
                        <span>{{ __('text.Viajo_a') }} Santa Cruz</span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="testimonial-rating">
                        ★★★★☆
                    </div>
                    <p>"Los tours fueron increíbles, especialmente el de ruinas del Fuerte. Los guías muy profesionales y conocedores."</p>
                </div>
                <div class="testimonial-author">
                    <img src="{{ asset('images/user2.jpg') }}" alt="Carlos Martínez">
                    <div>
                        <h4>Carlos Martínez</h4>
                        <span>{{ __('text.Viajo_a') }} Samaipata</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
