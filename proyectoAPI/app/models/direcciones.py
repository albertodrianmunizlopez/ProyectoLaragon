"""
Modelos SQLAlchemy para las tablas de dirección y catálogos.
Mapea: estados, municipios, codigos_postales, calles, numeros_vivienda, direcciones.
"""
from sqlalchemy import Column, Integer, String, ForeignKey
from sqlalchemy.orm import relationship
from app.data.database import Base


class Estado(Base):
    __tablename__ = "estados"

    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(100), unique=True, nullable=False)

    # Relaciones
    municipios = relationship("Municipio", back_populates="estado")
    direcciones = relationship("Direccion", back_populates="estado")


class Municipio(Base):
    __tablename__ = "municipios"

    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    id_estado = Column(Integer, ForeignKey("estados.id"), nullable=False)

    # Relaciones
    estado = relationship("Estado", back_populates="municipios")
    direcciones = relationship("Direccion", back_populates="municipio")


class CodigoPostal(Base):
    __tablename__ = "codigos_postales"

    id = Column(Integer, primary_key=True, index=True)
    codigo = Column(String(10), unique=True, nullable=False)

    # Relaciones
    direcciones = relationship("Direccion", back_populates="codigo_postal")


class Calle(Base):
    __tablename__ = "calles"

    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(200), nullable=False)

    # Relaciones
    direcciones = relationship("Direccion", back_populates="calle")


class NumeroVivienda(Base):
    __tablename__ = "numeros_vivienda"

    id = Column(Integer, primary_key=True, index=True)
    numero = Column(String(20), nullable=False)

    # Relaciones
    direcciones = relationship("Direccion", back_populates="numero_vivienda")


class Direccion(Base):
    __tablename__ = "direcciones"

    id = Column(Integer, primary_key=True, index=True)
    id_calle = Column(Integer, ForeignKey("calles.id"), nullable=False)
    id_numero_vivienda = Column(Integer, ForeignKey("numeros_vivienda.id"), nullable=False)
    id_codigo_postal = Column(Integer, ForeignKey("codigos_postales.id"), nullable=False)
    id_municipio = Column(Integer, ForeignKey("municipios.id"), nullable=False)
    id_estado = Column(Integer, ForeignKey("estados.id"), nullable=False)

    # Relaciones
    calle = relationship("Calle", back_populates="direcciones")
    numero_vivienda = relationship("NumeroVivienda", back_populates="direcciones")
    codigo_postal = relationship("CodigoPostal", back_populates="direcciones")
    municipio = relationship("Municipio", back_populates="direcciones")
    estado = relationship("Estado", back_populates="direcciones")

    # Relaciones inversas
    usuarios = relationship("Usuario", back_populates="direccion")
    pedidos = relationship("Pedido", back_populates="direccion_envio")
