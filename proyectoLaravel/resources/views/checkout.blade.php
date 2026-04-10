@extends('layouts.app')

@section('contenido')

<div class="auth-wrap" style="align-items:flex-start; padding-top:3rem;">
    <div style="width:100%; max-width:900px;">

        <a href="/carrito" style="color:var(--text-muted); font-size:0.85rem; text-decoration:underline; margin-bottom:1.5rem; display:inline-block;">← Volver al carrito</a>

        <div class="section-header" style="border:none; padding:0; margin-bottom:2rem;">
            <h2>Pasarela de <span>Pago</span></h2>
        </div>

        @if(session('error'))
            <div style="background:rgba(255,60,60,.15); border:1px solid rgba(255,60,60,.4); color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        <div style="display:grid; grid-template-columns:1fr 380px; gap:2rem; align-items:start;">

            {{-- Formulario de pago --}}
            <div class="checkout-card">
                <div class="checkout-card-header">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <span>Datos de la tarjeta</span>
                </div>

                <form method="POST" action="/checkout" id="checkoutForm">
                    @csrf

                    {{-- Tipo de tarjeta --}}
                    <div class="form-group">
                        <label class="form-label">Tipo de tarjeta</label>
                        <div style="display:flex; gap:0.75rem;">
                            <label class="card-type-option">
                                <input type="radio" name="tipo_tarjeta" value="credito" checked>
                                <span class="card-type-label">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    Crédito
                                </span>
                            </label>
                            <label class="card-type-option">
                                <input type="radio" name="tipo_tarjeta" value="debito">
                                <span class="card-type-label">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                    Débito
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Número de tarjeta --}}
                    <div class="form-group">
                        <label class="form-label">Número de tarjeta</label>
                        <div style="position:relative;">
                            <input type="text" name="numero_tarjeta" id="cardNumber" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19" required autocomplete="off" inputmode="numeric" style="padding-left:3rem;">
                            <div id="cardBrandIcon" style="position:absolute; left:0.8rem; top:50%; transform:translateY(-50%); width:28px; height:20px; display:flex; align-items:center; justify-content:center; color:var(--text-muted);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            </div>
                        </div>
                        <div id="cardError" style="color:#ff6b6b; font-size:0.78rem; margin-top:0.3rem; display:none;"></div>
                    </div>

                    {{-- Nombre del titular --}}
                    <div class="form-group">
                        <label class="form-label">Nombre del titular</label>
                        <input type="text" name="nombre_titular" class="form-input" placeholder="JOAQUÍN PÉREZ" required autocomplete="off" style="text-transform:uppercase;">
                    </div>

                    {{-- Vencimiento y CVV --}}
                    <div style="display:flex; gap:1rem;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Vencimiento</label>
                            <input type="text" name="vencimiento" id="cardExpiry" class="form-input" placeholder="MM/AA" maxlength="5" required autocomplete="off" inputmode="numeric">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">CVV</label>
                            <div style="position:relative;">
                                <input type="password" name="cvv" id="cardCvv" class="form-input" placeholder="•••" maxlength="4" required autocomplete="off" inputmode="numeric">
                                <button type="button" id="toggleCvv" style="position:absolute; right:0.6rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer; display:flex; align-items:center;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-secure">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--yellow)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Pago seguro — Tus datos están protegidos
                    </div>

                    <button type="submit" class="btn-primary" id="payBtn" style="width:100%; justify-content:center; padding:0.95rem; margin-top:1rem; font-size:1rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Confirmar pago — ${{ number_format($total, 2) }} MXN
                    </button>
                </form>
            </div>

            {{-- Resumen del pedido --}}
            <div class="checkout-summary">
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; font-weight:600;">Resumen del pedido</div>

                @foreach($carrito as $item)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; {{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <div style="flex:1;">
                        <div style="font-weight:600; font-size:0.9rem; color:var(--text);">{{ $item['nombre'] }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">× {{ $item['cantidad'] }}</div>
                    </div>
                    <div style="font-weight:600; color:var(--text); font-size:0.9rem;">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</div>
                </div>
                @endforeach

                <div style="border-top:1px solid var(--border); margin-top:1rem; padding-top:1rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; color:var(--text-muted); font-size:0.9rem;">
                        <span>Subtotal</span>
                        <span style="color:var(--text);">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; color:var(--text-muted); font-size:0.9rem;">
                        <span>IVA (16%)</span>
                        <span style="color:var(--text);">${{ number_format($iva, 2) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1.1rem; border-top:1px solid var(--border); padding-top:0.8rem; margin-top:0.5rem;">
                        <span style="color:var(--text);">Total</span>
                        <span style="color:var(--orange); font-family:'Rajdhani',sans-serif;">${{ number_format($total, 2) }} MXN</span>
                    </div>
                </div>

                {{-- Dirección de envío --}}
                <div style="margin-top:1.5rem; background:rgba(60,255,60,.06); border:1px solid rgba(60,255,60,.15); border-radius:10px; padding:0.9rem;">
                    <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.4rem;">Enviar a</div>
                    <div style="font-size:0.85rem; color:var(--text); line-height:1.5;">
                        {{ $dirData['direccion']['calle'] }} #{{ $dirData['direccion']['numero'] }}<br>
                        CP {{ $dirData['direccion']['codigo_postal'] }}<br>
                        {{ $dirData['direccion']['municipio'] }}, {{ $dirData['direccion']['estado'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formateo del número de tarjeta
    const cardInput = document.getElementById('cardNumber');
    cardInput.addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g, '');
        let formatted = v.match(/.{1,4}/g)?.join(' ') || v;
        this.value = formatted;
        
        // Detect card brand — use text labels instead of emojis
        var icon = document.getElementById('cardBrandIcon');
        if (v.startsWith('4')) icon.innerHTML = '<span style="font-size:0.65rem; font-weight:700; color:#1a6eff; letter-spacing:0.02em;">VISA</span>';
        else if (v.startsWith('5') || v.startsWith('2')) icon.innerHTML = '<span style="font-size:0.6rem; font-weight:700; color:var(--orange); letter-spacing:0.02em;">MC</span>';
        else if (v.startsWith('3')) icon.innerHTML = '<span style="font-size:0.6rem; font-weight:700; color:#007bff; letter-spacing:0.02em;">AMEX</span>';
        else icon.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>';
    });

    // Formateo de fecha de vencimiento
    const expiryInput = document.getElementById('cardExpiry');
    expiryInput.addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g, '');
        if (v.length >= 2) {
            v = v.substring(0, 2) + '/' + v.substring(2, 4);
        }
        this.value = v;
    });

    // Toggle CVV visibility
    const toggleCvv = document.getElementById('toggleCvv');
    const cvvInput = document.getElementById('cardCvv');
    toggleCvv.addEventListener('click', function() {
        cvvInput.type = cvvInput.type === 'password' ? 'text' : 'password';
    });

    // Validación al enviar
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const num = cardInput.value.replace(/\s/g, '');
        const errDiv = document.getElementById('cardError');

        if (num.length < 13 || num.length > 19) {
            e.preventDefault();
            errDiv.textContent = 'El número de tarjeta debe tener entre 13 y 19 dígitos.';
            errDiv.style.display = 'block';
            return;
        }

        // Validar Luhn
        let sum = 0, alt = false;
        for (let i = num.length - 1; i >= 0; i--) {
            let n = parseInt(num[i], 10);
            if (alt) { n *= 2; if (n > 9) n -= 9; }
            sum += n;
            alt = !alt;
        }
        if (sum % 10 !== 0) {
            e.preventDefault();
            errDiv.textContent = 'El número de tarjeta no es válido.';
            errDiv.style.display = 'block';
            return;
        }

        // Validar vencimiento
        const exp = expiryInput.value.split('/');
        if (exp.length === 2) {
            const month = parseInt(exp[0], 10);
            const year = parseInt('20' + exp[1], 10);
            const now = new Date();
            const expDate = new Date(year, month, 0);
            if (expDate < now) {
                e.preventDefault();
                errDiv.textContent = 'La tarjeta está vencida.';
                errDiv.style.display = 'block';
                return;
            }
        }

        errDiv.style.display = 'none';

        // Deshabilitar botón para evitar doble clic
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="pay-spinner"></span> Procesando pago...';
    });
});
</script>

@endsection
