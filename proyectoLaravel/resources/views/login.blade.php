@extends('layouts.app')

@section('contenido')

<div class="flex justify-center mt-20">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h2>

        <form>
            <div class="mb-4">
                <label>Correo</label>
                <input type="email" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div class="mb-4">
                <label>Contraseña</label>
                <input type="password" class="w-full border rounded-lg px-3 py-2">
            </div>

            <button class="w-full bg-blue-600 text-white py-2 rounded-lg">
                Ingresar
            </button>
        </form>

        <p class="text-sm mt-4 text-center">
            ¿No tienes cuenta?
            <a href="/registro" class="text-blue-600">Regístrate</a>
        </p>
    </div>
</div>

@endsection