@extends('layouts.app')

@section('contenido')

@php
    $estado = $pedido['estado_pedido'] ?? 'pendiente';
    $steps = ['pendiente', 'surtido', 'enviado', 'entregado'];
    $stepLabels = [
        'pendiente' => 'Pendiente',
        'surtido'   => 'Surtido en Almacén',
        'enviado'   => 'Enviado',
        'entregado' => 'Entregado',
    ];
    $stepMap = ['pendiente' => 0, 'surtido' => 1, 'enviado' => 2, 'entregado' => 3, 'cancelado' => -1];
    $currentIdx = $stepMap[$estado] ?? 0;

    $badgeStyles = [
        'pendiente' => 'background:rgba(234,179,8,0.12); color:#eab308; border:1px solid rgba(234,179,8,0.3);',
        'surtido'   => 'background:rgba(59,130,246,0.12); color:#60a5fa; border:1px solid rgba(59,130,246,0.3);',
        'enviado'   => 'background:rgba(139,92,246,0.12); color:#a78bfa; border:1px solid rgba(139,92,246,0.3);',
        'en_camino' => 'background:rgba(139,92,246,0.12); color:#a78bfa; border:1px solid rgba(139,92,246,0.3);',
        'entregado' => 'background:rgba(34,197,94,0.12); color:#4ade80; border:1px solid rgba(34,197,94,0.3);',
        'cancelado' => 'background:rgba(239,68,68,0.12); color:#f87171; border:1px solid rgba(239,68,68,0.3);',
    ];
    $badgeEstilo = $badgeStyles[$estado] ?? 'background:rgba(255,255,255,0.1); color:var(--text-muted);';
@endphp

<div style="max-width:1100px; margin:0 auto;">

    <a href="/pedidos" style="color:var(--text-muted); font-size:0.85rem; text-decoration:underline; margin-bottom:1.5rem; display:inline-block;">← Volver a mis pedidos</a>

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

    <div style="display:grid; grid-template-columns:1fr 320px; gap:1.5rem; align-items:start;">

        {{-- ═══ COLUMNA IZQUIERDA: Detalles ═══ --}}
        <div>
            <h2 style="margin:0 0 1.5rem; font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.8rem;">
                Pedido <span style="color:var(--orange);">{{ $pedido['codigo_pedido'] }}</span>
            </h2>

            {{-- Info general --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.5rem;">
                <div style="background:var(--dark); border:1px solid var(--border); border-radius:12px; padding:1.3rem;">
                    <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Fecha del pedido</div>
                    <div style="color:var(--text); font-weight:500;">{{ \Carbon\Carbon::parse($pedido['fecha_pedido'])->format('d \\d\\e F \\d\\e Y') }}</div>
                </div>
                <div style="background:var(--dark); border:1px solid var(--border); border-radius:12px; padding:1.3rem;">
                    <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Dirección de envío</div>
                    <div style="color:var(--text); font-weight:500; font-size:0.9rem;">{{ $pedido['direccion_completa'] ?? 'No especificada' }}</div>
                </div>
            </div>

            {{-- Productos --}}
            <div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; padding:1.5rem; margin-bottom:1.5rem;">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Productos del pedido</div>
                @foreach($pedido['productos'] ?? [] as $producto)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.8rem 0; {{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <div style="flex:1;">
                        <div style="font-weight:600; color:var(--text);">{{ $producto['producto_nombre'] ?? 'Producto' }}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">SKU: {{ $producto['producto_codigo'] ?? 'N/A' }}</div>
                    </div>
                    <div style="text-align:center; min-width:70px;">
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
            <div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; padding:1.5rem; max-width:350px; margin-left:auto; margin-bottom:1.5rem;">
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

            {{-- Cancelar --}}
            @if(in_array($estado, ['pendiente', 'surtido']))
            <div style="display:flex; justify-content:flex-end;">
                <form method="POST" action="/pedidos/{{ $pedido['id'] }}/cancelar" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este pedido?');">
                    @csrf
                    <button type="submit" style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.9rem; letter-spacing:0.1em; text-transform:uppercase; background:transparent; color:#ff6b6b; border:1px solid rgba(255,60,60,.4); border-radius:8px; padding:0.7rem 1.5rem; cursor:pointer; transition:all 0.2s;">
                        Cancelar pedido
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- ═══ COLUMNA DERECHA: Timeline ═══ --}}
        <div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; overflow:hidden; position:sticky; top:84px;">
            {{-- Header --}}
            <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:0.5rem; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.6;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Seguimiento
            </div>

            <div style="padding:1.25rem;">
                {{-- Badge de estatus actual --}}
                <div style="text-align:center; padding:0.75rem 0 1.25rem;">
                    <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); margin-bottom:0.6rem;">Estatus actual</div>
                    <span style="{{ $badgeEstilo }} display:inline-block; padding:0.5rem 1.25rem; border-radius:25px; font-size:0.82rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase;">
                        {{ ucfirst(str_replace('_', ' ', $estado)) }}
                    </span>
                </div>

                {{-- Timeline --}}
                <div style="position:relative; margin:1rem 0;">
                    {{-- Track vertical --}}
                    <div style="position:absolute; left:8px; top:0; bottom:0; width:2px; background:var(--border);"></div>

                    @foreach($steps as $i => $step)
                    @php
                        if ($estado === 'cancelado') {
                            $dotStyle = 'background:rgba(255,255,255,0.1); border:2px solid rgba(255,255,255,0.15);';
                            $textStyle = 'color:var(--text-muted);';
                            $textWeight = '';
                        } elseif ($i < $currentIdx) {
                            $dotStyle = 'background:rgba(34,197,94,0.6); border:2px solid rgba(34,197,94,0.8);';
                            $textStyle = 'color:rgba(255,255,255,0.65);';
                            $textWeight = '';
                        } elseif ($i === $currentIdx) {
                            $dotStyle = 'background:rgba(233,48,42,0.7); border:2px solid #E9302A; box-shadow:0 0 8px rgba(233,48,42,0.4);';
                            $textStyle = 'color:var(--text);';
                            $textWeight = 'font-weight:600;';
                        } else {
                            $dotStyle = 'background:rgba(255,255,255,0.1); border:2px solid rgba(255,255,255,0.15);';
                            $textStyle = 'color:var(--text-muted);';
                            $textWeight = '';
                        }
                    @endphp
                    <div style="display:flex; align-items:center; gap:0.75rem; padding:0.5rem 0; position:relative;">
                        <div style="width:12px; height:12px; border-radius:50%; flex-shrink:0; position:relative; z-index:1; margin-left:2px; {{ $dotStyle }}"></div>
                        <span style="font-size:0.85rem; {{ $textStyle }} {{ $textWeight }}">{{ $stepLabels[$step] }}</span>
                    </div>
                    @endforeach

                    @if($estado === 'cancelado')
                    <div style="display:flex; align-items:center; gap:0.75rem; padding:0.5rem 0; position:relative;">
                        <div style="width:12px; height:12px; border-radius:50%; flex-shrink:0; position:relative; z-index:1; margin-left:2px; background:rgba(239,68,68,0.7); border:2px solid #f87171;"></div>
                        <span style="font-size:0.85rem; color:#f87171; font-weight:600;">Cancelado</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
