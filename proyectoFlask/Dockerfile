FROM python:3.10-slim

WORKDIR /app

# Instalar dependencias
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copiar todo el codigo fuente del proyecto
COPY . .

# Exponer el puerto
EXPOSE 5000

# Comando para ejecutar la aplicacion
CMD ["python", "app.py"]