"""
Schemas Pydantic para productos (inventario), tipos de autoparte y marcas.
"""
from pydantic import BaseModel, field_validator
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
    descripcion: str
    imagen_url: Optional[str] = None
    id_tipo_autoparte: int
    id_marca: int
    cantidad: int = 0
    estatus_producto: str = "en_stock"
    precio: Decimal

    @field_validator('nombre')
    @classmethod
    def nombre_no_vacio(cls, v):
        if not v or not v.strip():
            raise ValueError('El nombre no puede estar vacío')
        return v.strip()

    @field_validator('descripcion')
    @classmethod
    def descripcion_no_vacia(cls, v):
        if not v or not v.strip():
            raise ValueError('La descripción es obligatoria')
        return v.strip()

    @field_validator('precio')
    @classmethod
    def precio_positivo(cls, v):
        if v <= 0:
            raise ValueError('El precio debe ser mayor a 0')
        return v

    @field_validator('cantidad')
    @classmethod
    def cantidad_no_negativa(cls, v):
        if v < 0:
            raise ValueError('El stock no puede ser negativo')
        return v


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

    @field_validator('nombre')
    @classmethod
    def nombre_no_vacio(cls, v):
        if v is not None and not v.strip():
            raise ValueError('El nombre no puede estar vacío')
        return v.strip() if v else v

    @field_validator('descripcion')
    @classmethod
    def descripcion_no_vacia(cls, v):
        if v is not None and not v.strip():
            raise ValueError('La descripción es obligatoria')
        return v.strip() if v else v

    @field_validator('precio')
    @classmethod
    def precio_positivo(cls, v):
        if v is not None and v <= 0:
            raise ValueError('El precio debe ser mayor a 0')
        return v

    @field_validator('cantidad')
    @classmethod
    def cantidad_no_negativa(cls, v):
        if v is not None and v < 0:
            raise ValueError('El stock no puede ser negativo')
        return v


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
