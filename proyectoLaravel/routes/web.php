<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiendaController;

/* ---------- INICIO ---------- */
Route::get('/', [TiendaController::class, 'inicio']);

/* ---------- LOGIN ---------- */
Route::get('/login', [TiendaController::class, 'loginForm']);
Route::post('/login', [TiendaController::class, 'loginPost']);

/* ---------- REGISTRO ---------- */
Route::get('/registro', [TiendaController::class, 'registroForm']);
Route::post('/registro', [TiendaController::class, 'registroPost']);
Route::get('/api-check-email', [TiendaController::class, 'checkEmail']);

/* ---------- LOGOUT ---------- */
Route::post('/logout', [TiendaController::class, 'logout']);

/* ---------- CATÁLOGO ---------- */
Route::get('/catalogo', [TiendaController::class, 'catalogo']);

/* ---------- CARRITO ---------- */
Route::get('/carrito', [TiendaController::class, 'carrito']);
Route::post('/carrito/agregar', [TiendaController::class, 'agregarAlCarrito']);
Route::post('/carrito/eliminar', [TiendaController::class, 'eliminarDelCarrito']);

/* ---------- CHECKOUT ---------- */
Route::get('/checkout', [TiendaController::class, 'checkout']);
Route::post('/checkout', [TiendaController::class, 'checkoutPost']);
Route::get('/pago-exitoso', [TiendaController::class, 'pagoExitoso']);

/* ---------- DIRECCIÓN ---------- */
Route::get('/direccion', [TiendaController::class, 'direccionForm']);
Route::post('/direccion', [TiendaController::class, 'direccionPost']);

/* ---------- PEDIDOS ---------- */
Route::get('/pedidos', [TiendaController::class, 'pedidos']);
Route::get('/pedidos/{id}', [TiendaController::class, 'detallePedido']);
Route::post('/pedidos/{id}/cancelar', [TiendaController::class, 'cancelarPedido']);

