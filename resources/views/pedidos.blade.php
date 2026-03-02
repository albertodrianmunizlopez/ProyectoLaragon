@extends('layouts.app')

@section('contenido')

<h2 class="text-2xl font-bold mb-6">Mis Pedidos</h2>

<div class="bg-white p-6 rounded-xl shadow-md">
    <p><strong>Pedido #001</strong></p>
    <p>Fecha: 10/03/2026</p>
    <p>Estatus: <span class="text-yellow-600 font-bold">En proceso</span></p>

    <div class="mt-4 space-x-2">
        <button class="bg-red-600 text-white px-4 py-2 rounded-lg">
            Cancelar
        </button>

        <button class="bg-green-600 text-white px-4 py-2 rounded-lg">
            Descargar PDF
        </button>
    </div>
</div>

@endsection