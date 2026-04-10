"""
Schemas Pydantic para pedidos.
"""
from pydantic import BaseModel
from typing import Optional, List
from datetime import datetime
from decimal import Decimal


# ── Productos del pedido ───────────────────────────────────

class PedidoProductoCreate(BaseModel):
    id_producto: int
    cantidad: int
    precio_unitario: Decimal


class PedidoProductoResponse(BaseModel):
    id: int
    id_producto: int
    cantidad: int
    precio_unitario: Decimal
    # Datos del producto resueltos
    producto_codigo: Optional[str] = None
    producto_nombre: Optional[str] = None

    class Config:
        from_attributes = True


# ── Pedidos ────────────────────────────────────────────────

class PedidoCreate(BaseModel):
    id_usuario: int
    id_direccion_envio: int
    productos: List[PedidoProductoCreate]


class PedidoResponse(BaseModel):
    id: int
    codigo_pedido: str
    id_usuario: int
    id_direccion_envio: int
    estado_pedido: str
    subtotal: Decimal
    impuestos: Decimal
    total: Decimal
    fecha_pedido: datetime
    updated_at: datetime
    primer_producto_imagen: Optional[str] = None

    class Config:
        from_attributes = True


class PedidoDetalleResponse(PedidoResponse):
    """Pedido con la lista de productos incluidos."""
    productos: List[PedidoProductoResponse] = []

    # Datos del usuario resueltos
    usuario_nombre: Optional[str] = None
    usuario_email: Optional[str] = None

    # Dirección resuelta
    direccion_completa: Optional[str] = None


class ActualizarEstadoRequest(BaseModel):
    estado: str


class PedidoListResponse(BaseModel):
    """Respuesta paginada para listado de pedidos."""
    total: int
    pedidos: List[PedidoResponse]
