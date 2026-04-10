@extends('layouts.app')

@section('contenido')

<div style="max-width:800px; margin:0 auto;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem;">
        <div>
            <a href="/pedidos" style="color:var(--text-muted); font-size:0.85rem; text-decoration:underline; margin-bottom:0.5rem; display:inline-block;">← Volver a mis pedidos</a>
            <h2 style="margin:0; font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.8rem;">
                Pedido <span style="color:var(--orange);">{{ $pedido['codigo_pedido'] }}</span>
            </h2>
        </div>
        @php
            $estado = $pedido['estado_pedido'] ?? 'pendiente';
            $colores = [
                'pendiente'  => 'background:rgba(255,180,40,.2); color:#ffb428; border-color:rgba(255,180,40,.3);',
                'surtido'    => 'background:rgba(100,180,255,.2); color:#64b4ff; border-color:rgba(100,180,255,.3);',
                'enviado'    => 'background:rgba(100,180,255,.2); color:#64b4ff; border-color:rgba(100,180,255,.3);',
                'en_camino'  => 'background:rgba(100,180,255,.2); color:#64b4ff; border-color:rgba(100,180,255,.3);',
                'entregado'  => 'background:rgba(60,255,60,.2); color:#6bff6b; border-color:rgba(60,255,60,.3);',
                'cancelado'  => 'background:rgba(255,60,60,.2); color:#ff6b6b; border-color:rgba(255,60,60,.3);',
            ];
            $estilo = $colores[$estado] ?? '';
        @endphp
        <span style="{{ $estilo }} padding:0.5rem 1.2rem; border-radius:8px; font-size:0.85rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; border:1px solid;">
            {{ ucfirst(str_replace('_', ' ', $estado)) }}
        </span>
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

    {{-- Información general --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:1.3rem;">
            <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Fecha del pedido</div>
            <div style="color:var(--text); font-weight:500;">{{ $pedido['fecha_pedido'] ?? 'N/A' }}</div>
        </div>
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:1.3rem;">
            <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Dirección de envío</div>
            <div style="color:var(--text); font-weight:500; font-size:0.9rem;">{{ $pedido['direccion_completa'] ?? 'No especificada' }}</div>
        </div>
    </div>

    {{-- Productos --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:1.5rem; margin-bottom:2rem;">
        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Productos del pedido</div>
        
        @foreach($pedido['productos'] ?? [] as $producto)
        <div style="display:flex; align-items:center; justify-content:space-between; padding:0.8rem 0; {{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
            <div style="flex:1;">
                <div style="font-weight:600; color:var(--text);">{{ $producto['producto_nombre'] ?? 'Producto' }}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">SKU: {{ $producto['producto_codigo'] ?? 'N/A' }}</div>
            </div>
            <div style="text-align:center; min-width:80px;">
                <div style="font-size:0.7rem; color:var(--text-muted);">CANT.</div>
                <div style="font-weight:600; color:var(--text);">{{ $producto['cantidad'] }}</div>
            </div>
            <div style="text-align:center; min-width:100px;">
                <div style="font-size:0.7rem; color:var(--text-muted);">P. UNIT.</div>
                <div style="color:var(--text);">${{ number_format($producto['precio_unitario'], 2) }}</div>
            </div>
            <div style="text-align:right; min-width:100px;">
                <div style="font-size:0.7rem; color:var(--text-muted);">SUBTOTAL</div>
                <div style="font-weight:700; color:var(--orange);">${{ number_format($producto['precio_unitario'] * $producto['cantidad'], 2) }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Totales --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:1.5rem; max-width:350px; margin-left:auto; margin-bottom:2rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:0.6rem; color:var(--text-muted);">
            <span>Subtotal</span>
            <span style="color:var(--text);">${{ number_format($pedido['subtotal'], 2) }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:0.6rem; color:var(--text-muted);">
            <span>IVA (16%)</span>
            <span style="color:var(--text);">${{ number_format($pedido['impuestos'], 2) }}</span>
        </div>
        <div style="border-top:1px solid var(--border); padding-top:0.6rem; display:flex; justify-content:space-between; font-weight:700;">
            <span style="color:var(--text);">Total</span>
            <span style="color:var(--orange); font-family:'Rajdhani',sans-serif; font-size:1.3rem;">${{ number_format($pedido['total'], 2) }} MXN</span>
        </div>
    </div>

    {{-- Acciones --}}
    @if(in_array($estado, ['pendiente', 'surtido']))
    <div style="display:flex; justify-content:flex-end; gap:1rem;">
        <form method="POST" action="/pedidos/{{ $pedido['id'] }}/cancelar" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este pedido?');">
            @csrf
            <button type="submit" class="btn-ghost" style="padding:0.7rem 1.5rem; font-size:0.9rem; border-color:rgba(255,60,60,.4); color:#ff6b6b;">
                Cancelar pedido
            </button>
        </form>
    </div>
    @endif

</div>

@endsection
