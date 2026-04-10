"""
Operaciones CRUD para pedidos.
"""
from typing import Optional, List
from decimal import Decimal
from sqlalchemy.orm import Session, joinedload

from app.models.pedidos import Pedido, PedidoProducto, EstadoPedido
from app.models.productos import Producto


def _generar_codigo_pedido(db: Session) -> str:
    """Genera el siguiente código de pedido secuencial (ORD-XXXX)."""
    from sqlalchemy import func
    # Buscar el número más alto entre todos los códigos existentes
    todos = db.query(Pedido.codigo_pedido).all()
    max_num = 0
    for (codigo,) in todos:
        if codigo and "-" in codigo:
            try:
                num = int(codigo.split("-")[1])
                if num > max_num:
                    max_num = num
            except (ValueError, IndexError):
                pass
    return f"ORD-{max_num + 1:04d}"


def get_pedidos(
    db: Session,
    filtro_estado: Optional[str] = None,
    id_usuario: Optional[int] = None,
    fecha_inicio: Optional[str] = None,
    fecha_fin: Optional[str] = None,
    mes: Optional[int] = None,
    anio: Optional[int] = None,
    orden: Optional[str] = None,
    skip: int = 0,
    limit: int = 100,
) -> tuple[List[Pedido], int]:
    """Obtener lista de pedidos con filtros avanzados y paginación. Retorna (lista, total)."""
    from sqlalchemy import func, extract

    query = db.query(Pedido).options(
        joinedload(Pedido.productos),
        joinedload(Pedido.usuario),
    )

    if filtro_estado:
        query = query.filter(Pedido.estado_pedido == filtro_estado)
    if id_usuario:
        query = query.filter(Pedido.id_usuario == id_usuario)

    # Filtros de fecha
    if fecha_inicio:
        query = query.filter(Pedido.fecha_pedido >= fecha_inicio)
    if fecha_fin:
        query = query.filter(Pedido.fecha_pedido <= f"{fecha_fin} 23:59:59")
    if mes:
        query = query.filter(extract('month', Pedido.fecha_pedido) == mes)
    if anio:
        query = query.filter(extract('year', Pedido.fecha_pedido) == anio)

    total = query.count()

    # Ordenamiento
    if orden == "total_asc":
        query = query.order_by(Pedido.total.asc())
    elif orden == "total_desc":
        query = query.order_by(Pedido.total.desc())
    elif orden == "fecha_asc":
        query = query.order_by(Pedido.fecha_pedido.asc())
    else:
        # Default: más recientes primero
        query = query.order_by(Pedido.fecha_pedido.desc())

    pedidos = query.offset(skip).limit(limit).all()
    return pedidos, total


def get_pedido_by_id(db: Session, pedido_id: int) -> Optional[Pedido]:
    """Obtener pedido por ID con productos cargados."""
    from app.models.direcciones import Direccion
    return (
        db.query(Pedido)
        .options(
            joinedload(Pedido.productos).joinedload(PedidoProducto.producto),
            joinedload(Pedido.usuario),
            joinedload(Pedido.direccion_envio).joinedload(Direccion.calle),
            joinedload(Pedido.direccion_envio).joinedload(Direccion.numero_vivienda),
            joinedload(Pedido.direccion_envio).joinedload(Direccion.codigo_postal),
            joinedload(Pedido.direccion_envio).joinedload(Direccion.municipio),
            joinedload(Pedido.direccion_envio).joinedload(Direccion.estado),
        )
        .filter(Pedido.id == pedido_id)
        .first()
    )


def get_pedido_by_codigo(db: Session, codigo: str) -> Optional[Pedido]:
    """Obtener pedido por su código (ORD-XXXX)."""
    return (
        db.query(Pedido)
        .options(joinedload(Pedido.productos).joinedload(PedidoProducto.producto))
        .filter(Pedido.codigo_pedido == codigo.upper())
        .first()
    )


def create_pedido(db: Session, id_usuario: int, id_direccion_envio: int, items: list) -> Pedido:
    """
    Crear un nuevo pedido con sus productos.
    items: lista de dicts con {id_producto, cantidad, precio_unitario}
    Valida stock disponible y descuenta inventario automáticamente.
    """
    from app.data.productos import get_producto_by_id, ajustar_stock

    # Validar stock disponible antes de crear el pedido
    for item in items:
        producto = get_producto_by_id(db, item["id_producto"])
        if not producto:
            raise ValueError(f"Producto con ID {item['id_producto']} no encontrado")
        if producto.cantidad < item["cantidad"]:
            raise ValueError(
                f"Stock insuficiente para '{producto.nombre}'. "
                f"Disponible: {producto.cantidad}, Solicitado: {item['cantidad']}"
            )

    # Calcular totales
    subtotal = sum(Decimal(str(i["precio_unitario"])) * i["cantidad"] for i in items)
    impuestos = subtotal * Decimal("0.16")  # IVA 16%
    total = subtotal + impuestos

    pedido = Pedido(
        codigo_pedido=_generar_codigo_pedido(db),
        id_usuario=id_usuario,
        id_direccion_envio=id_direccion_envio,
        estado_pedido=EstadoPedido.pendiente,
        subtotal=subtotal,
        impuestos=impuestos,
        total=total,
    )
    db.add(pedido)
    db.flush()  # Para obtener el ID antes del commit

    # Agregar productos al pedido y descontar stock
    for item in items:
        pp = PedidoProducto(
            id_pedido=pedido.id,
            id_producto=item["id_producto"],
            cantidad=item["cantidad"],
            precio_unitario=item["precio_unitario"],
        )
        db.add(pp)

        # Descontar stock del producto
        producto = get_producto_by_id(db, item["id_producto"])
        ajustar_stock(db, producto, item["cantidad"], "salida")

    db.commit()
    db.refresh(pedido)
    return pedido


