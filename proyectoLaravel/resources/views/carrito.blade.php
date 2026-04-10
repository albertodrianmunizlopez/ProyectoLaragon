@extends('layouts.app')

@section('contenido')

<div class="section-header">
    <h2>Mi <span>Carrito</span></h2>
    <span class="section-count">{{ count($carrito) }} artículo(s)</span>
</div>

@if(session('error'))
    <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.9rem;">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="background:rgba(60,255,60,.12); border:1px solid rgba(60,255,60,.3); color:#6bff6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif

@if(count($carrito) > 0)

    <div style="display:flex; flex-direction:column; gap:1rem; margin-bottom:2rem;">
        @php $total = 0; @endphp
        @foreach($carrito as $item)
        @php $subtotal = $item['precio'] * $item['cantidad']; $total += $subtotal; @endphp
        <div style="display:flex; align-items:center; justify-content:space-between; background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:1.2rem 1.5rem;">
            <div style="flex:1;">
                <div style="font-weight:600; font-size:1rem; color:var(--text);">{{ $item['nombre'] }}</div>
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.2rem;">SKU: {{ $item['codigo'] }}</div>
            </div>
            <div style="text-align:center; min-width:80px;">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Cantidad</div>
                <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.2rem; color:var(--text);">{{ $item['cantidad'] }}</div>
            </div>
            <div style="text-align:center; min-width:120px;">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Precio unitario</div>
                <div style="font-family:'Rajdhani',sans-serif; font-weight:600; color:var(--text);">${{ number_format($item['precio'], 2) }}</div>
            </div>
            <div style="text-align:center; min-width:120px;">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Subtotal</div>
                <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.1rem; color:var(--orange);">${{ number_format($subtotal, 2) }}</div>
            </div>
            <form method="POST" action="/carrito/eliminar" style="margin-left:1rem;">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $item['producto_id'] }}">
                <button type="submit" style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.3); color:#ff6b6b; padding:0.5rem 0.8rem; border-radius:8px; cursor:pointer; font-size:0.8rem; transition:all 0.2s;" onmouseover="this.style.background='rgba(255,60,60,.3)'" onmouseout="this.style.background='rgba(255,60,60,.15)'">
                    ✕ Quitar
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Resumen --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:2rem; max-width:400px; margin-left:auto;">
        @php
            $iva = $total * 0.16;
            $gran_total = $total + $iva;
        @endphp
        <div style="display:flex; justify-content:space-between; margin-bottom:0.8rem; color:var(--text-muted);">
            <span>Subtotal</span>
            <span style="color:var(--text);">${{ number_format($total, 2) }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:0.8rem; color:var(--text-muted);">
            <span>IVA (16%)</span>
            <span style="color:var(--text);">${{ number_format($iva, 2) }}</span>
        </div>
        <div style="border-top:1px solid var(--border); padding-top:0.8rem; display:flex; justify-content:space-between; font-weight:700;">
            <span style="color:var(--text);">Total</span>
            <span style="color:var(--orange); font-family:'Rajdhani',sans-serif; font-size:1.3rem;">${{ number_format($gran_total, 2) }}</span>
        </div>
        <a href="/checkout" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem; margin-top:1.5rem; display:flex; text-decoration:none;">
            Proceder al pago
        </a>
        <a href="/catalogo" style="display:block; text-align:center; margin-top:0.8rem; color:var(--text-muted); font-size:0.85rem; text-decoration:underline;">
            Seguir comprando
        </a>
    </div>

@else
    <div style="text-align:center; padding:4rem 2rem; color:var(--text-muted);">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
        <p style="font-size:1.1rem; margin-bottom:0.5rem;">Tu carrito está vacío</p>
        <p style="font-size:0.9rem;">Explora nuestro catálogo y encuentra las autopartes que necesitas.</p>
        <a href="/catalogo" class="btn-primary" style="margin-top:1.5rem; display:inline-flex; text-decoration:none;">Ver catálogo</a>
    </div>
@endif

@endsection
