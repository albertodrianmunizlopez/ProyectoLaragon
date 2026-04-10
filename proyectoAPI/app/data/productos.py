"""
Operaciones CRUD para productos (inventario), tipos de autoparte y marcas.
"""
from typing import Optional, List
from sqlalchemy.orm import Session

from app.models.productos import Producto, EstatusProducto, TipoAutoparte, Marca


# ── Tipos de Autoparte ─────────────────────────────────────

def get_tipos_autoparte(db: Session) -> List[TipoAutoparte]:
    """Obtener todos los tipos de autoparte ordenados alfabéticamente."""
    return db.query(TipoAutoparte).order_by(TipoAutoparte.nombre).all()


def create_tipo_autoparte(db: Session, nombre: str) -> TipoAutoparte:
    """Crear un nuevo tipo de autoparte."""
    tipo = TipoAutoparte(nombre=nombre.strip())
    db.add(tipo)
    db.commit()
    db.refresh(tipo)
    return tipo


def get_tipo_by_nombre(db: Session, nombre: str) -> Optional[TipoAutoparte]:
    """Buscar tipo por nombre (case-insensitive)."""
    return db.query(TipoAutoparte).filter(TipoAutoparte.nombre.ilike(nombre.strip())).first()


# ── Marcas ─────────────────────────────────────────────────

def get_marcas(db: Session) -> List[Marca]:
    """Obtener todas las marcas ordenadas alfabéticamente."""
    return db.query(Marca).order_by(Marca.nombre).all()


def create_marca(db: Session, nombre: str) -> Marca:
    """Crear una nueva marca."""
    marca = Marca(nombre=nombre.strip())
    db.add(marca)
    db.commit()
    db.refresh(marca)
    return marca


def get_marca_by_nombre(db: Session, nombre: str) -> Optional[Marca]:
    """Buscar marca por nombre (case-insensitive)."""
    return db.query(Marca).filter(Marca.nombre.ilike(nombre.strip())).first()


# ── Productos ──────────────────────────────────────────────

def get_productos(
    db: Session,
    filtro_estatus: Optional[str] = None,
    filtro_tipo: Optional[int] = None,
    filtro_marca: Optional[int] = None,
    orden: Optional[str] = None,
    skip: int = 0,
    limit: int = 100,
) -> List[Producto]:
    """Obtener lista de productos con filtros y ordenamiento."""
    query = db.query(Producto)

    if filtro_estatus:
        query = query.filter(Producto.estatus_producto == filtro_estatus)
    if filtro_tipo:
        query = query.filter(Producto.id_tipo_autoparte == filtro_tipo)
    if filtro_marca:
        query = query.filter(Producto.id_marca == filtro_marca)

    # Ordenamiento
    if orden == "nombre_asc":
        query = query.order_by(Producto.nombre.asc())
    elif orden == "nombre_desc":
        query = query.order_by(Producto.nombre.desc())
    elif orden == "precio_asc":
        query = query.order_by(Producto.precio.asc())
    elif orden == "precio_desc":
        query = query.order_by(Producto.precio.desc())
    else:
        query = query.order_by(Producto.nombre)

    return query.offset(skip).limit(limit).all()


def get_producto_by_id(db: Session, producto_id: int) -> Optional[Producto]:
    """Obtener producto por ID."""
    return db.query(Producto).filter(Producto.id == producto_id).first()


def get_producto_by_codigo(db: Session, codigo: str) -> Optional[Producto]:
    """Buscar producto por su código SKU."""
    return db.query(Producto).filter(Producto.codigo == codigo.upper()).first()


def create_producto(db: Session, datos: dict) -> Producto:
    """Crear un nuevo producto."""
    producto = Producto(**datos)
    db.add(producto)
    db.commit()
    db.refresh(producto)
    return producto


def update_producto(db: Session, producto: Producto, datos: dict) -> Producto:
    """Actualizar campos de un producto."""
    for campo, valor in datos.items():
        if hasattr(producto, campo):
            setattr(producto, campo, valor)
    db.commit()
    db.refresh(producto)
    return producto


def ajustar_stock(db: Session, producto: Producto, cantidad: int, tipo: str) -> Producto:
    """
    Ajustar stock de un producto.
    tipo: 'entrada' suma, 'salida' resta.
    """
    if tipo == "entrada":
        producto.cantidad += cantidad
    elif tipo == "salida":
        producto.cantidad = max(0, producto.cantidad - cantidad)

    # Actualizar estatus automáticamente según la cantidad
    if producto.cantidad == 0 and producto.estatus_producto != EstatusProducto.descontinuado:
        producto.estatus_producto = EstatusProducto.agotado
    elif producto.cantidad > 0 and producto.estatus_producto == EstatusProducto.agotado:
        producto.estatus_producto = EstatusProducto.en_stock

    db.commit()
    db.refresh(producto)
    return producto


def soft_delete_producto(db: Session, producto: Producto) -> Producto:
    """Soft delete — marca como descontinuado en vez de eliminar."""
    producto.estatus_producto = EstatusProducto.descontinuado
    producto.cantidad = 0
    db.commit()
    db.refresh(producto)
    return producto
