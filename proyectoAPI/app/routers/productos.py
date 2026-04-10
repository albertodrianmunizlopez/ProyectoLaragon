"""
Router de productos (inventario) — CRUD + ajuste de stock.
Incluye endpoints para catálogos: tipos de autoparte y marcas.
"""
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List, Optional

from app.data.database import get_db
from app.data import productos as crud
from app.schemas.productos import (
    ProductoCreate, ProductoUpdate, ProductoResponse, AjusteStockRequest,
    TipoAutoparteCreate, TipoAutoparteResponse,
    MarcaCreate, MarcaResponse,
)
from app.security.auth import get_current_user, require_admin
from app.models.usuarios import Usuario

router = APIRouter(prefix="/api/productos", tags=["Productos / Inventario"])


# ── Helper: convertir Producto ORM a dict con nombres de tipo/marca ──

def _producto_con_nombres(producto):
    """Agrega tipo_nombre y marca_nombre al objeto producto para la respuesta."""
    data = {
        "id": producto.id,
        "codigo": producto.codigo,
        "nombre": producto.nombre,
        "descripcion": producto.descripcion,
        "imagen_url": producto.imagen_url,
        "id_tipo_autoparte": producto.id_tipo_autoparte,
        "id_marca": producto.id_marca,
        "tipo_nombre": producto.tipo_autoparte.nombre if producto.tipo_autoparte else None,
        "marca_nombre": producto.marca.nombre if producto.marca else None,
        "cantidad": producto.cantidad,
        "estatus_producto": producto.estatus_producto.value if hasattr(producto.estatus_producto, 'value') else producto.estatus_producto,
        "precio": producto.precio,
        "created_at": producto.created_at,
        "updated_at": producto.updated_at,
    }
    return data


# ── Tipos de Autoparte ─────────────────────────────────────

@router.get("/catalogos/tipos", response_model=List[TipoAutoparteResponse])
def listar_tipos(db: Session = Depends(get_db)):
    """Listar todos los tipos de autoparte."""
    return crud.get_tipos_autoparte(db)


@router.post("/catalogos/tipos", response_model=TipoAutoparteResponse, status_code=201)
def crear_tipo(
    datos: TipoAutoparteCreate,
    db: Session = Depends(get_db),
):
    """Crear un nuevo tipo de autoparte."""
    existente = crud.get_tipo_by_nombre(db, datos.nombre)
    if existente:
        raise HTTPException(status_code=409, detail="Ya existe un tipo con ese nombre")
    return crud.create_tipo_autoparte(db, datos.nombre)


# ── Marcas ─────────────────────────────────────────────────

@router.get("/catalogos/marcas", response_model=List[MarcaResponse])
def listar_marcas(db: Session = Depends(get_db)):
    """Listar todas las marcas."""
    return crud.get_marcas(db)


@router.post("/catalogos/marcas", response_model=MarcaResponse, status_code=201)
def crear_marca(
    datos: MarcaCreate,
    db: Session = Depends(get_db),
):
    """Crear una nueva marca."""
    existente = crud.get_marca_by_nombre(db, datos.nombre)
    if existente:
        raise HTTPException(status_code=409, detail="Ya existe una marca con ese nombre")
    return crud.create_marca(db, datos.nombre)


# ── Productos ──────────────────────────────────────────────

@router.get("")
def listar_productos(
    estatus: Optional[str] = None,
    tipo: Optional[int] = None,
    marca: Optional[int] = None,
    orden: Optional[str] = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
):
    """Listar productos con filtros opcionales y ordenamiento."""
    productos = crud.get_productos(
        db,
        filtro_estatus=estatus,
        filtro_tipo=tipo,
        filtro_marca=marca,
        orden=orden,
        skip=skip,
        limit=limit,
    )
    return [_producto_con_nombres(p) for p in productos]


@router.get("/buscar")
def buscar_por_codigo(
    codigo: str,
    db: Session = Depends(get_db),
):
    """Buscar producto por código SKU."""
    producto = crud.get_producto_by_codigo(db, codigo)
    if not producto:
        raise HTTPException(status_code=404, detail=f"Producto con código '{codigo}' no encontrado")
    return _producto_con_nombres(producto)


@router.get("/{producto_id}")
def obtener_producto(
    producto_id: int,
    db: Session = Depends(get_db),
):
    """Obtener producto por ID."""
    producto = crud.get_producto_by_id(db, producto_id)
    if not producto:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return _producto_con_nombres(producto)


@router.post("", response_model=ProductoResponse, status_code=201)
def crear_producto(
    datos: ProductoCreate,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Crear un nuevo producto (solo admins). El código SKU se genera automáticamente."""
    from app.models.productos import Marca, Producto as ProductoModel
    import re

    datos_dict = datos.model_dump()

    # Auto-generar código basado en la marca
    if not datos_dict.get("codigo"):
        prefijo = "PROD"
        if datos_dict.get("id_marca"):
            marca = db.query(Marca).filter(Marca.id == datos_dict["id_marca"]).first()
            if marca:
                # Tomar nombre de marca, quitar espacios y acentos, uppercase
                prefijo = re.sub(r'[^A-Za-z0-9]', '', marca.nombre).upper()[:10]

        # Buscar el número más alto existente con ese prefijo
        existentes = db.query(ProductoModel.codigo).filter(
            ProductoModel.codigo.like(f"{prefijo}-%")
        ).all()
        max_num = 0
        for (cod,) in existentes:
            partes = cod.rsplit("-", 1)
            if len(partes) == 2 and partes[1].isdigit():
                max_num = max(max_num, int(partes[1]))

        datos_dict["codigo"] = f"{prefijo}-{max_num + 1:03d}"

    # Verificar que no exista duplicado
    existente = crud.get_producto_by_codigo(db, datos_dict["codigo"])
    if existente:
        raise HTTPException(status_code=409, detail="Ya existe un producto con ese código")
    producto = crud.create_producto(db, datos_dict)
    return _producto_con_nombres(producto)


@router.put("/{producto_id}")
def actualizar_producto(
    producto_id: int,
    datos: ProductoUpdate,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Actualizar un producto (solo admins)."""
    producto = crud.get_producto_by_id(db, producto_id)
    if not producto:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    producto = crud.update_producto(db, producto, datos.model_dump(exclude_unset=True))
    return _producto_con_nombres(producto)


@router.patch("/{producto_id}/stock", response_model=ProductoResponse)
def ajustar_stock(
    producto_id: int,
    datos: AjusteStockRequest,
    db: Session = Depends(get_db),
    _user: Usuario = Depends(get_current_user),
):
    """Ajustar stock: tipo_ajuste = 'entrada' | 'salida'."""
    producto = crud.get_producto_by_id(db, producto_id)
    if not producto:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    if datos.tipo_ajuste not in ("entrada", "salida"):
        raise HTTPException(status_code=400, detail="tipo_ajuste debe ser 'entrada' o 'salida'")
    if datos.cantidad <= 0:
        raise HTTPException(status_code=400, detail="La cantidad debe ser mayor a 0")
    producto = crud.ajustar_stock(db, producto, datos.cantidad, datos.tipo_ajuste)
    return _producto_con_nombres(producto)


@router.delete("/{producto_id}")
def eliminar_producto(
    producto_id: int,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Soft delete — marca el producto como descontinuado."""
    producto = crud.get_producto_by_id(db, producto_id)
    if not producto:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    producto = crud.soft_delete_producto(db, producto)
    return _producto_con_nombres(producto)
