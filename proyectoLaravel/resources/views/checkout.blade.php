@extends('layouts.app')

@section('contenido')

<style>
    .card-type-option input[type="radio"] { display: none; }
    .card-type-label {
        display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
        padding: 0.7rem 1.2rem 0.55rem; border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.08); background: #3A2E2E;
        color: #9E9298; font-family: 'Rajdhani', sans-serif; font-weight: 600;
        font-size: 0.88rem; cursor: pointer; transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
        position: relative; overflow: hidden;
    }
    .card-type-label::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, #E9302A, #EC7B4B, #EEF50F);
        transform: scaleX(0); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .card-type-label:hover {
        border-color: rgba(233,48,42,0.3); background: rgba(255,255,255,0.03);
    }
    .card-type-text {
        font-size: 0.78rem; letter-spacing: 0.06em; text-transform: uppercase;
    }
    .card-type-option input[type="radio"]:checked + .card-type-label {
        border-color: #E9302A; background: rgba(233,48,42,0.08); color: #F0EDED;
    }
    .card-type-option input[type="radio"]:checked + .card-type-label::after {
        transform: scaleX(1);
    }
</style>

<div class="auth-wrap" style="min-height:calc(100vh - 64px); display:flex; align-items:flex-start; justify-content:center; padding:3rem 2rem; margin:-2.5rem -2.5rem 0; background:radial-gradient(ellipse 50% 60% at 50% 100%, rgba(233,48,42,0.16) 0%, transparent 70%), var(--surface);">
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
            <div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; padding:2rem; box-shadow:0 4px 24px rgba(0,0,0,0.35);">
                <div style="display:flex; align-items:center; gap:0.6rem; font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1.15rem; color:var(--text); margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--border);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <span>Datos de la tarjeta</span>
                </div>

                <form method="POST" action="/checkout" id="checkoutForm">
                    @csrf

                    {{-- Tipo de tarjeta --}}
                    <div class="form-group">
                        <label class="form-label">Tipo de tarjeta</label>
                        <div style="display:flex; gap:1rem;">
                            <label class="card-type-option">
                                <input type="radio" name="tipo_tarjeta" value="credito" checked>
                                <span class="card-type-label">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    <span class="card-type-text">Crédito</span>
                                </span>
                            </label>
                            <label class="card-type-option">
                                <input type="radio" name="tipo_tarjeta" value="debito">
                                <span class="card-type-label">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                    <span class="card-type-text">Débito</span>
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
                        <input type="text" name="nombre_titular" id="cardName" class="form-input" placeholder="JOAQUÍN PÉREZ" required autocomplete="off" style="text-transform:uppercase;" oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g,'')">
                        <div id="nameError" style="color:#ff6b6b; font-size:0.78rem; margin-top:0.3rem; display:none;"></div>
                    </div>

                    {{-- Vencimiento y CVV --}}
                    <div style="display:flex; gap:1rem;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Vencimiento</label>
                            <input type="text" name="vencimiento" id="cardExpiry" class="form-input" placeholder="MM/AA" maxlength="5" required autocomplete="off" inputmode="numeric">
                            <div id="expiryError" style="color:#ff6b6b; font-size:0.78rem; margin-top:0.3rem; display:none;"></div>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">CVV</label>
                            <div style="position:relative;">
                                <input type="password" name="cvv" id="cardCvv" class="form-input" placeholder="•••" maxlength="3" required autocomplete="off" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'')">
                                <button type="button" id="toggleCvv" style="position:absolute; right:0.6rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer; display:flex; align-items:center;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <div id="cvvError" style="color:#ff6b6b; font-size:0.78rem; margin-top:0.3rem; display:none;"></div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.78rem; color:var(--yellow); margin-top:1.25rem; padding:0.6rem 0.8rem; background:rgba(238,245,15,0.12); border:1px solid rgba(238,245,15,0.15); border-radius:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--yellow)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Pago seguro — Tus datos están protegidos
                    </div>

                    <button type="submit" class="btn-primary" id="payBtn" style="width:100%; justify-content:center; padding:0.95rem; margin-top:1rem; font-size:1rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Confirmar pago — ${{ number_format($total, 2) }} MXN
                    </button>
                </form>
            </div>

            {{-- Resumen del pedido --}}
            <div style="background:var(--dark); border:1px solid var(--border); border-radius:14px; padding:2rem; position:sticky; top:84px;">
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
                        {{ $dirData['direccion']['calle'] }}
                        @if($dirData['direccion']['numero'])
                            #{{ $dirData['direccion']['numero'] }}
                        @else
                            S/N
                        @endif
                        <br>
                        @if(!empty($dirData['direccion']['colonia']))
                            Col. {{ $dirData['direccion']['colonia'] }}<br>
                        @endif
                        @if(!empty($dirData['direccion']['localidad']))
                            {{ $dirData['direccion']['localidad'] }}<br>
                        @endif
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
        const nameErr = document.getElementById('nameError');
        const expiryErr = document.getElementById('expiryError');
        const cvvErr = document.getElementById('cvvError');
        const nameInput = document.getElementById('cardName');

        // Reset errors
        [errDiv, nameErr, expiryErr, cvvErr].forEach(d => d.style.display = 'none');
        let hasError = false;

        // Validar número de tarjeta
        if (num.length < 13 || num.length > 19) {
            errDiv.textContent = 'El número de tarjeta debe tener entre 13 y 19 dígitos.';
            errDiv.style.display = 'block';
            hasError = true;
        }

        // Validar nombre (solo letras, espacios, acentos)
        const nameVal = nameInput.value.trim();
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(nameVal)) {
            nameErr.textContent = 'El nombre solo puede contener letras y espacios.';
            nameErr.style.display = 'block';
            hasError = true;
        } else if (nameVal.length < 3) {
            nameErr.textContent = 'El nombre debe tener al menos 3 caracteres.';
            nameErr.style.display = 'block';
            hasError = true;
        }

        // Validar vencimiento (>= mes/año actual)
        const exp = expiryInput.value.split('/');
        if (exp.length !== 2 || exp[0].length !== 2 || exp[1].length !== 2) {
            expiryErr.textContent = 'Formato inválido. Usa MM/AA.';
            expiryErr.style.display = 'block';
            hasError = true;
        } else {
            const month = parseInt(exp[0], 10);
            const year = parseInt('20' + exp[1], 10);
            if (month < 1 || month > 12) {
                expiryErr.textContent = 'El mes debe estar entre 01 y 12.';
                expiryErr.style.display = 'block';
                hasError = true;
            } else {
                const now = new Date();
                const curMonth = now.getMonth() + 1;
                const curYear = now.getFullYear();
                if (year < curYear || (year === curYear && month < curMonth)) {
                    expiryErr.textContent = 'La tarjeta está vencida.';
                    expiryErr.style.display = 'block';
                    hasError = true;
                }
            }
        }

        // Validar CVV (exactamente 3 dígitos)
        const cvvVal = cvvInput.value.replace(/\D/g, '');
        if (cvvVal.length !== 3) {
            cvvErr.textContent = 'El CVV debe ser de 3 dígitos.';
            cvvErr.style.display = 'block';
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            return;
        }

        // Deshabilitar botón para evitar doble clic
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="pay-spinner"></span> Procesando pago...';
    });
});
</script>

@endsection
