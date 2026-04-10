@extends('layouts.app')

@section('contenido')

<div class="mcq-hero">
    <div class="hero-tag">Autopartes · Velocidad · Calidad</div>

    <h1>
        Las mejores
        <span>Autopartes</span>
    </h1>

    <p class="hero-sub">
        Encuentra todo lo que tu auto necesita. Piezas originales y de alto rendimiento al mejor precio del mercado.
    </p>

    <div class="hero-divider">
        <span></span><i></i><span></span>
    </div>

    <div style="display:flex; gap:1rem; flex-wrap:wrap; justify-content:center;">
        <a href="/catalogo" class="btn-primary btn-primary-lg">
            Ver Catálogo
        </a>
        <a href="/registro" class="btn-ghost btn-primary-lg">
            Crear cuenta
        </a>
    </div>

    <div style="display:flex; gap:4rem; margin-top:4rem; flex-wrap:wrap; justify-content:center; align-items:flex-start;">
        <div style="text-align:center; min-width:120px;">
            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:2rem; color:var(--red);">+{{ count($marcas ?? []) }}</div>
            <div style="font-size:0.78rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--text-muted);">Marcas</div>
        </div>
        <div style="text-align:center; min-width:120px;">
            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:2rem; color:var(--orange); text-transform:uppercase;">Envíos Gratis</div>
            <div style="font-size:0.78rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--text-muted);">En todo el país</div>
        </div>
        <div style="text-align:center; min-width:120px;">
            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:2rem; color:var(--yellow); text-transform:uppercase;">1 AÑO</div>
            <div style="font-size:0.78rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--text-muted);">De Garantía</div>
        </div>
    </div>
</div>

{{-- Carrusel de productos --}}
@if(isset($productosCarrusel) && count($productosCarrusel) > 0)
<div style="padding:3rem 0 2rem;">
    <div style="display:flex !important; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
        <h2 style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.5rem; color:var(--text); margin:0; text-transform:uppercase;">Productos <span style="color:var(--orange);">Destacados</span></h2>
        <a href="/catalogo" style="color:var(--text-muted); font-size:0.85rem; text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='var(--yellow)'" onmouseout="this.style.color='var(--text-muted)'">Ver todos →</a>
    </div>

    <div style="position:relative !important; display:flex !important; align-items:center; gap:0.5rem;" id="productCarousel">
        <button id="carouselPrev" aria-label="Anterior" style="width:42px; height:42px; border-radius:50%; background:var(--dark-2); border:1px solid var(--border); color:var(--text-muted); cursor:pointer; display:flex !important; align-items:center; justify-content:center; flex-shrink:0 !important; z-index:10;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>

        <div style="flex:1 !important; overflow:hidden !important; border-radius:14px;">
            <div id="carouselTrack" style="display:flex !important; transition:transform 0.5s cubic-bezier(0.4,0,0.2,1) !important;">
                @foreach($productosCarrusel as $producto)
                <div class="carousel-slide-item" style="min-width:33.333%; padding:0 0.5rem; box-sizing:border-box; flex-shrink:0 !important;">
                    <div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; overflow:hidden;">
                        <div style="width:100%; height:160px; background:linear-gradient(135deg, rgba(233,48,42,0.08), rgba(236,123,75,0.05)); display:flex !important; align-items:center; justify-content:center; overflow:hidden;">
                            @if(!empty($producto['imagen_url']))
                                <img src="{{ $producto['imagen_url'] }}" alt="{{ $producto['nombre'] }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 12 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            @endif
                        </div>
                        <div style="padding:0.9rem 1rem;">
                            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1rem; color:var(--text); margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $producto['nombre'] }}</div>
                            <div style="font-size:1.05rem; font-weight:600; color:var(--orange); margin-bottom:0.4rem;">${{ number_format($producto['precio'], 0) }} MXN</div>
                            <span class="badge badge-avail" style="font-size:0.65rem;">Disponible ({{ $producto['cantidad'] }})</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button id="carouselNext" aria-label="Siguiente" style="width:42px; height:42px; border-radius:50%; background:var(--dark-2); border:1px solid var(--border); color:var(--text-muted); cursor:pointer; display:flex !important; align-items:center; justify-content:center; flex-shrink:0 !important; z-index:10;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

    {{-- Dots --}}
    <div id="carouselDots" style="display:flex !important; justify-content:center; gap:0.5rem; margin-top:1.25rem;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var track = document.getElementById('carouselTrack');
    var slides = track.querySelectorAll('.carousel-slide-item');
    var prevBtn = document.getElementById('carouselPrev');
    var nextBtn = document.getElementById('carouselNext');
    var dotsContainer = document.getElementById('carouselDots');

    if (slides.length === 0) return;

    var currentIndex = 0;
    var slidesPerView = 3;
    var autoPlayInterval;

    function updateSlidesPerView() {
        if (window.innerWidth < 640) slidesPerView = 1;
        else if (window.innerWidth < 900) slidesPerView = 2;
        else slidesPerView = 3;
    }

    function getMaxIndex() {
        return Math.max(0, slides.length - slidesPerView);
    }

    function updateTrack() {
        var slideWidth = 100 / slidesPerView;
        for (var i = 0; i < slides.length; i++) {
            slides[i].style.minWidth = slideWidth + '%';
        }
        var offset = -(currentIndex * slideWidth);
        track.style.transform = 'translateX(' + offset + '%)';
        updateDots();
    }

    function createDots() {
        dotsContainer.innerHTML = '';
        var maxIdx = getMaxIndex();
        for (var i = 0; i <= maxIdx; i++) {
            var dot = document.createElement('button');
            dot.style.cssText = 'width:8px; height:8px; border-radius:50%; border:none; cursor:pointer; transition:all 0.2s;';
            dot.style.background = (i === 0) ? 'var(--red)' : 'var(--dark-3)';
            dot.setAttribute('aria-label', 'Ir al slide ' + (i + 1));
            (function(idx) {
                dot.addEventListener('click', function() {
                    currentIndex = idx;
                    updateTrack();
                    resetAutoPlay();
                });
            })(i);
            dotsContainer.appendChild(dot);
        }
    }

    function updateDots() {
        var dots = dotsContainer.querySelectorAll('button');
        for (var i = 0; i < dots.length; i++) {
            dots[i].style.background = (i === currentIndex) ? 'var(--red)' : 'var(--dark-3)';
            dots[i].style.transform = (i === currentIndex) ? 'scale(1.3)' : 'scale(1)';
        }
    }

    function goNext() {
        currentIndex = currentIndex >= getMaxIndex() ? 0 : currentIndex + 1;
        updateTrack();
    }

    function goPrev() {
        currentIndex = currentIndex <= 0 ? getMaxIndex() : currentIndex - 1;
        updateTrack();
    }

    function startAutoPlay() {
        autoPlayInterval = setInterval(goNext, 4000);
    }

    function resetAutoPlay() {
        clearInterval(autoPlayInterval);
        startAutoPlay();
    }

    prevBtn.addEventListener('click', function() { goPrev(); resetAutoPlay(); });
    nextBtn.addEventListener('click', function() { goNext(); resetAutoPlay(); });

    window.addEventListener('resize', function() {
        updateSlidesPerView();
        if (currentIndex > getMaxIndex()) currentIndex = getMaxIndex();
        createDots();
        updateTrack();
    });

    updateSlidesPerView();
    createDots();
    updateTrack();
    startAutoPlay();
});
</script>
@endif

@endsection