def actualizar_estado_pedido(db: Session, pedido: Pedido, nuevo_estado: str) -> Pedido:
    """Actualizar el estado de un pedido. Restaura stock si se cancela."""
    from app.data.productos import get_producto_by_id, ajustar_stock

    estado_actual = pedido.estado_pedido.value if hasattr(pedido.estado_pedido, 'value') else pedido.estado_pedido

    # Si se cancela un pedido activo, restaurar el stock
    if nuevo_estado == "cancelado" and estado_actual != "cancelado":
        for pp in pedido.productos:
            producto = get_producto_by_id(db, pp.id_producto)
            if producto:
                ajustar_stock(db, producto, pp.cantidad, "entrada")

    pedido.estado_pedido = nuevo_estado
    db.commit()
    db.refresh(pedido)
    return pedido


def get_kpis(db: Session, fecha_inicio=None, fecha_fin=None) -> dict:
    """Obtener KPIs generales para reportes, con filtro de fechas opcional."""
    from app.models.usuarios import Usuario
    from sqlalchemy import func

    query_ventas = db.query(func.sum(Pedido.total)).filter(
        Pedido.estado_pedido != EstadoPedido.cancelado
    )
    query_pedidos = db.query(Pedido)
    if fecha_inicio:
        query_ventas = query_ventas.filter(Pedido.fecha_pedido >= fecha_inicio)
        query_pedidos = query_pedidos.filter(Pedido.fecha_pedido >= fecha_inicio)
    if fecha_fin:
        query_ventas = query_ventas.filter(Pedido.fecha_pedido <= f"{fecha_fin} 23:59:59")
        query_pedidos = query_pedidos.filter(Pedido.fecha_pedido <= f"{fecha_fin} 23:59:59")

    total_ventas = query_ventas.scalar() or 0
    total_pedidos = query_pedidos.count()

    # Pedidos por estado
    pendientes = db.query(Pedido).filter(Pedido.estado_pedido == EstadoPedido.pendiente).count()
    entregados = db.query(Pedido).filter(Pedido.estado_pedido == EstadoPedido.entregado).count()
    cancelados = db.query(Pedido).filter(Pedido.estado_pedido == EstadoPedido.cancelado).count()

    total_clientes = db.query(Usuario).filter(Usuario.status == "usuario").count()

    # Productos totales en inventario
    from app.models.productos import Producto
    total_productos = db.query(Producto).count()
    stock_total = db.query(func.sum(Producto.cantidad)).scalar() or 0

    return {
        "ventas": float(total_ventas),
        "pedidos": total_pedidos,
        "clientes": total_clientes,
        "pendientes": pendientes,
        "entregados": entregados,
        "cancelados": cancelados,
        "productos": total_productos,
        "stock_total": int(stock_total),
    }


def get_reporte_ventas(db: Session, fecha_inicio=None, fecha_fin=None, marca_id=None, top_n=None) -> list:
    """Generar datos de reporte de ventas (cada pedido no cancelado)."""
    from app.models.productos import Producto

    query = db.query(Pedido).options(
        joinedload(Pedido.productos).joinedload(PedidoProducto.producto),
        joinedload(Pedido.usuario),
    ).filter(Pedido.estado_pedido != EstadoPedido.cancelado)

    if fecha_inicio:
        query = query.filter(Pedido.fecha_pedido >= fecha_inicio)
    if fecha_fin:
        query = query.filter(Pedido.fecha_pedido <= f"{fecha_fin} 23:59:59")

    # Si filtran por marca, solo pedidos que contengan productos de esa marca
    if marca_id:
        query = query.filter(
            Pedido.productos.any(
                PedidoProducto.producto.has(Producto.id_marca == int(marca_id))
            )
        )

    # Ordenar por total descendente para top
    query = query.order_by(Pedido.total.desc())

    pedidos = query.all()
    resultado = []
    for p in pedidos:
        estado = p.estado_pedido.value if hasattr(p.estado_pedido, 'value') else p.estado_pedido
        num_arts = sum(pp.cantidad for pp in p.productos) if p.productos else 0
        nombre = f"{p.usuario.nombre} {p.usuario.apellidos}" if p.usuario else f"Usuario #{p.id_usuario}"
        resultado.append({
            "codigo": p.codigo_pedido,
            "fecha": p.fecha_pedido.strftime("%Y-%m-%d") if p.fecha_pedido else "",
            "cliente": nombre,
            "articulos": num_arts,
            "subtotal": float(p.subtotal),
            "impuestos": float(p.impuestos),
            "total": float(p.total),
            "estado": estado,
        })

    # Aplicar top N
    if top_n:
        resultado = resultado[:int(top_n)]

    return resultado


