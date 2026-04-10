"""
Schemas Pydantic para direcciones y catálogos.
"""
from pydantic import BaseModel
from typing import Optional


# ── Catálogos ──────────────────────────────────────────────

class EstadoBase(BaseModel):
    nombre: str

class EstadoResponse(EstadoBase):
    id: int
    class Config:
        from_attributes = True


class MunicipioBase(BaseModel):
    nombre: str
    id_estado: int

class MunicipioResponse(MunicipioBase):
    id: int
    class Config:
        from_attributes = True


class CodigoPostalBase(BaseModel):
    codigo: str

class CodigoPostalResponse(CodigoPostalBase):
    id: int
    class Config:
        from_attributes = True


class CalleBase(BaseModel):
    nombre: str

class CalleResponse(CalleBase):
    id: int
    class Config:
        from_attributes = True


class NumeroViviendaBase(BaseModel):
    numero: str

class NumeroViviendaResponse(NumeroViviendaBase):
    id: int
    class Config:
        from_attributes = True


# ── Direcciones ────────────────────────────────────────────

class DireccionCreate(BaseModel):
    id_calle: int
    id_numero_vivienda: Optional[int] = None
    id_codigo_postal: int
    id_municipio: int
    id_estado: int
    localidad: Optional[str] = None
    colonia: Optional[str] = None


class DireccionResponse(BaseModel):
    id: int
    id_calle: int
    id_numero_vivienda: Optional[int] = None
    id_codigo_postal: int
    id_municipio: int
    id_estado: int
    localidad: Optional[str] = None
    colonia: Optional[str] = None
    class Config:
        from_attributes = True


class DireccionCompletaResponse(BaseModel):
    """Dirección con todos sus datos resueltos (no solo IDs)."""
    id: int
    calle: str
    numero: str
    codigo_postal: str
    municipio: str
    estado: str
    localidad: Optional[str] = None
    colonia: Optional[str] = None

    class Config:
        from_attributes = True
