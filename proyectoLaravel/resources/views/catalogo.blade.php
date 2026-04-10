@extends('layouts.app')

@section('contenido')

<div class="section-header">
    <h2>Catálogo de <span>Autopartes</span></h2>
    <span class="section-count">{{ count($productos) }} productos</span>
</div>

@if(session('carrito_msg'))
    <div id="toast-carrito" style="position:fixed; top:1.2rem; right:1.2rem; z-index:9999; background:rgba(30,30,30,0.95); border:1px solid rgba(60,255,60,.35); color:#6bff6b; padding:0.85rem 1.3rem; border-radius:12px; font-size:0.9rem; display:flex; align-items:center; gap:0.6rem; box-shadow:0 8px 32px rgba(0,0,0,0.5); backdrop-filter:blur(12px); animation:toastIn 0.35s ease-out;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> {{ session('carrito_msg') }}
    </div>
    <style>
        @keyframes toastIn { from { opacity:0; transform:translateY(-16px) scale(0.96); } to { opacity:1; transform:translateY(0) scale(1); } }
        @keyframes toastOut { from { opacity:1; transform:translateY(0) scale(1); } to { opacity:0; transform:translateY(-16px) scale(0.96); } }
    </style>
    <script>
        setTimeout(function() {
            var t = document.getElementById('toast-carrito');
            if (t) { t.style.animation = 'toastOut 0.35s ease-in forwards'; setTimeout(function() { t.remove(); }, 350); }
        }, 5000);
    </script>
@endif

@if(session('carrito_error'))
    <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1.2rem; border-radius:10px; margin-bottom:1.5rem; font-size:0.9rem; display:flex; align-items:center; gap:0.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> {{ session('carrito_error') }}
    </div>
@endif

<div class="product-grid">

    @forelse($productos as $producto)
    <div class="mcq-card" data-product='@json($producto)'>
        <div style="position:relative !important;">
            <img src="{{ $producto['imagen_url'] ?? asset('images/autoparte_default.jpg') }}" class="card-img" alt="{{ $producto['nombre'] }}" onerror="this.src='{{ asset('images/autoparte_default.jpg') }}'">
            @if($producto['estatus_producto'] === 'en_stock' && $producto['cantidad'] > 0 && session('token'))
            <form method="POST" action="/carrito/agregar" style="position:absolute !important; top:8px !important; right:8px !important; left:auto !important; bottom:auto !important; z-index:10;" onclick="event.stopPropagation();">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $producto['id'] }}">
                <input type="hidden" name="nombre" value="{{ $producto['nombre'] }}">
                <input type="hidden" name="codigo" value="{{ $producto['codigo'] }}">
                <input type="hidden" name="precio" value="{{ $producto['precio'] }}">
                <input type="hidden" name="cantidad" value="1">
                <input type="hidden" name="stock" value="{{ $producto['cantidad'] }}">
                <input type="hidden" name="imagen_url" value="{{ $producto['imagen_url'] ?? '' }}">
                <button type="submit" style="width:36px; height:36px; border-radius:50%; background:rgba(233,48,42,0.9); border:none; color:#fff; cursor:pointer; display:flex !important; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(0,0,0,0.3);" title="Agregar al carrito">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </button>
            </form>
            @endif
        </div>
        <div class="card-body">
            <div class="card-name">{{ $producto['nombre'] }}</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.3rem;">SKU: {{ $producto['codigo'] }}</div>
            <div class="card-price">${{ number_format($producto['precio'], 0) }} MXN</div>
            <div class="card-footer">
                @if($producto['estatus_producto'] === 'en_stock' && $producto['cantidad'] > 0)
                    <span class="badge badge-avail">Disponible ({{ $producto['cantidad'] }})</span>
                @elseif($producto['estatus_producto'] === 'descontinuado')
                    <span class="badge badge-out">Descontinuado</span>
                @else
                    <span class="badge badge-out">Agotado</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">
        <p>No hay productos disponibles en este momento.</p>
    </div>
    @endforelse

</div>

