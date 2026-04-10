@extends('layouts.app')

@section('contenido')

<div class="auth-wrap" style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:2rem; margin:-2.5rem -2.5rem 0; background:radial-gradient(ellipse 50% 60% at 50% 100%, rgba(233,48,42,0.16) 0%, transparent 70%), var(--surface);">
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
                    {{ $dirData['direccion']['calle'] }}
                    @if($dirData['direccion']['numero'])
                        #{{ $dirData['direccion']['numero'] }}
                    @else
                        S/N
                    @endif
                    <br>
                    @if($dirData['direccion']['colonia'])
                        Col. {{ $dirData['direccion']['colonia'] }}<br>
                    @endif
                    @if($dirData['direccion']['localidad'])
                        {{ $dirData['direccion']['localidad'] }}<br>
                    @endif
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

            <div style="display:flex; gap:1rem; align-items:flex-end;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero" id="inputNumero" class="form-input" placeholder="456"
                           value="{{ old('numero', $dirData['direccion']['numero'] ?? '') }}"
                           {{ (isset($dirData['direccion']) && !$dirData['direccion']['numero']) ? 'disabled' : '' }}>
                </div>
                <div style="flex:1; display:flex; align-items:center; margin-bottom:1rem;">
                    <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer; color:var(--text-muted); font-size:0.85rem; user-select:none;">
                        <span style="position:relative; width:20px; height:20px; flex-shrink:0;">
                            <input type="checkbox" name="sin_numero" id="chkSinNumero" value="1"
                                   {{ (isset($dirData['direccion']) && !$dirData['direccion']['numero']) ? 'checked' : '' }}
                                   onchange="toggleNumero(this)"
                                   style="position:absolute; opacity:0; width:100%; height:100%; cursor:pointer; margin:0;">
                            <span id="chkBox" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:5px; border:2px solid rgba(255,255,255,.18); background:rgba(255,255,255,.06); transition:all .2s;"></span>
                        </span>
                        Mi calle no tiene número
                    </label>
                </div>
            </div>

            <div style="display:flex; gap:1rem;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Colonia / Barrio</label>
                    <input type="text" name="colonia" class="form-input" placeholder="Centro"
                           value="{{ old('colonia', $dirData['direccion']['colonia'] ?? '') }}">
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Localidad</label>
                    <input type="text" name="localidad" class="form-input" placeholder="Santiago de Querétaro"
                           value="{{ old('localidad', $dirData['direccion']['localidad'] ?? '') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Código Postal</label>
                <input type="text" name="codigo_postal" class="form-input" placeholder="76000" required maxlength="5"
                       value="{{ old('codigo_postal', $dirData['direccion']['codigo_postal'] ?? '') }}">
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

<script>
function updateChkStyle() {
    var chk = document.getElementById('chkSinNumero');
    var box = document.getElementById('chkBox');
    if (chk.checked) {
        box.style.background = 'linear-gradient(135deg, #e9302a, #ff5a52)';
        box.style.borderColor = '#e9302a';
        box.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
    } else {
        box.style.background = 'rgba(255,255,255,.06)';
        box.style.borderColor = 'rgba(255,255,255,.18)';
        box.innerHTML = '';
    }
}
function toggleNumero(chk) {
    var inp = document.getElementById('inputNumero');
    if (chk.checked) {
        inp.value = '';
        inp.disabled = true;
        inp.removeAttribute('required');
    } else {
        inp.disabled = false;
    }
    updateChkStyle();
}
document.addEventListener('DOMContentLoaded', updateChkStyle);
</script>

@endsection
