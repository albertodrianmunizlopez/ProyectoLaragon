@extends('layouts.app')

@section('contenido')

<div class="auth-wrap">
    <div class="auth-card" style="max-width:520px;">
        <div class="auth-brand">MACUIN Autopartes</div>

        <h2>Mi Dirección de Envío</h2>

        @if(session('aviso'))
            <div style="background:rgba(255,180,40,.15); border:1px solid rgba(255,180,40,.4); color:#ffb428; padding:0.85rem 1rem; border-radius:10px; margin-bottom:1.2rem; font-size:0.9rem; line-height:1.5;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> {{ session('aviso') }}
            </div>
        @endif

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

        @if(isset($dirData) && $dirData && $dirData['tiene_direccion'])
            {{-- Ya tiene dirección, mostrarla --}}
            <div style="background:rgba(60,255,60,.08); border:1px solid rgba(60,255,60,.2); border-radius:12px; padding:1.2rem; margin-bottom:1.5rem;">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Dirección actual</div>
                <div style="color:var(--text); line-height:1.6;">
                    {{ $dirData['direccion']['calle'] }} #{{ $dirData['direccion']['numero'] }}<br>
                    CP {{ $dirData['direccion']['codigo_postal'] }}<br>
                    {{ $dirData['direccion']['municipio'] }}, {{ $dirData['direccion']['estado'] }}
                </div>
            </div>
            <p class="auth-subtitle" style="margin-bottom:1rem;">¿Quieres actualizar tu dirección? Llena el formulario.</p>
        @else
            <p class="auth-subtitle">Registra tu dirección para poder recibir tus pedidos</p>
        @endif

        <form method="POST" action="/direccion">
            @csrf

            <div class="form-group">
                <label class="form-label">Calle</label>
                <input type="text" name="calle" class="form-input" placeholder="Av. Universidad" required
                       value="{{ old('calle', $dirData['direccion']['calle'] ?? '') }}">
            </div>

            <div style="display:flex; gap:1rem;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero" class="form-input" placeholder="456" required
                           value="{{ old('numero', $dirData['direccion']['numero'] ?? '') }}">
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Código Postal</label>
                    <input type="text" name="codigo_postal" class="form-input" placeholder="76000" required maxlength="5"
                           value="{{ old('codigo_postal', $dirData['direccion']['codigo_postal'] ?? '') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Municipio</label>
                <input type="text" name="municipio" class="form-input" placeholder="Querétaro" required
                       value="{{ old('municipio', $dirData['direccion']['municipio'] ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Estado</label>
                <input type="text" name="estado" class="form-input" placeholder="Querétaro" required
                       value="{{ old('estado', $dirData['direccion']['estado'] ?? '') }}">
            </div>

            <div style="margin-top:1.75rem;">
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem;">
                    Guardar dirección
                </button>
            </div>
        </form>

        <div class="auth-footer" style="margin-top:1.5rem;">
            <a href="/catalogo" class="form-link">← Volver al catálogo</a>
        </div>
    </div>
</div>

@endsection
