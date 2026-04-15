@extends('layouts.app')

@section('contenido')

<div class="section-header">
    <h2>Catálogo de <span>Autopartes</span></h2>
    <span class="section-count">{{ count($productos) }} productos</span>
</div>

{{-- ═══ Barra de Búsqueda + Filtros ═══ --}}
<style>
    .cat-filters-bar { margin-bottom:1.5rem; padding:1rem 1.25rem; background:var(--dark); border:1px solid var(--border); border-radius:var(--radius-md); }
    .cat-search-wrap { flex:1; display:flex; align-items:center; gap:0.5rem; background:rgba(255,255,255,0.04); border:1px solid var(--border); border-radius:10px; padding:0.55rem 0.85rem; transition:border-color var(--transition); }
    .cat-search-wrap:focus-within { border-color:var(--red); box-shadow:0 0 0 3px var(--red-glow); }
    .cat-search-wrap input { background:none; border:none; outline:none; color:var(--text); font-family:'Rajdhani',sans-serif; font-size:0.95rem; width:100%; }
    .cat-search-wrap input::placeholder { color:var(--text-muted); opacity:.6; }

    /* ── Dropdowns & inputs ────────── */
    .cat-select,
    .cat-input {
        -webkit-appearance:none; -moz-appearance:none; appearance:none;
        background:var(--dark-2); border:1px solid var(--border); color:var(--text);
        padding:0.45rem 0.75rem; border-radius:8px;
        font-family:'Rajdhani',sans-serif; font-size:0.85rem; font-weight:500;
        cursor:pointer; min-width:140px;
        transition:border-color var(--transition), box-shadow var(--transition);
        outline:none;
    }
    .cat-select { padding-right:2rem; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 0.6rem center; }
    .cat-select:focus, .cat-input:focus { border-color:var(--red); box-shadow:0 0 0 3px var(--red-glow); }
    .cat-select:hover, .cat-input:hover { border-color:rgba(255,255,255,0.18); }
    .cat-select option { background:var(--dark); color:var(--text); padding:0.5rem; }
    .cat-select option:checked { background:rgba(233,48,42,0.25); }
    .cat-input { cursor:text; min-width:unset; width:80px; }
    .cat-input::placeholder { color:var(--text-muted); opacity:.5; }
    /* Hide number spinners */
    .cat-input[type="number"]::-webkit-inner-spin-button,
    .cat-input[type="number"]::-webkit-outer-spin-button { -webkit-appearance:none; margin:0; }
    .cat-input[type="number"] { -moz-appearance:textfield; }

    .cat-label { font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; white-space:nowrap; }
    .cat-divider { width:1px; height:22px; background:var(--border); }
    .cat-clear { font-size:0.8rem; color:var(--red); text-decoration:none; margin-left:auto; display:flex; align-items:center; gap:4px; white-space:nowrap; transition:opacity var(--transition); }
    .cat-clear:hover { opacity:.75; }
    .cat-btn-x { background:none; border:none; cursor:pointer; color:var(--text-muted); padding:0; line-height:1; transition:color var(--transition); }
    .cat-btn-x:hover { color:var(--red); }
</style>

