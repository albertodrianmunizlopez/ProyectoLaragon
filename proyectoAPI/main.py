"""
Macuin API — Punto de entrada de FastAPI.
API REST centralizada para conectar Flask y Laravel con PostgreSQL.
"""
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.routers import usuarios, direcciones, productos, pedidos

# ── Crear aplicación ───────────────────────────────────────

app = FastAPI(
    title="Macuin API",
    description="API REST para el sistema de autopartes Macuin. Conecta Flask (portal) y Laravel (admin) con PostgreSQL.",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
)

# ── CORS — Permitir requests de Flask y Laravel ────────────

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost:5000",   # Flask
        "http://localhost:8000",   # Laravel
        "http://macuin_flask:5000",
        "http://macuin_laravel:8000",
        "*",  # Desarrollo — en producción quitar esto
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ── Registrar Routers ──────────────────────────────────────

app.include_router(usuarios.router)
app.include_router(direcciones.router)
app.include_router(productos.router)
app.include_router(pedidos.router)


# ── Health Check ───────────────────────────────────────────

@app.get("/", tags=["Health"])
def root():
    """Health check — verifica que la API está activa."""
    return {
        "api": "Macuin API",
        "version": "1.0.0",
        "status": "online",
        "docs": "/docs",
    }


@app.get("/health", tags=["Health"])
def health_check():
    """Health check para Docker."""
    return {"status": "healthy"}
