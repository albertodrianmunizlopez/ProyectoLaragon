@extends('layouts.app')

@section('contenido')

<h2 class="text-2xl font-bold mb-6">Catálogo de Autopartes</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Producto 1 -->
    <div class="bg-white rounded-xl shadow-md p-4">
        <img src="{{ asset('images/filtro.jpg') }}" class="w-full h-40 object-cover rounded-lg">
        <h3 class="font-bold mt-3">Filtro de Aceite</h3>
        <p class="text-gray-600">$250 MXN</p>
        <p class="text-green-600 font-semibold">Disponible</p>
        <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
            Agregar al Pedido
        </button>
    </div>

    <!-- Producto 2 -->
    <div class="bg-white rounded-xl shadow-md p-4">
        <img src="{{ asset('images/bujia.jpg') }}" class="w-full h-40 object-cover rounded-lg">
        <h3 class="font-bold mt-3">Bujía NGK</h3>
        <p class="text-gray-600">$120 MXN</p>
        <p class="text-red-600 font-semibold">Agotado</p>
        <button class="mt-3 bg-gray-400 text-white px-4 py-2 rounded-lg w-full" disabled>
            No disponible
        </button>
    </div>

</div>

@endsection