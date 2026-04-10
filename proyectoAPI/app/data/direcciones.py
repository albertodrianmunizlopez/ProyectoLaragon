"""
Operaciones CRUD para direcciones y catálogos.
"""
from typing import Optional, List
from sqlalchemy.orm import Session

from app.models.direcciones import (
    Estado, Municipio, CodigoPostal, Calle, NumeroVivienda, Direccion
)


# ── Catálogos ──────────────────────────────────────────────

def get_estados(db: Session) -> List[Estado]:
    return db.query(Estado).order_by(Estado.nombre).all()


def get_municipios(db: Session, id_estado: Optional[int] = None) -> List[Municipio]:
    query = db.query(Municipio)
    if id_estado:
        query = query.filter(Municipio.id_estado == id_estado)
    return query.order_by(Municipio.nombre).all()


def get_codigos_postales(db: Session) -> List[CodigoPostal]:
    return db.query(CodigoPostal).order_by(CodigoPostal.codigo).all()


def get_calles(db: Session) -> List[Calle]:
    return db.query(Calle).order_by(Calle.nombre).all()


def get_numeros_vivienda(db: Session) -> List[NumeroVivienda]:
    return db.query(NumeroVivienda).order_by(NumeroVivienda.id).all()


# ── Crear catálogos ────────────────────────────────────────

def create_estado(db: Session, nombre: str) -> Estado:
    estado = Estado(nombre=nombre)
    db.add(estado)
    db.commit()
    db.refresh(estado)
    return estado


def create_municipio(db: Session, nombre: str, id_estado: int) -> Municipio:
    municipio = Municipio(nombre=nombre, id_estado=id_estado)
    db.add(municipio)
    db.commit()
    db.refresh(municipio)
    return municipio


def create_codigo_postal(db: Session, codigo: str) -> CodigoPostal:
    cp = CodigoPostal(codigo=codigo)
    db.add(cp)
    db.commit()
    db.refresh(cp)
    return cp


def create_calle(db: Session, nombre: str) -> Calle:
    calle = Calle(nombre=nombre)
    db.add(calle)
    db.commit()
    db.refresh(calle)
    return calle


def create_numero_vivienda(db: Session, numero: str) -> NumeroVivienda:
    nv = NumeroVivienda(numero=numero)
    db.add(nv)
    db.commit()
    db.refresh(nv)
    return nv


# ── Direcciones ────────────────────────────────────────────

def get_direccion_by_id(db: Session, direccion_id: int) -> Optional[Direccion]:
    return db.query(Direccion).filter(Direccion.id == direccion_id).first()


def create_direccion(db: Session, datos: dict) -> Direccion:
    direccion = Direccion(**datos)
    db.add(direccion)
    db.commit()
    db.refresh(direccion)
    return direccion


def direccion_a_texto(db: Session, direccion: Direccion) -> str:
    """Construye la dirección completa como texto legible."""
    partes = []
    if direccion.calle:
        partes.append(direccion.calle.nombre)
    if direccion.numero_vivienda:
        partes.append(f"#{direccion.numero_vivienda.numero}")
    else:
        partes.append("S/N")
    if direccion.colonia:
        partes.append(f"Col. {direccion.colonia}")
    if direccion.localidad:
        partes.append(direccion.localidad)
    if direccion.codigo_postal:
        partes.append(f"CP {direccion.codigo_postal.codigo}")
    if direccion.municipio:
        partes.append(direccion.municipio.nombre)
    if direccion.estado:
        partes.append(direccion.estado.nombre)
    return ", ".join(partes)
