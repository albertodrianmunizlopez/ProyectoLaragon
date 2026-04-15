<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para comunicarse con la API REST (FastAPI).
 */
class ApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('MACUIN_API_URL', 'http://fastapi:8080');
    }

    // ── Helpers ──────────────────────────────────────────

    public function get(string $endpoint, ?string $token = null, array $params = [])
    {
        try {
            $request = Http::timeout(5);
            if ($token) {
                $request = $request->withToken($token);
            }
            $response = $request->get("{$this->baseUrl}{$endpoint}", $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error("[API GET] {$endpoint}: " . $e->getMessage());
            return null;
        }
    }

    public function post(string $endpoint, array $data = [], ?string $token = null)
    {
        try {
            $request = Http::timeout(5);
            if ($token) {
                $request = $request->withToken($token);
            }
            $response = $request->post("{$this->baseUrl}{$endpoint}", $data);
            return [
                'data'   => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("[API POST] {$endpoint}: " . $e->getMessage());
            return ['data' => null, 'status' => 500];
        }
    }

    public function patch(string $endpoint, array $data = [], ?string $token = null)
    {
        try {
            $request = Http::timeout(5);
            if ($token) {
                $request = $request->withToken($token);
            }
            $response = $request->patch("{$this->baseUrl}{$endpoint}", $data);
            return [
                'data'   => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("[API PATCH] {$endpoint}: " . $e->getMessage());
            return ['data' => null, 'status' => 500];
        }
    }

    // ── Autenticación ───────────────────────────────────

    public function login(string $email, string $password)
    {
        return $this->post('/api/auth/login', [
            'email'    => $email,
            'password' => $password,
        ]);
    }

    public function register(string $nombre, string $apellidos, string $email, string $password)
    {
        return $this->post('/api/auth/register', [
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'password'  => $password,
        ]);
    }

    // ── Productos ───────────────────────────────────────

    public function getProductos(?string $token = null, array $params = [])
    {
        return $this->get('/api/productos', $token, $params) ?? [];
    }

    public function getProducto(int $id, ?string $token = null)
    {
        return $this->get("/api/productos/{$id}", $token);
    }

    // ── Pedidos ─────────────────────────────────────────

    public function getMisPedidos(?string $token = null)
    {
        return $this->get('/api/mis-pedidos', $token);
    }

    public function getPedidos(?string $token = null)
    {
        return $this->get('/api/pedidos', $token);
    }

    public function getPedido(int $id, ?string $token = null)
    {
        return $this->get("/api/pedidos/{$id}", $token);
    }

    public function cancelarPedido(int $id, ?string $token = null)
    {
        return $this->patch("/api/pedidos/{$id}/cancelar", [], $token);
    }

    public function crearPedido(array $data, ?string $token = null)
    {
        return $this->post('/api/pedidos', $data, $token);
    }

    // ── Usuario / Dirección ─────────────────────────────

    public function getMiPerfil(?string $token = null)
    {
        return $this->get('/api/usuarios/me', $token);
    }

    public function getMiDireccion(?string $token = null)
    {
        return $this->get('/api/usuarios/me/direccion', $token);
    }

    public function crearMiDireccion(array $data, ?string $token = null)
    {
        return $this->post('/api/usuarios/me/direccion', $data, $token);
    }
}
