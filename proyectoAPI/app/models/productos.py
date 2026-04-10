"""
Modelo SQLAlchemy para la tabla de productos (inventario).
Incluye modelos para catálogos: TipoAutoparte y Marca.
"""
import enum
from sqlalchemy import Column, Integer, String, Text, Numeric, Enum, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.data.database import Base


class EstatusProducto(str, enum.Enum):
    en_stock = "en_stock"
    agotado = "agotado"
    descontinuado = "descontinuado"


class TipoAutoparte(Base):
    __tablename__ = "tipos_autoparte"

    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(100), unique=True, nullable=False)

    # Relación inversa
    productos = relationship("Producto", back_populates="tipo_autoparte")


class Marca(Base):
    __tablename__ = "marcas"

    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(100), unique=True, nullable=False)

    # Relación inversa
    productos = relationship("Producto", back_populates="marca")


class Producto(Base):
    __tablename__ = "productos"

    id = Column(Integer, primary_key=True, index=True)
    codigo = Column(String(50), unique=True, nullable=False, index=True)
    nombre = Column(String(200), nullable=False)
    descripcion = Column(Text, nullable=True)
    imagen_url = Column(Text, nullable=True)
    id_tipo_autoparte = Column(Integer, ForeignKey("tipos_autoparte.id"), nullable=True)
    id_marca = Column(Integer, ForeignKey("marcas.id"), nullable=True)
    cantidad = Column(Integer, nullable=False, default=0)
    estatus_producto = Column(
        Enum(EstatusProducto, name="estatus_producto", create_type=False),
        nullable=False,
        default=EstatusProducto.en_stock
    )
    precio = Column(Numeric(12, 2), nullable=False, default=0.00)
    created_at = Column(DateTime, nullable=False, server_default=func.now())
    updated_at = Column(DateTime, nullable=False, server_default=func.now(), onupdate=func.now())

    # Relaciones
    tipo_autoparte = relationship("TipoAutoparte", back_populates="productos")
    marca = relationship("Marca", back_populates="productos")
    pedido_productos = relationship("PedidoProducto", back_populates="producto")
