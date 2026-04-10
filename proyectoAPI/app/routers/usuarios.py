"""
Router de usuarios — endpoints CRUD + autenticación.
"""
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List

from app.data.database import get_db
from app.data import usuarios as crud
from app.schemas.usuarios import (
    UsuarioCreate, UsuarioUpdate, UsuarioResponse, UsuarioAdminResponse,
    UsuarioLoginRequest, TokenResponse, CambiarRolRequest,
)
from app.security.auth import (
    verify_password, create_access_token,
    get_current_user, require_admin,
)
from app.models.usuarios import Usuario

router = APIRouter(prefix="/api", tags=["Usuarios"])


# ── Autenticación ──────────────────────────────────────────

@router.post("/auth/login", response_model=TokenResponse)
def login(datos: UsuarioLoginRequest, db: Session = Depends(get_db)):
    """Iniciar sesión y obtener token JWT."""
    usuario = crud.get_usuario_by_email(db, datos.email)
    pwd = datos.password.get_secret_value()
    if not usuario or not verify_password(pwd, usuario.password_hash):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Credenciales incorrectas",
        )
    if not usuario.activo:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Cuenta desactivada",
        )
    token = create_access_token(data={"sub": usuario.id, "email": usuario.email})
    return TokenResponse(access_token=token, usuario=UsuarioResponse.model_validate(usuario))


@router.post("/auth/register", response_model=UsuarioResponse, status_code=201)
def register(datos: UsuarioCreate, db: Session = Depends(get_db)):
    """Registrar un nuevo usuario."""
    if crud.get_usuario_by_email(db, datos.email):
        raise HTTPException(
            status_code=status.HTTP_409_CONFLICT,
            detail="Ya existe un usuario con ese email",
        )
    usuario = crud.create_usuario(db, datos.model_dump())
    return usuario


@router.get("/auth/check-email")
def check_email(email: str, db: Session = Depends(get_db)):
    """Verificar si un email ya está registrado (endpoint público)."""
    exists = crud.get_usuario_by_email(db, email) is not None
    return {"email": email, "exists": exists}


# ── Perfil del usuario autenticado ─────────────────────────

@router.get("/usuarios/me", response_model=UsuarioResponse)
def mi_perfil(current_user: Usuario = Depends(get_current_user)):
    """Obtener datos del usuario autenticado."""
    return current_user


