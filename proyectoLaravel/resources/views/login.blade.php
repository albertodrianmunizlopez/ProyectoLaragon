@extends('layouts.app')

@section('contenido')

<div class="auth-wrap" style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:2rem; margin:-2.5rem -2.5rem 0; background:radial-gradient(ellipse 50% 60% at 50% 100%, rgba(233,48,42,0.16) 0%, transparent 70%), var(--surface);">
    <div class="auth-card">
        <div class="auth-brand">MACUIN Autopartes</div>

        <h2>Bienvenido</h2>
        <p class="auth-subtitle">Ingresa tus credenciales para continuar</p>

        @if(session('error'))
            <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background:rgba(60,255,60,.15); border:1px solid rgba(60,255,60,.4); color:#6bff6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-input" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div style="margin-top:1.75rem;">
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem;">
                    Iniciar sesión
                </button>
            </div>
        </form>

        <div class="auth-footer">
            ¿No tienes cuenta?
            <a href="/registro" class="form-link">Regístrate gratis</a>
        </div>
    </div>
</div>

@endsection