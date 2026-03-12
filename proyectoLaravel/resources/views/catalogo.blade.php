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

    <!-- Producto 3 -->
        <div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/pastillas_freno.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Pastillas de Freno Brembo</h3>
    <p class="text-gray-600">$1,200 MXN</p>
    <p class="text-green-600 font-semibold">Disponible</p>
    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
        Agregar al Pedido
    </button>
    </div>

    <!-- Producto 4 -->
    <div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/amortiguador.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Amortiguador Monroe</h3>
    <p class="text-gray-600">$950 MXN</p>
    <p class="text-green-600 font-semibold">Disponible</p>
    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
        Agregar al Pedido
    </button>
    </div>

    <!-- Producto 5 -->
    <div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/faro.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Faro LED Universal</h3>
    <p class="text-gray-600">$780 MXN</p>
    <p class="text-red-600 font-semibold">Agotado</p>
    <button class="mt-3 bg-gray-400 text-white px-4 py-2 rounded-lg w-full" disabled>
        No disponible
    </button>
    </div>

    <!-- Producto 6 -->
    <div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/bateria.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Batería LTH 12V</h3>
    <p class="text-gray-600">$2,300 MXN</p>
    <p class="text-green-600 font-semibold">Disponible</p>
    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
        Agregar al Pedido
    </button>
    </div>

    <!-- Producto 7 -->
<div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/filtro_aire.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Filtro de Aire Bosch</h3>
    <p class="text-gray-600">$320 MXN</p>
    <p class="text-green-600 font-semibold">Disponible</p>
    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
        Agregar al Pedido
    </button>
</div>

<!-- Producto 8 -->
<div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/radiador.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Radiador de Aluminio</h3>
    <p class="text-gray-600">$3,500 MXN</p>
    <p class="text-green-600 font-semibold">Disponible</p>
    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
        Agregar al Pedido
    </button>
</div>

<!-- Producto 9 -->
<div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/llanta.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Llanta Michelin 205/55 R16</h3>
    <p class="text-gray-600">$2,100 MXN</p>
    <p class="text-red-600 font-semibold">Agotado</p>
    <button class="mt-3 bg-gray-400 text-white px-4 py-2 rounded-lg w-full" disabled>
        No disponible
    </button>
</div>

<!-- Producto 10 -->
<div class="bg-white rounded-xl shadow-md p-4">
    <img src="{{ asset('images/correa.jpg') }}" class="w-full h-40 object-cover rounded-lg">
    <h3 class="font-bold mt-3">Correa de Distribución Gates</h3>
    <p class="text-gray-600">$850 MXN</p>
    <p class="text-green-600 font-semibold">Disponible</p>
    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
        Agregar al Pedido
    </button>
</div>


</div>

@endsection