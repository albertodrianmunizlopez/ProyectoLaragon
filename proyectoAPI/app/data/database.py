"""
Configuración de la conexión a PostgreSQL con SQLAlchemy.
"""
import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

# URL de conexión — viene del docker-compose.yml como variable de entorno
DATABASE_URL = os.getenv(
    "DATABASE_URL",
    "postgresql://macuin_user:macuin_pass_2026@postgres:5432/macuin_db"
)

engine = create_engine(DATABASE_URL)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

Base = declarative_base()


def get_db():
    """
    Dependency de FastAPI.
    Crea una sesión de BD por request y la cierra al terminar.
    """
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
