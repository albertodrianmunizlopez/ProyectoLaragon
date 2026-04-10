<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MACUIN Autopartes</title>
    @vite('resources/css/app.css')
</head>
<body>

    <nav class="mcq-navbar">
        <a href="/" class="brand">
            <span class="brand-accent"></span>
            MACUIN
        </a>
        <div class="nav-links">
            <a href="/">Inicio</a>
            <a href="/catalogo">Catálogo</a>
            @if(session('token'))
                <a href="/carrito" style="position:relative; display:inline-flex; align-items:center; gap:0.35rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Carrito
                    @php
                        $carritoCount = count(session('carrito', []));
                    @endphp
                    @if($carritoCount > 0)
                        <span style="position:absolute !important; top:-6px; right:-10px; background:var(--red); color:#fff; font-size:0.65rem; font-weight:700; width:18px; height:18px; border-radius:50%; display:flex !important; align-items:center; justify-content:center;">{{ $carritoCount }}</span>
                    @endif
                </a>

                {{-- User menu --}}
                <div style="position:relative !important;" id="userMenuWrapper">
                    <button id="userMenuTrigger" type="button" style="display:flex !important; align-items:center; gap:0.5rem; background:none; border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:0.35rem 0.7rem 0.35rem 0.9rem; cursor:pointer; transition:0.2s;">
                        <span style="font-family:'Rajdhani',sans-serif; font-weight:600; font-size:0.82rem; letter-spacing:0.08em; color:var(--text-muted);">{{ strtoupper(session('usuario.nombre', 'Usuario')) }}</span>
                        <span style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#E9302A,#EC7B4B); display:flex !important; align-items:center; justify-content:center; color:#fff;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                    </button>

                    <div id="userMenuDropdown" style="position:absolute !important; top:calc(100% + 10px); right:0; width:280px; background:rgba(44,36,36,0.98); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.1); border-radius:14px; box-shadow:0 12px 48px rgba(0,0,0,0.5); opacity:0; visibility:hidden; transform:translateY(-8px) scale(0.97); transition:opacity 0.2s ease, transform 0.2s ease, visibility 0.2s; z-index:300; padding:0.5rem 0; overflow:hidden;">

                        {{-- Header: nombre + email + dirección --}}
                        @php
                            $dirSession = session('direccion');
                            $tieneDireccion = $dirSession && isset($dirSession['tiene_direccion']) && $dirSession['tiene_direccion'];
                        @endphp

                        <div style="padding:0.8rem 1rem;">
                            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.85rem; color:var(--text); letter-spacing:0.04em;">{{ strtoupper(session('usuario.nombre', '')) }} {{ strtoupper(session('usuario.apellidos', '')) }}</div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.15rem;">{{ session('usuario.email', '') }}</div>
                            @if($tieneDireccion)
                                <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.72rem; color:var(--text-muted); margin-top:0.4rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>{{ $dirSession['direccion']['calle'] }} #{{ $dirSession['direccion']['numero'] }}, CP {{ $dirSession['direccion']['codigo_postal'] }}</span>
                                </div>
                            @else
                                <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.72rem; color:var(--text-muted); font-style:italic; margin-top:0.4rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>Sin dirección registrada</span>
                                </div>
                            @endif
                        </div>

                        <div style="height:1px; background:rgba(255,255,255,0.08); margin:0.25rem 0;"></div>

                        {{-- Menu items --}}
                        <a href="/direccion" style="display:flex !important; align-items:center; gap:0.6rem; padding:0.65rem 1rem; font-family:'Rajdhani',sans-serif; font-weight:600; font-size:0.88rem; color:var(--text-muted); text-decoration:none; transition:0.2s; letter-spacing:0.03em;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            @if($tieneDireccion) Modificar dirección @else Agregar dirección @endif
                        </a>

                        <a href="/pedidos" style="display:flex !important; align-items:center; gap:0.6rem; padding:0.65rem 1rem; font-family:'Rajdhani',sans-serif; font-weight:600; font-size:0.88rem; color:var(--text-muted); text-decoration:none; transition:0.2s; letter-spacing:0.03em;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            Mis pedidos
                        </a>

                        <div style="height:1px; background:rgba(255,255,255,0.08); margin:0.25rem 0;"></div>

                        <form method="POST" action="/logout" style="margin:0;">
                            @csrf
                            <button type="submit" style="display:flex !important; align-items:center; gap:0.6rem; padding:0.65rem 1rem; font-family:'Rajdhani',sans-serif; font-weight:600; font-size:0.88rem; color:#ff6b6b; text-decoration:none; background:none; border:none; cursor:pointer; width:100%; text-align:left; letter-spacing:0.03em;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" class="nav-cta">Ingresar</a>
            @endif
        </div>
    </nav>

    <div class="mcq-page">
        @yield('contenido')
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var trigger = document.getElementById('userMenuTrigger');
        var dropdown = document.getElementById('userMenuDropdown');
        var wrapper = document.getElementById('userMenuWrapper');

        if (!trigger || !dropdown) return;

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = dropdown.style.opacity === '1';
            if (isOpen) {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                dropdown.style.transform = 'translateY(-8px) scale(0.97)';
            } else {
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.transform = 'translateY(0) scale(1)';
            }
        });

        document.addEventListener('click', function(e) {
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                dropdown.style.transform = 'translateY(-8px) scale(0.97)';
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                dropdown.style.transform = 'translateY(-8px) scale(0.97)';
            }
        });
    });
    </script>

</body>
</html>