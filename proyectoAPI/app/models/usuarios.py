"""
Modelo SQLAlchemy para la tabla de usuarios.
"""
import enum
from sqlalchemy import Column, Integer, String, Boolean, ForeignKey, Enum, DateTime
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.data.database import Base


class EstatusUsuario(str, enum.Enum):
    administrador = "administrador"
    superadministrador = "superadministrador"
    usuario = "usuario"


class Usuario(Base):
    __tablename__ = "usuarios"

    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(100), nullable=False)
    apellidos = Column(String(100), nullable=False)
    email = Column(String(150), unique=True, nullable=False, index=True)
    password_hash = Column(String(255), nullable=False)
    password_plain = Column(String(255), nullable=True)
    telefono = Column(String(10), nullable=True)
    id_direccion = Column(Integer, ForeignKey("direcciones.id"), nullable=True)
    status = Column(
        Enum(EstatusUsuario, name="estatus_usuario", create_type=False),
        nullable=False,
        default=EstatusUsuario.usuario
    )
    activo = Column(Boolean, nullable=False, default=True)
    created_at = Column(DateTime, nullable=False, server_default=func.now())
    updated_at = Column(DateTime, nullable=False, server_default=func.now(), onupdate=func.now())

    # Relaciones
    direccion = relationship("Direccion", back_populates="usuarios")
    pedidos = relationship("Pedido", back_populates="usuario")