{{-- Modal de detalle de producto --}}
<div id="productModal" style="display:none; position:fixed !important; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); z-index:500; align-items:center; justify-content:center; padding:2rem;">
    <div style="background:var(--dark); border:1px solid var(--border); border-radius:20px; max-width:780px; width:100%; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 24px 80px rgba(0,0,0,0.5);">
        <button id="closeModal" onclick="closeProductModal()" style="position:absolute; top:1rem; right:1rem; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); color:#9E9298; font-size:1.4rem; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:10; line-height:1;">&times;</button>

        <div style="display:grid; grid-template-columns:1fr 1fr;">
            <div style="background:var(--dark-2);">
                <img src="{{ asset('images/autoparte_default.jpg') }}" id="modalImg" alt="Producto" style="width:100%; height:100%; min-height:300px; object-fit:cover; display:block; border-radius:20px 0 0 20px;">
            </div>
            <div style="padding:2rem;">
                <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.12em; margin-bottom:0.5rem;" id="modalSku">SKU: ---</div>
                <h3 style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.6rem; color:var(--text); line-height:1.15; margin-bottom:0.4rem;" id="modalName">Nombre</h3>
                <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.5rem; color:var(--orange);" id="modalPrice">$0 MXN</div>

                <div style="margin:1rem 0;">
                    <span id="modalBadge" class="badge badge-avail">Disponible</span>
                    <span id="modalStock" style="margin-left:0.5rem; font-size:0.8rem; color:var(--text-muted);"></span>
                </div>

                <div id="modalDescription" style="font-size:0.9rem; color:var(--text-muted); line-height:1.6; margin-bottom:1.5rem;"></div>

                <div id="modalCartSection" style="display:none;">
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Cantidad</div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                        <input type="number" id="modalQty" class="qty-input" value="1" min="1" max="1" style="text-align:center; width:70px;">
                        <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                    </div>
                    <div id="modalStockError" style="color:#ff6b6b; font-size:0.8rem; margin-bottom:0.8rem; display:none;"></div>

                    <form method="POST" action="/carrito/agregar" id="modalCartForm">
                        @csrf
                        <input type="hidden" name="producto_id" id="modalProductId">
                        <input type="hidden" name="nombre" id="modalProductName">
                        <input type="hidden" name="codigo" id="modalProductCode">
                        <input type="hidden" name="precio" id="modalProductPrice">
                        <input type="hidden" name="cantidad" id="modalProductQty" value="1">
                        <input type="hidden" name="stock" id="modalProductStock">
                        <input type="hidden" name="imagen_url" id="modalProductImg">
                        <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> Agregar al carrito
                        </button>
                    </form>
                </div>

                <div id="modalNoStock" style="display:none;">
                    <button class="btn-disabled" disabled>No disponible</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var isLoggedIn = {{ session('token') ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    // Attach click listeners to all product cards
    document.querySelectorAll('.mcq-card[data-product]').forEach(function(card) {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e) {
            // Don't open modal if clicking on the add-to-cart form
            if (e.target.closest('form')) return;
            try {
                var product = JSON.parse(this.getAttribute('data-product'));
                openProductModal(product);
            } catch(err) {
                console.error('Error opening modal:', err);
            }
        });
    });

    // Qty manual input
    var qtyInput = document.getElementById('modalQty');
    if (qtyInput) {
        qtyInput.addEventListener('change', function() {
            var max = parseInt(this.max);
            var val = parseInt(this.value);
            var errDiv = document.getElementById('modalStockError');
            if (isNaN(val) || val < 1) val = 1;
            if (val > max) {
                val = max;
                errDiv.textContent = 'No hay suficiente stock. Máximo disponible: ' + max;
                errDiv.style.display = 'block';
            } else {
                errDiv.style.display = 'none';
            }
            this.value = val;
            document.getElementById('modalProductQty').value = val;
        });
    }

    // Close modal on overlay click
    var overlay = document.getElementById('productModal');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeProductModal();
        });
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeProductModal();
    });
});

function openProductModal(product) {
    var modal = document.getElementById('productModal');
    var modalImg = document.getElementById('modalImg');
    document.getElementById('modalName').textContent = product.nombre;
    document.getElementById('modalSku').textContent = 'SKU: ' + product.codigo;
    document.getElementById('modalPrice').textContent = '$' + Number(product.precio).toLocaleString('es-MX') + ' MXN';
    document.getElementById('modalDescription').textContent = product.descripcion || 'Autoparte de alta calidad para tu vehículo.';

    // Set product image or fallback
    if (product.imagen_url) {
        modalImg.src = product.imagen_url;
        modalImg.onerror = function() { this.src = '{{ asset("images/autoparte_default.jpg") }}'; };
    } else {
        modalImg.src = '{{ asset("images/autoparte_default.jpg") }}';
    }

    var badge = document.getElementById('modalBadge');
    var stockText = document.getElementById('modalStock');
    var cartSection = document.getElementById('modalCartSection');
    var noStock = document.getElementById('modalNoStock');
    var qtyInput = document.getElementById('modalQty');

    if (product.estatus_producto === 'en_stock' && product.cantidad > 0) {
        badge.className = 'badge badge-avail';
        badge.textContent = 'Disponible';
        stockText.textContent = product.cantidad + ' en stock';
        qtyInput.max = product.cantidad;
        qtyInput.value = 1;

        if (isLoggedIn) {
            cartSection.style.display = 'block';
            noStock.style.display = 'none';
            document.getElementById('modalProductId').value = product.id;
            document.getElementById('modalProductName').value = product.nombre;
            document.getElementById('modalProductCode').value = product.codigo;
            document.getElementById('modalProductPrice').value = product.precio;
            document.getElementById('modalProductStock').value = product.cantidad;
            document.getElementById('modalProductQty').value = 1;
            document.getElementById('modalProductImg').value = product.imagen_url || '';
        } else {
            cartSection.style.display = 'none';
            noStock.style.display = 'block';
            noStock.innerHTML = '<a href="/login" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem; text-decoration:none; display:flex;">Inicia sesión para comprar</a>';
        }
    } else if (product.estatus_producto === 'descontinuado') {
        badge.className = 'badge badge-out';
        badge.textContent = 'Descontinuado';
        stockText.textContent = '';
        cartSection.style.display = 'none';
        noStock.style.display = 'block';
        noStock.innerHTML = '<button class="btn-disabled" disabled>No disponible</button>';
    } else {
        badge.className = 'badge badge-out';
        badge.textContent = 'Agotado';
        stockText.textContent = '0 en stock';
        cartSection.style.display = 'none';
        noStock.style.display = 'block';
        noStock.innerHTML = '<button class="btn-disabled" disabled>No disponible</button>';
    }

    document.getElementById('modalStockError').style.display = 'none';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
    document.body.style.overflow = '';
}

function changeQty(delta) {
    var input = document.getElementById('modalQty');
    var val = parseInt(input.value) + delta;
    var max = parseInt(input.max);
    var errDiv = document.getElementById('modalStockError');

    if (val < 1) val = 1;
    if (val > max) {
        val = max;
        errDiv.textContent = 'No hay suficiente stock. Máximo disponible: ' + max;
        errDiv.style.display = 'block';
    } else {
        errDiv.style.display = 'none';
    }

    input.value = val;
    document.getElementById('modalProductQty').value = val;
}
</script>

@endsection