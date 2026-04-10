@extends('layouts.app')

@section('contenido')

<div class="section-header">
    <h2>Mis <span>Pedidos</span></h2>
    <span class="section-count">{{ count($pedidos) }} pedido(s)</span>
</div>

@if(session('success'))
    <div style="background:rgba(60,255,60,.12); border:1px solid rgba(60,255,60,.3); color:#6bff6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.9rem;">
        {{ session('error') }}
    </div>
@endif

@forelse($pedidos as $pedido)
<div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; padding:1.5rem 1.75rem; margin-bottom:1rem; display:flex; align-items:center; gap:1.25rem; position:relative; overflow:hidden; transition:border-color 0.2s;">
    <div style="position:absolute; left:0; top:0; bottom:0; width:3px; background:linear-gradient(180deg, var(--red), var(--orange));"></div>

    {{-- Imagen del primer producto --}}
    @if(!empty($pedido['primer_producto_imagen']))
        <img src="{{ $pedido['primer_producto_imagen'] }}" alt="Producto" style="width:64px; height:64px; object-fit:cover; border-radius:10px; flex-shrink:0; background:var(--dark-2);" onerror="this.style.display='none'">
    @else
        <div style="width:64px; height:64px; border-radius:10px; flex-shrink:0; background:var(--dark-2); display:flex; align-items:center; justify-content:center;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
    @endif

    <div style="flex:1;">
        <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.1rem; color:var(--text); margin-bottom:0.25rem;">Pedido {{ $pedido['codigo_pedido'] ?? '#' . $pedido['id'] }}</div>
        <div style="font-size:0.82rem; color:var(--text-muted); margin-bottom:0.6rem;">{{ \Carbon\Carbon::parse($pedido['fecha_pedido'])->format('d \\d\\e F \\d\\e Y') }}</div>
        @php
            $estado = $pedido['estado_pedido'] ?? 'pendiente';
            $colores = [
                'pendiente'  => 'background:rgba(255,180,40,.2); color:#ffb428;',
                'surtido'    => 'background:rgba(100,180,255,.2); color:#64b4ff;',
                'enviado'    => 'background:rgba(100,180,255,.2); color:#64b4ff;',
                'en_camino'  => 'background:rgba(100,180,255,.2); color:#64b4ff;',
                'entregado'  => 'background:rgba(60,255,60,.2); color:#6bff6b;',
                'cancelado'  => 'background:rgba(255,60,60,.2); color:#ff6b6b;',
            ];
            $estilo = $colores[$estado] ?? 'background:rgba(255,255,255,.1); color:var(--text-muted);';
        @endphp
        <span style="{{ $estilo }} padding:0.3rem 0.8rem; border-radius:6px; font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; display:inline-block;">
            {{ ucfirst(str_replace('_', ' ', $estado)) }}
        </span>
    </div>

    <div style="display:flex; align-items:center; gap:1rem;">
        <div style="text-align:right;">
            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.3rem; color:var(--orange);">
                ${{ number_format($pedido['total'], 2) }} MXN
            </div>
        </div>
        <a href="/pedidos/{{ $pedido['id'] }}" style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.85rem; letter-spacing:0.1em; text-transform:uppercase; background:var(--red); color:#fff; border:none; border-radius:8px; padding:0.6rem 1.4rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem; transition:background 0.2s, box-shadow 0.2s;">
            Ver detalles
        </a>
    </div>
</div>
@empty
<div style="text-align:center; padding:4rem 2rem; color:var(--text-muted);">
    <div style="display:flex; justify-content:center; margin-bottom:1rem;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></div>
    <p style="font-size:1.1rem; margin-bottom:0.5rem;">No tienes pedidos aún</p>
    <p style="font-size:0.9rem;">Explora nuestro catálogo y realiza tu primer compra.</p>
    <a href="/catalogo" class="btn-primary" style="margin-top:1.5rem; display:inline-flex; text-decoration:none;">Ver catálogo</a>
</div>
@endforelse

@endsection