"""
Schemas Pydantic para usuarios.
Usa SecretStr para proteger contraseñas en logs, repr y serialización automática.
"""
from pydantic import BaseModel, EmailStr, SecretStr, field_validator
import re
from typing import Optional
from datetime import datetime


class UsuarioCreate(BaseModel):
    nombre: str
    apellidos: str
    email: EmailStr
    password: SecretStr
    telefono: Optional[str] = None
    id_direccion: Optional[int] = None
    status: str = "usuario"

    @field_validator("nombre", "apellidos")
    @classmethod
    def solo_letras(cls, v: str) -> str:
        if not re.match(r'^[A-Za-záéíóúñÁÉÍÓÚÑüÜ\s]+$', v.strip()):
            raise ValueError("Solo se permiten letras y espacios")
        return v.strip()

    @field_validator("telefono")
    @classmethod
    def telefono_solo_10_digitos(cls, v: Optional[str]) -> Optional[str]:
        if v is not None:
            v = v.strip()
            if v and not re.match(r'^\d{10}$', v):
                raise ValueError("El teléfono debe ser exactamente 10 dígitos numéricos")
        return v or None

    @field_validator("password")
    @classmethod
    def password_min_length(cls, v: SecretStr) -> SecretStr:
        if len(v.get_secret_value()) < 6:
            raise ValueError("La contraseña debe tener al menos 6 caracteres")
        return v


class UsuarioUpdate(BaseModel):
    nombre: Optional[str] = None
    apellidos: Optional[str] = None
    email: Optional[EmailStr] = None
    password: Optional[SecretStr] = None
    telefono: Optional[str] = None
    id_direccion: Optional[int] = None
    status: Optional[str] = None
    activo: Optional[bool] = None

    @field_validator("telefono")
    @classmethod
    def telefono_solo_10_digitos(cls, v: Optional[str]) -> Optional[str]:
        if v is not None:
            v = v.strip()
            if v and not re.match(r'^\d{10}$', v):
                raise ValueError("El teléfono debe ser exactamente 10 dígitos numéricos")
        return v or None

    @field_validator("password")
    @classmethod
    def password_min_length(cls, v: Optional[SecretStr]) -> Optional[SecretStr]:
        if v is not None and len(v.get_secret_value()) < 6:
            raise ValueError("La contraseña debe tener al menos 6 caracteres")
        return v


class UsuarioResponse(BaseModel):
    """Respuesta pública — NO expone contraseñas."""
    id: int
    nombre: str
    apellidos: str
    email: str
    telefono: Optional[str] = None
    id_direccion: Optional[int] = None
    status: str
    activo: bool
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True


class UsuarioAdminResponse(UsuarioResponse):
    """Respuesta para superadmins — expone la contraseña en texto plano."""
    password_plain: Optional[str] = None

    class Config:
        from_attributes = True


class UsuarioLoginRequest(BaseModel):
    email: EmailStr
    password: SecretStr


class TokenResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    usuario: UsuarioResponse


class CambiarRolRequest(BaseModel):
    nuevo_rol: str
