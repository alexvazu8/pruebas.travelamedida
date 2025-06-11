@extends('layouts.app')

@section('content')

<div  class="container mt-5">
    <h1 class="mb-4 text-center">Detalle Completo del Servicio</h1>
     <!-- Botón de Imprimir -->
     <div class="text-end mb-3">
     <a href="{{ route('reservas.showReserva', $data['reserva']['Localizador']) }}" class="btn btn-primary">Retornar a la reserva</a>
     <button class="btn btn-dark" onclick="imprimirVoucher()">Imprimir Voucher</button>
    </div>
    
    <div id="voucherContent" class="card shadow mb-4">
        <!-- Tarjeta de Información Principal -->
        <div  class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Información Principal</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID Detalle Servicio:</strong> {{ $data['id'] }}</p>
                        <p><strong>Precio Servicio:</strong> ${{ number_format($data['Precio_Servicio'], 2) }}</p>
                        <p><strong>Tipo Servicio:</strong> 
                            @switch($data['Tipo_servicio'])
                                @case('H') Hotel @break
                                @case('T') Traslado @break
                                @case('TOU') Tour @break
                                @default Otro
                            @endswitch
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email Encargado:</strong> {{ $data['Email_encargado_reserva'] }}</p>
                        <p><strong>Fecha Creación:</strong> {{ \Carbon\Carbon::parse($data['created_at'])->format('d/m/Y H:i') }}</p>
                        <p><strong>Última Actualización:</strong> {{ \Carbon\Carbon::parse($data['updated_at'])->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Datos del Cliente (común a todos los tipos) -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h2 class="h5 mb-0">Datos del Cliente</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Localizador:</strong> {{ $data['reserva']['Localizador'] }}</p>
                        <p><strong>Nombre:</strong> {{ $data['reserva']['Nombre_Cliente'] }} {{ $data['reserva']['Apellido_Cliente'] }}</p>
                        <p><strong>Teléfono:</strong> {{ $data['reserva']['Telefono_Cliente'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> {{ $data['reserva']['Email_contacto_reserva'] }}</p>
                        <p><strong>Importe Reserva:</strong> ${{ number_format($data['reserva']['Importe_Reserva'], 2) }}</p>
                        @if($data['Tipo_servicio'] == 'TOU')
                            <p><strong>Comentarios:</strong> {{ $data['reserva']['Comentarios'] ?? 'Ninguno' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($data['Tipo_servicio'] == 'H')
            <!-- SECCIÓN PARA HOTELES -->
            @include('reservas.detalles.hotel')

        @elseif($data['Tipo_servicio'] == 'T')
            <!-- SECCIÓN PARA TRASLADOS -->
            @include('reservas.detalles.traslado')

        @elseif($data['Tipo_servicio'] == 'TOU')
            <!-- SECCIÓN PARA TOURS -->
            @include('reservas.detalles.tour')
        @endif
    </div>
</div>

@endsection


@section('styles')
<style>
     .map-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* Relación de aspecto 16:9 */
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        background: #f8f9fa;
    }
    
    #miniMapa {
        height: 400px; /* Ajusta la altura según tus necesidades */
        width: 100%;
        border: 1px solid #ddd;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    /* Estilo para cuando se imprime */
    @media print {
        .map-container {
            height: 300px;
            padding-bottom: 0;
        }
    }
    .tour-image {
        height: 150px;
        object-fit: cover;
        margin-bottom: 10px;
        border-radius: 5px;
    }
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
</style>
@if($data['Tipo_servicio'] == 'H')
    <!-- Leaflet CSS solo para hoteles -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endif
@endsection

@section('scripts')
@if($data['Tipo_servicio'] == 'H')
    <!-- Leaflet JS solo para hoteles -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if($data['Tipo_servicio'] == 'H')
            let mapInitialized = false; // Bandera para controlar la inicialización
            
            const loadMap = () => {
                if (mapInitialized) return; // Evitar múltiples inicializaciones
                
                try {
                    const hotelData = @json($data['detalle_hotel']['tipo_habitacion_hotel']['hotel']);
                    
                    if(hotelData.Latitud && hotelData.Longitud) {
                        const lat = parseFloat(hotelData.Latitud);
                        const lon = parseFloat(hotelData.Longitud);
                        //alert(lat);
                        // Crear el mapa con opciones de rendimiento
                        const miniMapa = L.map('miniMapa', {
                            preferCanvas: true,
                            zoomControl: true,
                            fadeAnimation: false,
                            zoomAnimation: false
                        }).setView([lat, lon], 15);
                        
                        // Capa de tiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(miniMapa);
                        
                        // Marcador
                        L.marker([lat, lon]).addTo(miniMapa)
                            .bindPopup(hotelData.Nombre_Hotel)
                            .openPopup();
                        
                        // Función para manejar el redimensionamiento
                        const handleResize = () => {
                            setTimeout(() => {
                                miniMapa.invalidateSize({
                                    animate: false,
                                    pan: false
                                });
                            }, 100);
                        };
                        
                        // Eventos para redimensionamiento
                        window.addEventListener('resize', handleResize);
                        
                        // Redimensionar inicialmente después de un pequeño delay
                        setTimeout(() => {
                            miniMapa.invalidateSize({
                                animate: false,
                                pan: false
                            });
                        }, 300);
                        
                        mapInitialized = true; // Marcar como inicializado
                    } else {
                        document.getElementById('miniMapa').innerHTML = 
                            '<div class="alert alert-warning">Mapa no disponible</div>';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    document.getElementById('miniMapa').innerHTML = 
                        '<div class="alert alert-danger">Error al cargar el mapa</div>';
                }
            };
            
            // Verificar si el mapa ya está visible inmediatamente
            const checkMapVisibility = () => {
                const mapContainer = document.getElementById('miniMapa');
                if (mapContainer && getComputedStyle(mapContainer).display !== 'none') {
                    loadMap();
                    return true;
                }
                return false;
            };
            
            // Intentar cargar inmediatamente
            if (!checkMapVisibility()) {
                // Si no está visible, configurar el observer
                const observer = new MutationObserver((mutations, obs) => {
                    if (checkMapVisibility()) {
                        obs.disconnect(); // Dejar de observar si se cargó
                    }
                });
                
                observer.observe(document.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
                
                // Desconectar después de 5 segundos como máximo
                setTimeout(() => observer.disconnect(), 5000);
            }
            @endif
        });
    </script>
@endif

<script>
    function imprimirVoucher() {
        const printContent = document.getElementById('voucherContent').outerHTML;
        const ventana = window.open('', '_blank');
        if (!ventana) {
            alert("Por favor, permite ventanas emergentes para poder imprimir el voucher.");
            return;
        }
        ventana.document.open();
        ventana.document.write(`
            <html>
                <head>
                    <title>Voucher</title>
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha384-wH6/z8fYGTVhQAm7RY5M07M3A9P2pXlyQQiL0Y2Ew1FZq+q3sB9+bfk9L7H3Ixxh" crossorigin=""/>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                        .logo-container { text-align: center; margin-bottom: 20px; }
                        .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; }
                        #map {
                            width: 100% !important;
                            height: 500px !important;
                        }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div class="logo-container">
                        <img src="/travel.svg" alt="Logo Travel a Medida" style="height: 80px;">
                        <img src="/visionmundo.png" alt="Logo Vision Mundo" style="height: 80px;">
                    </div>
                    <div id="printContent">
                        ${printContent}
                    </div>
                    


                </body>
            </html>
        `);
        ventana.document.close();
        @if($data['Tipo_servicio'] == 'H')
        
            ventana.onload = function () {
                // Agregar estilos dinámicamente
                const leafletCSS = ventana.document.createElement('link');
                leafletCSS.rel = 'stylesheet';
                leafletCSS.href = "https://unpkg.com/leaflet@1.9.3/dist/leaflet.css";
                ventana.document.head.appendChild(leafletCSS);

                const customCSS = ventana.document.createElement('style');
                customCSS.innerHTML = `
                    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                    .logo-container { text-align: center; margin-bottom: 20px; }
                    .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; }
                    #miniMapa { width: 100%; height: 250px; }
                `;
                ventana.document.head.appendChild(customCSS);

                // Verifica si la latitud y longitud están disponibles
                const hotelData = @json($data["detalle_hotel"]["tipo_habitacion_hotel"]["hotel"]);
                const lat = hotelData?.Latitud ? parseFloat(hotelData.Latitud) : null;
                const lon = hotelData?.Longitud ? parseFloat(hotelData.Longitud) : null;
            
                if (lat && lon) {
                    // Agregar el script de Leaflet dinámicamente
                    const scriptLeaflet = ventana.document.createElement('script');
                    scriptLeaflet.src = "https://unpkg.com/leaflet@1.9.3/dist/leaflet.js";
                    scriptLeaflet.onload = function () {
                        const miniMapa = L.map('miniMapa', {
                                    preferCanvas: true,
                                    zoomControl: true,
                                    fadeAnimation: false,
                                    zoomAnimation: false
                                }).setView([lat, lon], 15);
                        // Capa de tiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '&copy; OpenStreetMap'
                                }).addTo(miniMapa);
                                
                                // Marcador
                                L.marker([lat, lon]).addTo(miniMapa)
                                    .bindPopup(hotelData.Nombre_Hotel)
                                    .openPopup();
                    };
                    ventana.document.head.appendChild(scriptLeaflet);
                } else {
                    ventana.document.getElementById('miniMapa').innerHTML = '<div class="alert alert-warning">Mapa no disponible</div>';
                }

                setTimeout(() => {
                    ventana.print();
                    ventana.close();
                }, 800); // Espera para cargar el mapa antes de imprimir
            };
        @endif

       
               
    }
</script>

@endsection