<div class="cat-filters-bar">

    {{-- Buscador --}}
    <div style="display:flex; gap:0.6rem; align-items:center; margin-bottom:0.75rem;">
        <div class="cat-search-wrap">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="catSearchInput" placeholder="Buscar por nombre o código..." value="{{ $busqueda }}" oninput="debounceBusqueda()">
            @if($busqueda)
            <button onclick="document.getElementById('catSearchInput').value='';aplicarFiltros();" class="cat-btn-x" title="Limpiar búsqueda">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            @endif
        </div>
        <span style="font-size:0.75rem; color:var(--text-muted); white-space:nowrap;">
            {{ count($productos) }} resultado{{ count($productos) != 1 ? 's' : '' }}
        </span>
    </div>

    {{-- Filtros --}}
    <div style="display:flex; gap:0.6rem; flex-wrap:wrap; align-items:center;">
        <span class="cat-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:3px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Filtros:
        </span>

        {{-- Tipo de autoparte --}}
        <select id="catFiltroTipo" onchange="aplicarFiltros()" title="Tipo de autoparte" class="cat-select">
            <option value="">Todos los tipos</option>
            @foreach($tipos as $t)
            <option value="{{ $t['id'] }}" {{ $filtroTipo == $t['id'] ? 'selected' : '' }}>{{ $t['nombre'] }}</option>
            @endforeach
        </select>

        {{-- Marca --}}
        <select id="catFiltroMarca" onchange="aplicarFiltros()" title="Marca" class="cat-select">
            <option value="">Todas las marcas</option>
            @foreach($marcas as $m)
            <option value="{{ $m['id'] }}" {{ $filtroMarca == $m['id'] ? 'selected' : '' }}>{{ $m['nombre'] }}</option>
            @endforeach
        </select>

        {{-- Rango de precio --}}
        <div style="display:flex; align-items:center; gap:0.35rem;">
            <span style="font-size:0.8rem; color:var(--text-muted);">$</span>
            <input type="number" id="catPrecioMin" placeholder="Mín" min="0" step="0.01" value="{{ $precioMin }}" title="Precio mínimo" class="cat-input" oninput="debouncePrecios()">
            <span style="font-size:0.8rem; color:var(--text-muted);">—</span>
            <input type="number" id="catPrecioMax" placeholder="Máx" min="0" step="0.01" value="{{ $precioMax }}" title="Precio máximo" class="cat-input" oninput="debouncePrecios()">
        </div>

        <div class="cat-divider"></div>

        <span class="cat-label">Orden:</span>

        {{-- Orden --}}
        <select id="catOrden" onchange="aplicarFiltros()" title="Orden" class="cat-select" style="min-width:170px;">
            <option value="" {{ !$orden ? 'selected' : '' }}>Nombre A→Z</option>
            <option value="nombre_desc" {{ $orden == 'nombre_desc' ? 'selected' : '' }}>Nombre Z→A</option>
            <option value="precio_asc" {{ $orden == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
            <option value="precio_desc" {{ $orden == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
        </select>

        @if($busqueda || $filtroTipo || $filtroMarca || $precioMin || $precioMax || $orden)
        <a href="/catalogo" class="cat-clear">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Limpiar filtros
        </a>
        @endif
    </div>
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
    <div style="background:var(--dark); border:1px solid var(--border); border-radius:20px; max-width:780px; width:100%; max-height:90vh; overflow:hidden; position:relative; box-shadow:0 24px 80px rgba(0,0,0,0.5);">
        <button id="closeModal" onclick="closeProductModal()" style="position:absolute; top:1rem; right:1rem; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); color:#9E9298; font-size:1.4rem; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:10; line-height:1;">&times;</button>

        <div style="display:grid; grid-template-columns:1fr 1fr; height:100%; max-height:90vh;">
            <div style="background:var(--dark-2); display:flex; align-items:center; justify-content:center; border-radius:20px 0 0 20px; position:sticky; top:0; align-self:start; height:100%;">
                <img src="{{ asset('images/autoparte_default.jpg') }}" id="modalImg" alt="Producto" style="width:100%; aspect-ratio:1/1; object-fit:contain; display:block; border-radius:20px 0 0 20px; padding:1rem; background:var(--dark-2);">
            </div>
            <div style="padding:2rem; overflow-y:auto; max-height:90vh;">
                <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.12em; margin-bottom:0.5rem;"><span id="modalSku">SKU: ---</span> <span id="modalMarca" style="margin-left:0.5rem; color:var(--orange); font-weight:600;"></span></div>
                <h3 style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.6rem; color:var(--text); line-height:1.15; margin-bottom:0.4rem;" id="modalName">Nombre</h3>
                <div style="font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.5rem; color:var(--orange);" id="modalPrice">$0 MXN</div>

                <div style="margin:1rem 0;">
                    <span id="modalBadge" class="badge badge-avail">Disponible</span>
                    <span id="modalStock" style="margin-left:0.5rem; font-size:0.8rem; color:var(--text-muted);"></span>
                </div>

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
                        <input type="hidden" name="redirect_checkout" id="modalRedirectCheckout" value="">
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:0.85rem;" onclick="document.getElementById('modalRedirectCheckout').value='';">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> AGREGAR AL CARRITO
                            </button>
                            <button type="submit" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.85rem; background:var(--yellow); border:none; color:#1a1a1a; font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.95rem; border-radius:var(--radius-sm); cursor:pointer; transition:var(--transition); letter-spacing:0.1em; text-transform:uppercase; text-decoration:none;" onclick="document.getElementById('modalRedirectCheckout').value='1';" onmouseover="this.style.filter='brightness(1.1)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.filter=''; this.style.transform='';">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg> COMPRAR AHORA
                            </button>
                        </div>
                    </form>
                </div>

                <div id="modalNoStock" style="display:none;">
                    <button class="btn-disabled" disabled>No disponible</button>
                </div>

                <div style="margin-top:1.5rem;">
                    <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.15em; font-weight:700; margin-bottom:0.75rem;">DETALLE DEL PRODUCTO</div>
                    <div id="modalDescription" style="font-size:0.9rem; color:var(--text-muted); line-height:1.6;"></div>
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
    document.getElementById('modalMarca').textContent = product.marca_nombre ? '· ' + product.marca_nombre : '';
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

// ── Catálogo: búsqueda y filtros ──
function aplicarFiltros() {
    var params = new URLSearchParams();
    var busq  = document.getElementById('catSearchInput').value.trim();
    var tipo  = document.getElementById('catFiltroTipo').value;
    var marca = document.getElementById('catFiltroMarca').value;
    var pMin  = document.getElementById('catPrecioMin').value;
    var pMax  = document.getElementById('catPrecioMax').value;
    var orden = document.getElementById('catOrden').value;
    if (busq)  params.set('busqueda', busq);
    if (tipo)  params.set('tipo', tipo);
    if (marca) params.set('marca', marca);
    if (pMin)  params.set('precio_min', pMin);
    if (pMax)  params.set('precio_max', pMax);
    if (orden) params.set('orden', orden);
    window.location.href = '/catalogo' + (params.toString() ? '?' + params.toString() : '');
}

var _busqTimer = null;
function debounceBusqueda() {
    clearTimeout(_busqTimer);
    _busqTimer = setTimeout(aplicarFiltros, 500);
}

var _precioTimer = null;
function debouncePrecios() {
    clearTimeout(_precioTimer);
    _precioTimer = setTimeout(aplicarFiltros, 700);
}
</script>

@endsection