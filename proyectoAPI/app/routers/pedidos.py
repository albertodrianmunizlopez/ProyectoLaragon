"""
Router de pedidos — CRUD + actualización de estado + reportes.
"""
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.data import pedidos as crud
from app.data import direcciones as dir_crud
from app.schemas.pedidos import (
    PedidoCreate, PedidoResponse, PedidoDetalleResponse,
    PedidoProductoResponse, ActualizarEstadoRequest, PedidoListResponse,
)
from app.security.auth import get_current_user, require_admin
from app.models.usuarios import Usuario

router = APIRouter(prefix="/api", tags=["Pedidos"])

ESTADOS_VALIDOS = {"pendiente", "surtido", "enviado", "en_camino", "entregado", "cancelado"}


@router.get("/pedidos")
def listar_pedidos(
    estado: str = None,
    id_usuario: int = None,
    fecha_inicio: str = None,
    fecha_fin: str = None,
    mes: int = None,
    anio: int = None,
    orden: str = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    _user: Usuario = Depends(get_current_user),
):
    """Listar pedidos con filtros avanzados y paginación."""
    pedidos, total = crud.get_pedidos(
        db,
        filtro_estado=estado,
        id_usuario=id_usuario,
        fecha_inicio=fecha_inicio,
        fecha_fin=fecha_fin,
        mes=mes,
        anio=anio,
        orden=orden,
        skip=skip,
        limit=limit,
    )

    # Construir respuesta enriquecida
    resultado = []
    for p in pedidos:
        estado_val = p.estado_pedido.value if hasattr(p.estado_pedido, 'value') else p.estado_pedido
        num_articulos = sum(pp.cantidad for pp in p.productos) if p.productos else 0
        usuario_nombre = f"{p.usuario.nombre} {p.usuario.apellidos}" if p.usuario else f"Usuario #{p.id_usuario}"

        resultado.append({
            "id": p.id,
            "codigo_pedido": p.codigo_pedido,
            "id_usuario": p.id_usuario,
            "id_direccion_envio": p.id_direccion_envio,
            "estado_pedido": estado_val,
            "subtotal": float(p.subtotal),
            "impuestos": float(p.impuestos),
            "total": float(p.total),
            "fecha_pedido": p.fecha_pedido.isoformat() if p.fecha_pedido else None,
            "updated_at": p.updated_at.isoformat() if p.updated_at else None,
            "num_articulos": num_articulos,
            "usuario_nombre": usuario_nombre,
        })

    return {"total": total, "pedidos": resultado}


