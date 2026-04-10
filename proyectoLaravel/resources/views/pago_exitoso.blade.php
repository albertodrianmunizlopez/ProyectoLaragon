@extends('layouts.app')

@section('contenido')

<div style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:2rem; margin:-2.5rem -2.5rem 0; background:radial-gradient(ellipse 50% 60% at 50% 100%, rgba(233,48,42,0.16) 0%, transparent 70%), var(--surface);">
    <div style="text-align:center; max-width:520px;">

        {{-- Animated checkmark --}}
        <div class="pago-exitoso-icon">
            <svg class="checkmark-svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h2 style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:2.2rem; color:var(--text); margin-bottom:0.5rem;">
            ¡Pago <span style="color:var(--orange);">Exitoso!</span>
        </h2>

        <p style="color:var(--text-muted); font-size:1rem; margin-bottom:2rem; line-height:1.6;">
            Tu pedido ha sido procesado correctamente.<br>
            Recibirás actualizaciones sobre el estado de tu envío.
        </p>

        @if($codigoPedido)
        <div style="background:var(--dark); border:1px solid var(--border); border-radius:12px; padding:1.2rem 1.5rem; margin-bottom:2rem; display:inline-block;">
            <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.3rem;">Número de pedido</div>
            <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.6rem; color:var(--orange); letter-spacing:0.05em;">{{ $codigoPedido }}</div>
        </div>
        @endif

        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="/catalogo" class="btn-primary" style="text-decoration:none; padding:0.85rem 2rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> Seguir comprando
            </a>
            <a href="/pedidos" class="btn-ghost" style="text-decoration:none; padding:0.85rem 2rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg> Ver mis pedidos
            </a>
        </div>

    </div>
</div>

@endsection
