@extends('layouts.app')

@section('contenido')

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Filtro de Aceite</h2>

    <p><strong>Marca:</strong> Bosch</p>
    <p><strong>Precio:</strong> $250 MXN</p>
    <p><strong>Disponibilidad:</strong> 12 piezas</p>

    <form method="POST" action="#">
        @csrf
        <div class="mt-4">
            <label>Cantidad:</label>
            <input type="number" min="1" class="border px-2 py-1 w-20">
        </div>

        <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded">
            Agregar al pedido
        </button>
    </form>
</div>

@endsection