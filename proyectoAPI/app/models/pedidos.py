"""
Modelos SQLAlchemy para las tablas de pedidos y pedido_productos.
"""
import enum
from sqlalchemy import Column, Integer, String, Numeric, ForeignKey, Enum, DateTime, UniqueConstraint
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.data.database import Base


class EstadoPedido(str, enum.Enum):
    pendiente = "pendiente"
    surtido = "surtido"
    enviado = "enviado"
    en_camino = "en_camino"
    entregado = "entregado"
    cancelado = "cancelado"


class Pedido(Base):
    __tablename__ = "pedidos"

    id = Column(Integer, primary_key=True, index=True)
    codigo_pedido = Column(String(20), unique=True, nullable=False)
    id_usuario = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    id_direccion_envio = Column(Integer, ForeignKey("direcciones.id"), nullable=False)
    estado_pedido = Column(
        Enum(EstadoPedido, name="estado_pedido", create_type=False),
        nullable=False,
        default=EstadoPedido.pendiente
    )
    subtotal = Column(Numeric(12, 2), nullable=False, default=0.00)
    impuestos = Column(Numeric(12, 2), nullable=False, default=0.00)
    total = Column(Numeric(12, 2), nullable=False, default=0.00)
    fecha_pedido = Column(DateTime, nullable=False, server_default=func.now())
    updated_at = Column(DateTime, nullable=False, server_default=func.now(), onupdate=func.now())

    # Relaciones
    usuario = relationship("Usuario", back_populates="pedidos")
    direccion_envio = relationship("Direccion", back_populates="pedidos")
    productos = relationship("PedidoProducto", back_populates="pedido", cascade="all, delete-orphan")


class PedidoProducto(Base):
    __tablename__ = "pedido_productos"

    id = Column(Integer, primary_key=True, index=True)
    id_pedido = Column(Integer, ForeignKey("pedidos.id", ondelete="CASCADE"), nullable=False)
    id_producto = Column(Integer, ForeignKey("productos.id"), nullable=False)
    cantidad = Column(Integer, nullable=False)
    precio_unitario = Column(Numeric(12, 2), nullable=False)

    __table_args__ = (
        UniqueConstraint("id_pedido", "id_producto", name="uq_pedido_producto"),
    )

    # Relaciones
    pedido = relationship("Pedido", back_populates="productos")
    producto = relationship("Producto", back_populates="pedido_productos")
