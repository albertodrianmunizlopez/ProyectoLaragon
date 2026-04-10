@extends('layouts.app')

@section('contenido')

<div class="detail-card">
    <img src="{{ asset('images/filtro.jpg') }}" class="detail-img" alt="Filtro de Aceite">

    <div class="detail-body">
        <div class="detail-tag">Autopartes · Lubricación</div>
        <h2 class="detail-title">Filtro de Aceite</h2>
        <div class="detail-price">$250 MXN</div>

        <div class="detail-info"><strong>Marca:</strong> Bosch</div>
        <div class="detail-info"><strong>Disponibilidad:</strong> 12 piezas en stock</div>

        <div class="detail-divider"></div>

        <form method="POST" action="#">
            @csrf
            <label class="form-label">Cantidad</label>
            <input type="number" min="1" value="1" class="qty-input">

            <div class="detail-footer">
                <button type="submit" class="btn-primary">
                    Agregar al pedido
                </button>
                <a href="/catalogo" class="btn-ghost">
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

@endsection