@router.get("/mis-pedidos", response_model=PedidoListResponse)
def mis_pedidos(
    estado: str = None,
    skip: int = 0,
    limit: int = 20,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Listar solo los pedidos del usuario autenticado."""
    pedidos, total = crud.get_pedidos(
        db, filtro_estado=estado, id_usuario=current_user.id, skip=skip, limit=limit
    )
    return PedidoListResponse(
        total=total,
        pedidos=[PedidoResponse.model_validate(p) for p in pedidos],
    )


@router.get("/pedidos/{pedido_id}", response_model=PedidoDetalleResponse)
def detalle_pedido(
    pedido_id: int,
    db: Session = Depends(get_db),
    _user: Usuario = Depends(get_current_user),
):
    """Obtener detalle completo de un pedido con productos y dirección."""
    pedido = crud.get_pedido_by_id(db, pedido_id)
    if not pedido:
        raise HTTPException(status_code=404, detail="Pedido no encontrado")

    # Construir respuesta con datos resueltos
    productos_response = []
    for pp in pedido.productos:
        productos_response.append(PedidoProductoResponse(
            id=pp.id,
            id_producto=pp.id_producto,
            cantidad=pp.cantidad,
            precio_unitario=pp.precio_unitario,
            producto_codigo=pp.producto.codigo if pp.producto else None,
            producto_nombre=pp.producto.nombre if pp.producto else None,
        ))

    # Dirección como texto
    direccion_texto = None
    if pedido.direccion_envio:
        direccion_texto = dir_crud.direccion_a_texto(db, pedido.direccion_envio)

    return PedidoDetalleResponse(
        id=pedido.id,
        codigo_pedido=pedido.codigo_pedido,
        id_usuario=pedido.id_usuario,
        id_direccion_envio=pedido.id_direccion_envio,
        estado_pedido=pedido.estado_pedido.value if hasattr(pedido.estado_pedido, 'value') else pedido.estado_pedido,
        subtotal=pedido.subtotal,
        impuestos=pedido.impuestos,
        total=pedido.total,
        fecha_pedido=pedido.fecha_pedido,
        updated_at=pedido.updated_at,
        productos=productos_response,
        usuario_nombre=f"{pedido.usuario.nombre} {pedido.usuario.apellidos}" if pedido.usuario else None,
        usuario_email=pedido.usuario.email if pedido.usuario else None,
        direccion_completa=direccion_texto,
    )


@router.post("/pedidos", response_model=PedidoResponse, status_code=201)
def crear_pedido(
    datos: PedidoCreate,
    db: Session = Depends(get_db),
    _user: Usuario = Depends(get_current_user),
):
    """Crear un nuevo pedido con productos."""
    if not datos.productos:
        raise HTTPException(status_code=400, detail="El pedido debe tener al menos un producto")

    items = [item.model_dump() for item in datos.productos]
    try:
        pedido = crud.create_pedido(db, datos.id_usuario, datos.id_direccion_envio, items)
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))
    return pedido


@router.patch("/pedidos/{pedido_id}/estado", response_model=PedidoResponse)
def actualizar_estado(
    pedido_id: int,
    datos: ActualizarEstadoRequest,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Actualizar el estado de un pedido (solo admins)."""
    if datos.estado not in ESTADOS_VALIDOS:
        raise HTTPException(
            status_code=400,
            detail=f"Estado inválido. Opciones: {', '.join(sorted(ESTADOS_VALIDOS))}",
        )
    pedido = crud.get_pedido_by_id(db, pedido_id)
    if not pedido:
        raise HTTPException(status_code=404, detail="Pedido no encontrado")
    return crud.actualizar_estado_pedido(db, pedido, datos.estado)


@router.patch("/pedidos/{pedido_id}/cancelar", response_model=PedidoResponse)
def cancelar_mi_pedido(
    pedido_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Cancelar un pedido propio (solo si es del usuario y está pendiente)."""
    pedido = crud.get_pedido_by_id(db, pedido_id)
    if not pedido:
        raise HTTPException(status_code=404, detail="Pedido no encontrado")
    if pedido.id_usuario != current_user.id:
        raise HTTPException(status_code=403, detail="No puedes cancelar un pedido que no es tuyo")
    estado_actual = pedido.estado_pedido.value if hasattr(pedido.estado_pedido, 'value') else pedido.estado_pedido
    if estado_actual not in ("pendiente", "surtido"):
        raise HTTPException(status_code=400, detail="Solo se pueden cancelar pedidos pendientes o surtidos")
    return crud.actualizar_estado_pedido(db, pedido, "cancelado")


# ── Reportes ───────────────────────────────────────────────

@router.get("/reportes/kpis")
def obtener_kpis(
    fecha_inicio: str = None,
    fecha_fin: str = None,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """KPIs generales con filtro de fechas opcional."""
    return crud.get_kpis(db, fecha_inicio=fecha_inicio, fecha_fin=fecha_fin)


@router.get("/reportes/datos")
def obtener_datos_reporte(
    tipo: str = "ventas",
    fecha_inicio: str = None,
    fecha_fin: str = None,
    estado: str = None,
    marca_id: int = None,
    top: int = None,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Datos tabulares para reportes de ventas, pedidos, inventario o clientes."""
    if tipo == "ventas":
        datos = crud.get_reporte_ventas(db, fecha_inicio, fecha_fin, marca_id=marca_id, top_n=top)
    elif tipo == "pedidos":
        datos = crud.get_reporte_pedidos(db, fecha_inicio, fecha_fin, estado)
    elif tipo == "inventario":
        datos = crud.get_reporte_inventario(db)
    elif tipo == "clientes":
        datos = crud.get_reporte_clientes(db)
    else:
        datos = []

    return {"tipo": tipo, "total": len(datos), "datos": datos}

