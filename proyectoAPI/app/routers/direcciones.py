"""
Router de direcciones y catálogos.
"""
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List

from app.data.database import get_db
from app.data import direcciones as crud
from app.schemas.direcciones import (
    EstadoBase, EstadoResponse,
    MunicipioBase, MunicipioResponse,
    CodigoPostalBase, CodigoPostalResponse,
    CalleBase, CalleResponse,
    NumeroViviendaBase, NumeroViviendaResponse,
    DireccionCreate, DireccionResponse, DireccionCompletaResponse,
)
from app.security.auth import get_current_user

router = APIRouter(prefix="/api", tags=["Direcciones y Catálogos"])


# ── Catálogos (lectura pública, creación protegida) ────────

@router.get("/catalogos/estados", response_model=List[EstadoResponse])
def listar_estados(db: Session = Depends(get_db)):
    """Listar todos los estados."""
    return crud.get_estados(db)


@router.post("/catalogos/estados", response_model=EstadoResponse, status_code=201)
def crear_estado(
    datos: EstadoBase,
    db: Session = Depends(get_db),
    _user=Depends(get_current_user),
):
    """Crear un nuevo estado."""
    return crud.create_estado(db, datos.nombre)


@router.get("/catalogos/municipios", response_model=List[MunicipioResponse])
def listar_municipios(id_estado: int = None, db: Session = Depends(get_db)):
    """Listar municipios. Filtra por id_estado si se proporciona."""
    return crud.get_municipios(db, id_estado=id_estado)


@router.post("/catalogos/municipios", response_model=MunicipioResponse, status_code=201)
def crear_municipio(
    datos: MunicipioBase,
    db: Session = Depends(get_db),
    _user=Depends(get_current_user),
):
    """Crear un nuevo municipio."""
    return crud.create_municipio(db, datos.nombre, datos.id_estado)


@router.get("/catalogos/codigos-postales", response_model=List[CodigoPostalResponse])
def listar_codigos_postales(db: Session = Depends(get_db)):
    """Listar todos los códigos postales."""
    return crud.get_codigos_postales(db)


@router.post("/catalogos/codigos-postales", response_model=CodigoPostalResponse, status_code=201)
def crear_codigo_postal(
    datos: CodigoPostalBase,
    db: Session = Depends(get_db),
    _user=Depends(get_current_user),
):
    """Crear un nuevo código postal."""
    return crud.create_codigo_postal(db, datos.codigo)


@router.get("/catalogos/calles", response_model=List[CalleResponse])
def listar_calles(db: Session = Depends(get_db)):
    """Listar todas las calles."""
    return crud.get_calles(db)


@router.post("/catalogos/calles", response_model=CalleResponse, status_code=201)
def crear_calle(
    datos: CalleBase,
    db: Session = Depends(get_db),
    _user=Depends(get_current_user),
):
    """Crear una nueva calle."""
    return crud.create_calle(db, datos.nombre)


@router.get("/catalogos/numeros-vivienda", response_model=List[NumeroViviendaResponse])
def listar_numeros_vivienda(db: Session = Depends(get_db)):
    """Listar todos los números de vivienda."""
    return crud.get_numeros_vivienda(db)


@router.post("/catalogos/numeros-vivienda", response_model=NumeroViviendaResponse, status_code=201)
def crear_numero_vivienda(
    datos: NumeroViviendaBase,
    db: Session = Depends(get_db),
    _user=Depends(get_current_user),
):
    """Crear un nuevo número de vivienda."""
    return crud.create_numero_vivienda(db, datos.numero)


# ── Direcciones ────────────────────────────────────────────

@router.post("/direcciones", response_model=DireccionResponse, status_code=201)
def crear_direccion(
    datos: DireccionCreate,
    db: Session = Depends(get_db),
    _user=Depends(get_current_user),
):
    """Crear una nueva dirección."""
    return crud.create_direccion(db, datos.model_dump())


@router.get("/direcciones/{direccion_id}", response_model=DireccionCompletaResponse)
def obtener_direccion(
    direccion_id: int,
    db: Session = Depends(get_db),
):
    """Obtener dirección completa con datos resueltos."""
    direccion = crud.get_direccion_by_id(db, direccion_id)
    if not direccion:
        raise HTTPException(status_code=404, detail="Dirección no encontrada")
    return DireccionCompletaResponse(
        id=direccion.id,
        calle=direccion.calle.nombre if direccion.calle else "",
        numero=direccion.numero_vivienda.numero if direccion.numero_vivienda else "",
        codigo_postal=direccion.codigo_postal.codigo if direccion.codigo_postal else "",
        municipio=direccion.municipio.nombre if direccion.municipio else "",
        estado=direccion.estado.nombre if direccion.estado else "",
    )