def get_reporte_pedidos(db: Session, fecha_inicio=None, fecha_fin=None, estado=None, ordenar=None) -> list:
    """Reporte de todos los pedidos (incluye cancelados) con opción de filtro por estado."""
    query = db.query(Pedido).options(
        joinedload(Pedido.productos),
        joinedload(Pedido.usuario),
    )
    if fecha_inicio:
        query = query.filter(Pedido.fecha_pedido >= fecha_inicio)
    if fecha_fin:
        query = query.filter(Pedido.fecha_pedido <= f"{fecha_fin} 23:59:59")
    if estado:
        query = query.filter(Pedido.estado_pedido == estado)

    pedidos = query.order_by(Pedido.fecha_pedido.desc()).all()
    resultado = []
    for p in pedidos:
        est = p.estado_pedido.value if hasattr(p.estado_pedido, 'value') else p.estado_pedido
        num_arts = sum(pp.cantidad for pp in p.productos) if p.productos else 0
        nombre = f"{p.usuario.nombre} {p.usuario.apellidos}" if p.usuario else f"Usuario #{p.id_usuario}"
        resultado.append({
            "codigo": p.codigo_pedido,
            "fecha": p.fecha_pedido.strftime("%Y-%m-%d") if p.fecha_pedido else "",
            "cliente": nombre,
            "articulos": num_arts,
            "total": float(p.total),
            "estado": est,
        })

    if ordenar == "arts_desc":
        resultado.sort(key=lambda x: x["articulos"], reverse=True)
    elif ordenar == "arts_asc":
        resultado.sort(key=lambda x: x["articulos"])

    return resultado


def get_reporte_inventario(db: Session, marca_id: int = None, tipo_id: int = None,
                           ordenar: str = None, precio_min: float = None,
                           precio_max: float = None) -> list:
    """Reporte del inventario actual de productos."""
    from app.models.productos import Producto, TipoAutoparte, Marca
    query = db.query(Producto).options(
        joinedload(Producto.tipo_autoparte),
        joinedload(Producto.marca),
    )

    if marca_id:
        query = query.filter(Producto.id_marca == marca_id)
    if tipo_id:
        query = query.filter(Producto.id_tipo_autoparte == tipo_id)
    if precio_min is not None:
        query = query.filter(Producto.precio >= precio_min)
    if precio_max is not None:
        query = query.filter(Producto.precio <= precio_max)

    # Ordenamiento
    if ordenar == "mas_vendido":
        from sqlalchemy import func
        from app.models.pedidos import DetallePedido
        sub = db.query(
            DetallePedido.id_producto,
            func.sum(DetallePedido.cantidad).label("total_vendido")
        ).group_by(DetallePedido.id_producto).subquery()
        query = query.outerjoin(sub, Producto.id == sub.c.id_producto).order_by(sub.c.total_vendido.desc().nullslast())
    elif ordenar == "precio_asc":
        query = query.order_by(Producto.precio.asc())
    elif ordenar == "precio_desc":
        query = query.order_by(Producto.precio.desc())
    elif ordenar == "alfabetico":
        query = query.order_by(Producto.nombre.asc())
    else:
        query = query.order_by(Producto.nombre)

    productos = query.all()

    resultado = []
    for p in productos:
        tipo = p.tipo_autoparte.nombre if p.tipo_autoparte else "—"
        marca = p.marca.nombre if p.marca else "—"
        resultado.append({
            "codigo": p.codigo,
            "nombre": p.nombre,
            "tipo": tipo,
            "marca": marca,
            "precio": float(p.precio),
            "stock": p.cantidad,
        })
    return resultado


def get_reporte_clientes(db: Session, ordenar: str = None) -> list:
    """Reporte de clientes registrados con su número de pedidos y gasto total."""
    from app.models.usuarios import Usuario
    from sqlalchemy import func

    # Subquery para conteo de pedidos y total por usuario
    clientes = db.query(Usuario).filter(Usuario.status == "usuario").all()
    resultado = []
    for c in clientes:
        num_pedidos = db.query(Pedido).filter(Pedido.id_usuario == c.id).count()
        gasto = db.query(func.sum(Pedido.total)).filter(
            Pedido.id_usuario == c.id,
            Pedido.estado_pedido != EstadoPedido.cancelado,
        ).scalar() or 0

        resultado.append({
            "nombre": f"{c.nombre} {c.apellidos}",
            "email": c.email,
            "pedidos": num_pedidos,
            "gasto_total": float(gasto),
        })

    if ordenar == "mas_pedidos":
        resultado.sort(key=lambda x: x["pedidos"], reverse=True)
    elif ordenar == "mayor_gasto":
        resultado.sort(key=lambda x: x["gasto_total"], reverse=True)
    elif ordenar == "alfabetico":
        resultado.sort(key=lambda x: x["nombre"])
    elif ordenar == "recientes":
        resultado.reverse()

    return resultado

