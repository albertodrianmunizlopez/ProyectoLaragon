@extends('layouts.app')

@section('contenido')

<div class="flex justify-center mt-20">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Registro</h2>

        <form>
            <div class="mb-4">
                <label>Nombre</label>
                <input type="text" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div class="mb-4">
                <label>Correo</label>
                <input type="email" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div class="mb-4">
                <label>Contraseña</label>
                <input type="password" class="w-full border rounded-lg px-3 py-2">
            </div>

            <button class="w-full bg-green-600 text-white py-2 rounded-lg">
                Registrarse
            </button>
        </form>
    </div>
</div>

@endsection