@router.get("/usuarios/me/direccion")
def mi_direccion(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Obtener la dirección completa del usuario autenticado."""
    from app.data import direcciones as dir_crud
    if not current_user.id_direccion:
        return {"tiene_direccion": False, "direccion": None}
    direccion = dir_crud.get_direccion_by_id(db, current_user.id_direccion)
    if not direccion:
        return {"tiene_direccion": False, "direccion": None}
    return {
        "tiene_direccion": True,
        "direccion": {
            "id": direccion.id,
            "calle": direccion.calle.nombre if direccion.calle else "",
            "numero": direccion.numero_vivienda.numero if direccion.numero_vivienda else "",
            "codigo_postal": direccion.codigo_postal.codigo if direccion.codigo_postal else "",
            "municipio": direccion.municipio.nombre if direccion.municipio else "",
            "estado": direccion.estado.nombre if direccion.estado else "",
        },
    }


@router.post("/usuarios/me/direccion")
def crear_mi_direccion(
    datos: dict,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """
    Crear una dirección y asignarla al usuario autenticado.
    Espera: calle, numero, codigo_postal, municipio, estado (nombres, no IDs).
    Crea los registros en los catálogos si no existen.
    """
    from app.data import direcciones as dir_crud
    from app.models.direcciones import Estado, Municipio, CodigoPostal, Calle, NumeroVivienda, Direccion

    # Buscar o crear Estado
    estado_obj = db.query(Estado).filter(Estado.nombre == datos.get("estado", "")).first()
    if not estado_obj:
        estado_obj = Estado(nombre=datos.get("estado", "Sin estado"))
        db.add(estado_obj)
        db.flush()

    # Buscar o crear Municipio
    municipio_obj = db.query(Municipio).filter(
        Municipio.nombre == datos.get("municipio", ""),
        Municipio.id_estado == estado_obj.id,
    ).first()
    if not municipio_obj:
        municipio_obj = Municipio(nombre=datos.get("municipio", "Sin municipio"), id_estado=estado_obj.id)
        db.add(municipio_obj)
        db.flush()

    # Buscar o crear CP
    cp_obj = db.query(CodigoPostal).filter(CodigoPostal.codigo == datos.get("codigo_postal", "")).first()
    if not cp_obj:
        cp_obj = CodigoPostal(codigo=datos.get("codigo_postal", "00000"))
        db.add(cp_obj)
        db.flush()

    # Buscar o crear Calle
    calle_obj = db.query(Calle).filter(Calle.nombre == datos.get("calle", "")).first()
    if not calle_obj:
        calle_obj = Calle(nombre=datos.get("calle", "Sin calle"))
        db.add(calle_obj)
        db.flush()

    # Buscar o crear Número
    num_obj = db.query(NumeroVivienda).filter(NumeroVivienda.numero == datos.get("numero", "")).first()
    if not num_obj:
        num_obj = NumeroVivienda(numero=datos.get("numero", "S/N"))
        db.add(num_obj)
        db.flush()

    # Crear Dirección
    direccion = Direccion(
        id_calle=calle_obj.id,
        id_numero_vivienda=num_obj.id,
        id_codigo_postal=cp_obj.id,
        id_municipio=municipio_obj.id,
        id_estado=estado_obj.id,
    )
    db.add(direccion)
    db.flush()

    # Asignar al usuario
    current_user.id_direccion = direccion.id
    db.commit()

    return {
        "message": "Dirección creada y asignada exitosamente",
        "id_direccion": direccion.id,
    }


# ── CRUD de Usuarios ───────────────────────────────────────

@router.get("/usuarios", response_model=List[UsuarioResponse])
def listar_usuarios(
    filtro: str = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Listar usuarios (solo admins). Filtro opcional: 'administrador', 'usuario'."""
    return crud.get_usuarios(db, filtro_status=filtro, skip=skip, limit=limit)


@router.get("/usuarios/stats")
def stats_usuarios(
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Estadísticas de usuarios (conteo por rol/estado)."""
    return crud.contar_usuarios(db)


@router.get("/usuarios/{usuario_id}", response_model=UsuarioResponse)
def obtener_usuario(
    usuario_id: int,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Obtener un usuario por ID."""
    usuario = crud.get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return usuario


@router.get("/usuarios/{usuario_id}/admin", response_model=UsuarioAdminResponse)
def obtener_usuario_admin(
    usuario_id: int,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Obtener un usuario con contraseña visible (solo admins)."""
    usuario = crud.get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return usuario


@router.post("/usuarios", response_model=UsuarioResponse, status_code=201)
def crear_usuario(
    datos: UsuarioCreate,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Crear un usuario (solo admins)."""
    if crud.get_usuario_by_email(db, datos.email):
        raise HTTPException(status_code=409, detail="Email ya registrado")
    return crud.create_usuario(db, datos.model_dump())


@router.put("/usuarios/{usuario_id}", response_model=UsuarioResponse)
def actualizar_usuario(
    usuario_id: int,
    datos: UsuarioUpdate,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Actualizar un usuario (solo admins)."""
    usuario = crud.get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return crud.update_usuario(db, usuario, datos.model_dump(exclude_unset=True))


@router.patch("/usuarios/{usuario_id}/toggle-estado", response_model=UsuarioResponse)
def toggle_estado(
    usuario_id: int,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Activar/desactivar un usuario."""
    usuario = crud.get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return crud.toggle_estado_usuario(db, usuario)


@router.patch("/usuarios/{usuario_id}/cambiar-rol", response_model=UsuarioResponse)
def cambiar_rol(
    usuario_id: int,
    datos: CambiarRolRequest,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Cambiar el rol de un usuario."""
    usuario = crud.get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    if datos.nuevo_rol not in ("administrador", "superadministrador", "usuario"):
        raise HTTPException(status_code=400, detail="Rol inválido")
    return crud.cambiar_rol_usuario(db, usuario, datos.nuevo_rol)


@router.delete("/usuarios/{usuario_id}", status_code=204)
def eliminar_usuario(
    usuario_id: int,
    db: Session = Depends(get_db),
    _admin: Usuario = Depends(require_admin),
):
    """Eliminar un usuario (solo admins)."""
    usuario = crud.get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    crud.delete_usuario(db, usuario)
