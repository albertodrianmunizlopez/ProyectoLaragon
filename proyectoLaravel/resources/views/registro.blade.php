@extends('layouts.app')

@section('contenido')

<div class="auth-wrap" style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:2rem; margin:-2.5rem -2.5rem 0; background:radial-gradient(ellipse 50% 60% at 50% 100%, rgba(233,48,42,0.16) 0%, transparent 70%), var(--surface);">
    <div class="auth-card">
        <div class="auth-brand">MACUIN Autopartes</div>

        <h2>Crear cuenta</h2>
        <p class="auth-subtitle">Únete y comienza a comprar autopartes</p>

        @if(session('error'))
            <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        <div id="email-error-banner" style="display:none; background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.9rem;">
            Este correo electrónico ya está registrado. <a href="/login" style="color:#ff6b6b; text-decoration:underline;">¿Quieres iniciar sesión?</a>
        </div>

        <form method="POST" action="/registro" id="formRegistro" onsubmit="return validarRegistro()">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label class="form-label">Nombre(s)</label>
                    <input type="text" name="nombre" class="form-input" placeholder="Ej: Carlos"
                           pattern="^[A-Za-záéíóúñÁÉÍÓÚÑüÜ\s]+$" title="Solo se permiten letras"
                           id="reg-nombre" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-input" placeholder="Ej: Herrera Mendoza"
                           pattern="^[A-Za-záéíóúñÁÉÍÓÚÑüÜ\s]+$" title="Solo se permiten letras"
                           id="reg-apellidos" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-input" placeholder="correo@ejemplo.com"
                       id="reg-email" required onblur="verificarEmailLaravel(this.value)">
                <span id="email-inline-error" style="display:none; color:#ff6b6b; font-size:0.8rem; margin-top:4px;">
                    Este correo ya está registrado.
                </span>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" placeholder="Mínimo 6 caracteres"
                       minlength="6" required>
            </div>

            <div style="margin-top:1.75rem;">
                <button type="submit" class="btn-primary" id="btnRegistro" style="width:100%; justify-content:center; padding:0.85rem;">
                    Crear cuenta
                </button>
            </div>
        </form>

        <div class="auth-footer">
            ¿Ya tienes cuenta?
            <a href="/login" class="form-link">Iniciar sesión</a>
        </div>
    </div>
</div>

<script>
    let emailRegistrado = false;

    async function verificarEmailLaravel(email) {
        const banner = document.getElementById('email-error-banner');
        const inlineErr = document.getElementById('email-inline-error');
        const btn = document.getElementById('btnRegistro');

        if (!email || !email.includes('@')) {
            banner.style.display = 'none';
            inlineErr.style.display = 'none';
            return;
        }

        try {
            const resp = await fetch('/api-check-email?email=' + encodeURIComponent(email));
            const data = await resp.json();
            if (data.exists) {
                banner.style.display = 'block';
                inlineErr.style.display = 'block';
                emailRegistrado = true;
                btn.disabled = true;
                btn.style.opacity = '0.5';
            } else {
                banner.style.display = 'none';
                inlineErr.style.display = 'none';
                emailRegistrado = false;
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        } catch(e) {
            banner.style.display = 'none';
            inlineErr.style.display = 'none';
        }
    }

    function validarRegistro() {
        const nombre = document.getElementById('reg-nombre').value;
        const apellidos = document.getElementById('reg-apellidos').value;
        const letrasRegex = /^[A-Za-záéíóúñÁÉÍÓÚÑüÜ\s]+$/;

        if (!letrasRegex.test(nombre)) {
            alert('El nombre solo puede contener letras.');
            return false;
        }
        if (!letrasRegex.test(apellidos)) {
            alert('Los apellidos solo pueden contener letras.');
            return false;
        }
        if (emailRegistrado) {
            alert('El correo ya está registrado. Usa otro o inicia sesión.');
            return false;
        }
        return true;
    }
</script>

@endsection