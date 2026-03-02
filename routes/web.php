<?php

use Illuminate\Support\Facades\Route;

/* ---------- INICIO ---------- */
Route::get('/', function () {
    return view('inicio');
});

/* ---------- LOGIN ---------- */
Route::get('/login', function () {
    return view('login');
});

/* ---------- REGISTRO ---------- */
Route::get('/registro', function () {
    return view('registro');
});

/* ---------- CATÁLOGO ---------- */
Route::get('/catalogo', function () {
    return view('catalogo');
});

/* ---------- PEDIDOS ---------- */
Route::get('/pedidos', function () {
    return view('pedidos');
});

