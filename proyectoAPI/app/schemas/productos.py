"""
Schemas Pydantic para productos (inventario), tipos de autoparte y marcas.
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime
from decimal import Decimal


# ── Catálogos ──────────────────────────────────────────────

class TipoAutoparteCreate(BaseModel):
    nombre: str

class TipoAutoparteResponse(BaseModel):
    id: int
    nombre: str

    class Config:
        from_attributes = True


class MarcaCreate(BaseModel):
    nombre: str

class MarcaResponse(BaseModel):
    id: int
    nombre: str

    class Config:
        from_attributes = True


# ── Productos ──────────────────────────────────────────────

class ProductoCreate(BaseModel):
    codigo: Optional[str] = None
    nombre: str
    descripcion: Optional[str] = None
    imagen_url: Optional[str] = None
    id_tipo_autoparte: Optional[int] = None
    id_marca: Optional[int] = None
    cantidad: int = 0
    estatus_producto: str = "en_stock"
    precio: Decimal


class ProductoUpdate(BaseModel):
    codigo: Optional[str] = None
    nombre: Optional[str] = None
    descripcion: Optional[str] = None
    imagen_url: Optional[str] = None
    id_tipo_autoparte: Optional[int] = None
    id_marca: Optional[int] = None
    cantidad: Optional[int] = None
    estatus_producto: Optional[str] = None
    precio: Optional[Decimal] = None


class ProductoResponse(BaseModel):
    id: int
    codigo: str
    nombre: str
    descripcion: Optional[str] = None
    imagen_url: Optional[str] = None
    id_tipo_autoparte: Optional[int] = None
    id_marca: Optional[int] = None
    tipo_nombre: Optional[str] = None
    marca_nombre: Optional[str] = None
    cantidad: int
    estatus_producto: str
    precio: Decimal
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True


class AjusteStockRequest(BaseModel):
    cantidad: int
    tipo_ajuste: str  # "entrada" o "salida"
