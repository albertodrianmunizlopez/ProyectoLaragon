"""
Operaciones CRUD para usuarios.
"""
from typing import Optional, List
from sqlalchemy.orm import Session

from app.models.usuarios import Usuario, EstatusUsuario
from app.security.auth import hash_password


def get_usuarios(
    db: Session,
    filtro_status: Optional[str] = None,
    busqueda: Optional[str] = None,
    skip: int = 0,
    limit: int = 100,
) -> List[Usuario]:
    """Obtener lista de usuarios con filtro opcional por status y búsqueda por nombre/email."""
    query = db.query(Usuario)
    if filtro_status:
        query = query.filter(Usuario.status == filtro_status)
    if busqueda:
        term = f"%{busqueda}%"
        query = query.filter(
            (Usuario.nombre.ilike(term)) |
            (Usuario.apellidos.ilike(term)) |
            (Usuario.email.ilike(term))
        )
    return query.offset(skip).limit(limit).all()


def get_usuario_by_id(db: Session, usuario_id: int) -> Optional[Usuario]:
    """Obtener un usuario por su ID."""
    return db.query(Usuario).filter(Usuario.id == usuario_id).first()


def get_usuario_by_email(db: Session, email: str) -> Optional[Usuario]:
    """Obtener un usuario por su email."""
    return db.query(Usuario).filter(Usuario.email == email).first()


def create_usuario(db: Session, datos: dict) -> Usuario:
    """Crear un nuevo usuario. El password se hashea y se guarda en texto plano."""
    password = datos.pop("password", None)
    usuario = Usuario(**datos)
    if password:
        # Si viene como SecretStr, extraer el valor
        plain = password.get_secret_value() if hasattr(password, 'get_secret_value') else str(password)
        usuario.password_hash = hash_password(plain)
        usuario.password_plain = plain
    db.add(usuario)
    db.commit()
    db.refresh(usuario)
    return usuario


def update_usuario(db: Session, usuario: Usuario, datos: dict) -> Usuario:
    """Actualizar campos de un usuario existente."""
    password = datos.pop("password", None)
    for campo, valor in datos.items():
        if valor is not None and hasattr(usuario, campo):
            setattr(usuario, campo, valor)
    if password:
        plain = password.get_secret_value() if hasattr(password, 'get_secret_value') else str(password)
        usuario.password_hash = hash_password(plain)
        usuario.password_plain = plain
    db.commit()
    db.refresh(usuario)
    return usuario


def delete_usuario(db: Session, usuario: Usuario) -> None:
    """Eliminar un usuario de la base de datos."""
    db.delete(usuario)
    db.commit()


def toggle_estado_usuario(db: Session, usuario: Usuario) -> Usuario:
    """Activar/desactivar un usuario."""
    usuario.activo = not usuario.activo
    db.commit()
    db.refresh(usuario)
    return usuario


def cambiar_rol_usuario(db: Session, usuario: Usuario, nuevo_rol: str) -> Usuario:
    """Cambiar el rol/status de un usuario."""
    usuario.status = nuevo_rol
    db.commit()
    db.refresh(usuario)
    return usuario


def contar_usuarios(db: Session) -> dict:
    """Obtener conteo de usuarios por status."""
    total = db.query(Usuario).count()
    superadmins = db.query(Usuario).filter(
        Usuario.status == EstatusUsuario.superadministrador
    ).count()
    admins = db.query(Usuario).filter(
        Usuario.status == EstatusUsuario.administrador
    ).count()
    usuarios = db.query(Usuario).filter(Usuario.status == EstatusUsuario.usuario).count()
    activos = db.query(Usuario).filter(Usuario.activo == True).count()
    return {
        "total": total, "superadmins": superadmins, "admins": admins,
        "usuarios": usuarios, "activos": activos,
    }
