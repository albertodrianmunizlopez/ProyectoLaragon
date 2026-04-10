@extends('layouts.app')

@section('contenido')

<div class="auth-wrap" style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:2rem; margin:-2.5rem -2.5rem 0; background:radial-gradient(ellipse 50% 60% at 50% 100%, rgba(233,48,42,0.16) 0%, transparent 70%), var(--surface);">
    <div class="auth-card" style="max-width:480px;">
        <div class="auth-brand">MACUIN Autopartes</div>

        <h2>Mi Teléfono</h2>

        @if(session('error'))
            <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background:rgba(60,255,60,.12); border:1px solid rgba(60,255,60,.3); color:#6bff6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($telefono)
            <div style="background:rgba(60,255,60,.08); border:1px solid rgba(60,255,60,.2); border-radius:12px; padding:1.2rem; margin-bottom:1.5rem;">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Teléfono actual</div>
                <div style="display:flex; align-items:center; gap:0.5rem; color:var(--text);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.57 3.18 2 2 0 0 1 3.54 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.51a16 16 0 0 0 6 6l.87-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                    <span style="font-size:1.05rem;">{{ $telefono }}</span>
                </div>
            </div>
            <p class="auth-subtitle" style="margin-bottom:1rem;">¿Quieres actualizar tu número? Modifícalo abajo.</p>
        @else
            <p class="auth-subtitle">Registra tu número de teléfono para que podamos contactarte sobre tus pedidos.</p>
        @endif

        <form method="POST" action="/telefono">
            @csrf

            <div class="form-group">
                <label class="form-label">Número de teléfono</label>
                <input type="tel" name="telefono" class="form-input" placeholder="Ej: 6141234567"
                       pattern="^\d{10}$" maxlength="10"
                       title="Debe ser exactamente 10 dígitos numéricos"
                       value="{{ old('telefono', $telefono ?? '') }}"
                       required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>

            <div style="margin-top:1.75rem;">
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem;">
                    Guardar teléfono
                </button>
            </div>
        </form>

        <div class="auth-footer" style="margin-top:1.5rem;">
            <a href="/catalogo" class="form-link">← Volver al catálogo</a>
        </div>
    </div>
</div>

@endsection
