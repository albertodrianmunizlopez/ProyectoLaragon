<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

/**
 * Controlador principal de la tienda Macuin (portal cliente Laravel).
 */
class TiendaController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    // ── Inicio ──────────────────────────────────────────

    public function inicio()
    {
        // Obtener productos para el carrusel del inicio
        $productos = $this->api->getProductos();
        // Filtrar solo los que tienen stock
        $productosCarrusel = array_filter($productos, function ($p) {
            return ($p['estatus_producto'] ?? '') === 'en_stock' && ($p['cantidad'] ?? 0) > 0;
        });
        // Tomar hasta 8 para el carrusel
        $productosCarrusel = array_slice(array_values($productosCarrusel), 0, 8);

        // Extraer marcas únicas
        $marcas = collect($productos)
            ->pluck('marca_nombre')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('inicio', [
            'productosCarrusel' => $productosCarrusel,
            'marcas' => $marcas,
        ]);
    }

    // ── Login ───────────────────────────────────────────

    public function loginForm()
    {
        return view('login');
    }

    public function loginPost(Request $request)
    {
        $result = $this->api->login(
            $request->input('email'),
            $request->input('password')
        );

        if ($result['status'] === 200 && isset($result['data']['access_token'])) {
            $request->session()->put('token', $result['data']['access_token']);
            $request->session()->put('usuario', $result['data']['usuario']);

            // Obtener y guardar dirección del usuario en sesión
            $dirData = $this->api->getMiDireccion($result['data']['access_token']);
            $request->session()->put('direccion', $dirData);

            return redirect('/catalogo');
        }

        return back()->with('error', 'Credenciales incorrectas. Verifica tu correo y contraseña.');
    }

    // ── Registro ────────────────────────────────────────

    public function registroForm()
    {
        return view('registro');
    }

    public function registroPost(Request $request)
    {
        $nombre = trim($request->input('nombre', ''));
        $apellidos = trim($request->input('apellidos', ''));

        // Validar solo letras
        if (!preg_match('/^[A-Za-záéíóúñÁÉÍÓÚÑüÜ\s]+$/', $nombre)) {
            return back()->with('error', 'El nombre solo puede contener letras.');
        }
        if (!preg_match('/^[A-Za-záéíóúñÁÉÍÓÚÑüÜ\s]+$/', $apellidos)) {
            return back()->with('error', 'Los apellidos solo pueden contener letras.');
        }

        $result = $this->api->register(
            $nombre,
            $apellidos,
            $request->input('email'),
            $request->input('password')
        );

        if ($result['status'] === 201) {
            return redirect('/login')->with('success', '¡Cuenta creada! Ahora inicia sesión.');
        }

        $msg = $result['data']['detail'] ?? 'No se pudo crear la cuenta. Intenta con otro correo.';
        return back()->with('error', $msg);
    }

    // ── Verificar Email Duplicado (AJAX) ──────────────────

    public function checkEmail(Request $request)
    {
        $email = $request->query('email', '');
        $result = $this->api->get('/api/auth/check-email?email=' . urlencode($email));
        return response()->json($result ?? ['exists' => false]);
    }

    // ── Logout ──────────────────────────────────────────

    public function logout(Request $request)
    {
        $request->session()->forget(['token', 'usuario', 'carrito', 'direccion']);
        return redirect('/');
    }

    // ── Catálogo ────────────────────────────────────────

    public function catalogo(Request $request)
    {
        $token = $request->session()->get('token');

        // Build API query params from request
        $params = [];
        if ($request->filled('busqueda'))   $params['busqueda']   = $request->input('busqueda');
        if ($request->filled('tipo'))       $params['tipo']       = $request->input('tipo');
        if ($request->filled('marca'))      $params['marca']      = $request->input('marca');
        if ($request->filled('precio_min')) $params['precio_min'] = $request->input('precio_min');
        if ($request->filled('precio_max')) $params['precio_max'] = $request->input('precio_max');
        if ($request->filled('orden'))      $params['orden']      = $request->input('orden');

        $productos = $this->api->getProductos($token, $params);
        $tipos     = $this->api->get('/api/productos/catalogos/tipos', $token) ?? [];
        $marcas    = $this->api->get('/api/productos/catalogos/marcas', $token) ?? [];

        return view('catalogo', [
            'productos'  => $productos,
            'tipos'      => $tipos,
            'marcas'     => $marcas,
            'busqueda'   => $request->input('busqueda', ''),
            'filtroTipo' => $request->input('tipo', ''),
            'filtroMarca'=> $request->input('marca', ''),
            'precioMin'  => $request->input('precio_min', ''),
            'precioMax'  => $request->input('precio_max', ''),
            'orden'      => $request->input('orden', ''),
        ]);
    }

    // ── Carrito ─────────────────────────────────────────

    public function carrito(Request $request)
    {
        $carrito = $request->session()->get('carrito', []);
        return view('carrito', ['carrito' => $carrito]);
    }

    public function agregarAlCarrito(Request $request)
    {
        $carrito = $request->session()->get('carrito', []);

        $productoId  = $request->input('producto_id');
        $nombre      = $request->input('nombre');
        $codigo      = $request->input('codigo');
        $precio      = floatval($request->input('precio'));
        $cantidad    = intval($request->input('cantidad', 1));
        $stock       = intval($request->input('stock', 999));
        $imagen_url  = $request->input('imagen_url', '');

        // Verificar stock
        $cantidadEnCarrito = 0;
        foreach ($carrito as $item) {
            if ($item['producto_id'] == $productoId) {
                $cantidadEnCarrito = $item['cantidad'];
                break;
            }
        }

        if (($cantidadEnCarrito + $cantidad) > $stock) {
            return back()->with('carrito_error', "No hay suficiente stock de \"{$nombre}\". Disponible: {$stock}, En carrito: {$cantidadEnCarrito}.");
        }

        // Si ya existe en el carrito, incrementar cantidad
        $encontrado = false;
        foreach ($carrito as &$item) {
            if ($item['producto_id'] == $productoId) {
                $item['cantidad'] += $cantidad;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $carrito[] = [
                'producto_id' => $productoId,
                'nombre'      => $nombre,
                'codigo'      => $codigo,
                'precio'      => $precio,
                'cantidad'    => $cantidad,
                'imagen_url'  => $imagen_url,
            ];
        }

        $request->session()->put('carrito', $carrito);
        if ($request->input('redirect_checkout')) {
            return redirect('/checkout');
        }
        return back()->with('carrito_msg', "\"{$nombre}\" agregado al carrito");
    }

    public function eliminarDelCarrito(Request $request)
    {
        $carrito = $request->session()->get('carrito', []);
        $productoId = $request->input('producto_id');

        $carrito = array_values(array_filter($carrito, function ($item) use ($productoId) {
            return $item['producto_id'] != $productoId;
        }));

        $request->session()->put('carrito', $carrito);
        return redirect('/carrito');
    }

    // ── Checkout (Pasarela de Pago) ────────────────────

    public function checkout(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para realizar un pedido.');
        }

        $carrito = $request->session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect('/carrito')->with('error', 'Tu carrito está vacío.');
        }

        // Verificar si el usuario tiene dirección
        $dirData = $this->api->getMiDireccion($token);

        if (!$dirData || !$dirData['tiene_direccion']) {
            return redirect('/direccion')->with('aviso', 'Para completar tu compra, primero necesitas registrar tu dirección de envío.');
        }

        // Calcular totales
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        $iva = $total * 0.16;
        $granTotal = $total + $iva;

        return view('checkout', [
            'carrito'  => $carrito,
            'subtotal' => $total,
            'iva'      => $iva,
            'total'    => $granTotal,
            'dirData'  => $dirData,
        ]);
    }

    public function checkoutPost(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        $carrito = $request->session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect('/carrito')->with('error', 'Tu carrito está vacío.');
        }

        // Verificar dirección
        $dirData = $this->api->getMiDireccion($token);
        if (!$dirData || !$dirData['tiene_direccion']) {
            return redirect('/direccion');
        }

        // Validar campos de tarjeta (validación básica en servidor)
        $numTarjeta = preg_replace('/\s+/', '', $request->input('numero_tarjeta', ''));
        $nombreTitular = trim($request->input('nombre_titular', ''));
        $vencimiento = trim($request->input('vencimiento', ''));
        $cvv = trim($request->input('cvv', ''));

        if (strlen($numTarjeta) < 13 || strlen($numTarjeta) > 19) {
            return back()->with('error', 'Número de tarjeta inválido.');
        }
        if (empty($nombreTitular)) {
            return back()->with('error', 'El nombre del titular es obligatorio.');
        }
        if (!preg_match('/^\d{2}\/\d{2}$/', $vencimiento)) {
            return back()->with('error', 'Fecha de vencimiento inválida (formato MM/AA).');
        }
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return back()->with('error', 'CVV inválido.');
        }

        // Crear el pedido vía API
        $usuario = $request->session()->get('usuario');
        $productos = [];
        foreach ($carrito as $item) {
            $productos[] = [
                'id_producto'    => intval($item['producto_id']),
                'cantidad'       => intval($item['cantidad']),
                'precio_unitario' => floatval($item['precio']),
            ];
        }

        $result = $this->api->crearPedido([
            'id_usuario'         => $usuario['id'],
            'id_direccion_envio' => $dirData['direccion']['id'],
            'productos'          => $productos,
        ], $token);

        if ($result['status'] === 201) {
            $request->session()->forget('carrito');
            $codigoPedido = $result['data']['codigo_pedido'] ?? '';
            return redirect('/pago-exitoso?pedido=' . urlencode($codigoPedido));
        }

        $msg = $result['data']['detail'] ?? 'Error al procesar el pago. Intenta de nuevo.';
        return back()->with('error', $msg);
    }

    public function pagoExitoso(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        $codigoPedido = $request->query('pedido', '');
        return view('pago_exitoso', ['codigoPedido' => $codigoPedido]);
    }

    // ── Dirección ───────────────────────────────────────

    public function direccionForm(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        // Obtener catálogos de la API
        $estados = $this->api->get('/api/catalogos/estados', $token) ?? [];

        // Verificar si ya tiene dirección
        $dirData = $this->api->getMiDireccion($token);

        return view('direccion', [
            'estados'  => $estados,
            'dirData'  => $dirData,
        ]);
    }

    public function direccionPost(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        $result = $this->api->crearMiDireccion([
            'calle'         => $request->input('calle'),
            'numero'        => $request->input('numero'),
            'codigo_postal' => $request->input('codigo_postal'),
            'municipio'     => $request->input('municipio'),
            'estado'        => $request->input('estado'),
            'localidad'     => $request->input('localidad'),
            'colonia'       => $request->input('colonia'),
            'sin_numero'    => $request->has('sin_numero'),
        ], $token);

        if ($result['status'] === 200) {
            // Actualizar dirección en sesión
            $dirData = $this->api->getMiDireccion($token);
            $request->session()->put('direccion', $dirData);

            // Si venía del checkout, redirigir ahí
            if ($request->session()->has('carrito') && count($request->session()->get('carrito', [])) > 0) {
                return redirect('/checkout')->with('success', '¡Dirección guardada! Ahora completa tu pedido.');
            }
            return redirect('/catalogo')->with('carrito_msg', '¡Dirección guardada exitosamente!');
        }

        return back()->with('error', 'No se pudo guardar la dirección. Intenta de nuevo.');
    }

    // ── Teléfono ────────────────────────────────────────

    public function telefonoForm(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        $telefono = $request->session()->get('usuario.telefono', '');
        return view('telefono', ['telefono' => $telefono]);
    }

    public function telefonoPost(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        $telefono = trim($request->input('telefono', ''));

        $result = $this->api->patch('/api/usuarios/me/telefono', ['telefono' => $telefono], $token);

        if (($result['status'] ?? 500) === 200) {
            $request->session()->put('usuario.telefono', $telefono ?: null);
            return redirect('/telefono')->with('success', '¡Teléfono actualizado correctamente!');
        }

        return back()->with('error', 'No se pudo actualizar el teléfono. Intenta de nuevo.');
    }

    // ── Pedidos ─────────────────────────────────────────

    public function pedidos(Request $request)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login')->with('error', 'Inicia sesión para ver tus pedidos.');
        }

        // Usar /api/mis-pedidos para filtrar solo los del usuario
        $data = $this->api->getMisPedidos($token);
        $pedidos = $data['pedidos'] ?? [];

        return view('pedidos', ['pedidos' => $pedidos]);
    }

    public function detallePedido(Request $request, $id)
    {
        $token = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        $detalle = $this->api->getPedido(intval($id), $token);
        if (!$detalle || (isset($detalle['detail']) && $detalle['detail'] === 'Pedido no encontrado')) {
            return redirect('/pedidos')->with('error', 'Pedido no encontrado.');
        }

        return view('detalle_pedido_cliente', ['pedido' => $detalle]);
    }

    public function cancelarPedido(Request $request, $id)
    {
        $token = $request->session()->get('token');
        $result = $this->api->cancelarPedido(intval($id), $token);

        if ($result['status'] === 200) {
            return redirect("/pedidos/{$id}")->with('success', 'Pedido cancelado exitosamente.');
        }

        $msg = $result['data']['detail'] ?? 'No se pudo cancelar el pedido.';
        return redirect("/pedidos/{$id}")->with('error', $msg);
    }
}
