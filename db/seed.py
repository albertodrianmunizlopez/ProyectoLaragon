"""
seed.py — Poblar la BD de MACUIN con datos de prueba realistas.
Usa Faker (es_MX) + psycopg2. Ejecutar desde el host:

    pip install faker psycopg2-binary
    python db/seed.py

O desde dentro del contenedor de postgres:
    docker exec -i macuin_postgres python /seed.py  (copia el archivo primero)
"""

import random
import string
from decimal import Decimal, ROUND_HALF_UP
from datetime import datetime, timedelta

import psycopg2
from faker import Faker

# ── Conexión ──────────────────────────────────────────────────────────────────
DB = dict(
    host="localhost",
    port=5433,
    dbname="macuin_db",
    user="macuin_user",
    password="macuin_pass_2026",
)

# ── Configuración de cantidad de datos ───────────────────────────────────────
N_EXTRA_CALLES      = 30
N_EXTRA_NUMEROS     = 40
N_EXTRA_CODIGOS     = 20
N_EXTRA_DIRECCIONES = 50
N_CLIENTES          = 40   # usuarios con rol 'usuario'
N_PRODUCTOS         = 30   # productos adicionales
N_PEDIDOS           = 60   # pedidos adicionales

# ── Bcrypt hash fijo de "macuin2026" ─────────────────────────────────────────
PWD_HASH  = "$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy"
PWD_PLAIN = "macuin2026"

fake = Faker("es_MX")
random.seed(42)

ESTADOS_PEDIDO = ["pendiente", "surtido", "enviado", "en_camino", "entregado", "cancelado"]


def run():
    conn = psycopg2.connect(**DB)
    cur  = conn.cursor()

    print("Conectado a macuin_db. Iniciando seed...")

    # ── 1. Cargar IDs existentes ─────────────────────────────────────────────
    cur.execute("SELECT id FROM estados")
    estados_ids = [r[0] for r in cur.fetchall()]

    cur.execute("SELECT id FROM municipios")
    municipios_ids = [r[0] for r in cur.fetchall()]

    cur.execute("SELECT id FROM tipos_autoparte")
    tipos_ids = [r[0] for r in cur.fetchall()]

    cur.execute("SELECT id FROM marcas")
    marcas_ids = [r[0] for r in cur.fetchall()]

    cur.execute("SELECT nombre FROM marcas")
    marcas_nombre = {r[0]: True for r in cur.fetchall()}

    cur.execute("SELECT id, nombre FROM marcas")
    marcas_map = {r[0]: r[1] for r in cur.fetchall()}

    # ── 2. Calles adicionales ────────────────────────────────────────────────
    print(f"Insertando {N_EXTRA_CALLES} calles...")
    cur.execute("SELECT nombre FROM calles")
    calles_existentes = {r[0] for r in cur.fetchall()}
    prefijos = ["Av.", "Calle", "Blvd.", "Calz.", "Circuito", "Paseo", "Privada"]
    nuevas_calles = set()
    while len(nuevas_calles) < N_EXTRA_CALLES:
        nombre = f"{random.choice(prefijos)} {fake.last_name()}"
        if nombre not in calles_existentes and nombre not in nuevas_calles:
            nuevas_calles.add(nombre)
    for nombre in nuevas_calles:
        cur.execute("INSERT INTO calles (nombre) VALUES (%s)", (nombre,))
    conn.commit()

    cur.execute("SELECT id FROM calles")
    calles_ids = [r[0] for r in cur.fetchall()]

    # ── 3. Números de vivienda adicionales ───────────────────────────────────
    print(f"Insertando {N_EXTRA_NUMEROS} números de vivienda...")
    cur.execute("SELECT numero FROM numeros_vivienda")
    nums_existentes = {r[0] for r in cur.fetchall()}
    nuevos_nums = set()
    letras = ["", "-A", "-B", "-C", "BIS"]
    while len(nuevos_nums) < N_EXTRA_NUMEROS:
        n = f"{random.randint(1, 999)}{random.choice(letras)}"
        if n not in nums_existentes and n not in nuevos_nums:
            nuevos_nums.add(n)
    for n in nuevos_nums:
        cur.execute("INSERT INTO numeros_vivienda (numero) VALUES (%s)", (n,))
    conn.commit()

    cur.execute("SELECT id FROM numeros_vivienda")
    numeros_ids = [r[0] for r in cur.fetchall()]

    # ── 4. Códigos postales adicionales ─────────────────────────────────────
    print(f"Insertando {N_EXTRA_CODIGOS} códigos postales...")
    cur.execute("SELECT codigo FROM codigos_postales")
    cps_existentes = {r[0] for r in cur.fetchall()}
    nuevos_cps = set()
    while len(nuevos_cps) < N_EXTRA_CODIGOS:
        cp = str(random.randint(10000, 99999))
        if cp not in cps_existentes and cp not in nuevos_cps:
            nuevos_cps.add(cp)
    for cp in nuevos_cps:
        cur.execute("INSERT INTO codigos_postales (codigo) VALUES (%s)", (cp,))
    conn.commit()

    cur.execute("SELECT id FROM codigos_postales")
    cps_ids = [r[0] for r in cur.fetchall()]

    # ── 5. Direcciones adicionales ───────────────────────────────────────────
    print(f"Insertando {N_EXTRA_DIRECCIONES} direcciones...")
    colonias = ["Centro", "Del Valle", "Roma Norte", "Polanco", "Coyoacán",
                "Industrial", "Las Flores", "San Pedro", "Jardines", "La Paz",
                "Nueva España", "Reforma", "Satelite", "Los Pinos", "Juárez"]
    localidades = ["", "San Juan", "El Rosario", "Santa Fe", "La Herradura", ""]
    dir_ids_nuevos = []
    for _ in range(N_EXTRA_DIRECCIONES):
        sin_numero = random.random() < 0.1  # 10% sin número
        cur.execute(
            """INSERT INTO direcciones
               (id_calle, id_numero_vivienda, id_codigo_postal, id_municipio, id_estado, colonia, localidad)
               VALUES (%s, %s, %s, %s, %s, %s, %s) RETURNING id""",
            (
                random.choice(calles_ids),
                None if sin_numero else random.choice(numeros_ids),
                random.choice(cps_ids),
                random.choice(municipios_ids),
                random.choice(estados_ids),
                random.choice(colonias),
                random.choice(localidades),
            ),
        )
        dir_ids_nuevos.append(cur.fetchone()[0])
    conn.commit()

    cur.execute("SELECT id FROM direcciones")
    all_dir_ids = [r[0] for r in cur.fetchall()]

    # ── 6. Clientes (usuarios externos) ─────────────────────────────────────
    print(f"Insertando {N_CLIENTES} clientes...")
    cur.execute("SELECT email FROM usuarios")
    emails_existentes = {r[0] for r in cur.fetchall()}
    clientes_ids = []
    for i in range(N_CLIENTES):
        while True:
            email = fake.email()
            if email not in emails_existentes:
                break
        emails_existentes.add(email)
        telefono = "".join([str(random.randint(0, 9)) for _ in range(10)])
        cur.execute(
            """INSERT INTO usuarios
               (nombre, apellidos, email, password_hash, password_plain, telefono,
                id_direccion, status, activo)
               VALUES (%s, %s, %s, %s, %s, %s, %s, 'usuario', %s) RETURNING id""",
            (
                fake.first_name(),
                f"{fake.last_name()} {fake.last_name()}",
                email,
                PWD_HASH,
                PWD_PLAIN,
                telefono,
                random.choice(all_dir_ids),
                random.random() > 0.05,  # 95% activos
            ),
        )
        clientes_ids.append(cur.fetchone()[0])
    conn.commit()

    # ── 7. Productos adicionales ─────────────────────────────────────────────
    print(f"Insertando {N_PRODUCTOS} productos...")
    cur.execute("SELECT codigo FROM productos")
    codigos_existentes = {r[0] for r in cur.fetchall()}

    nombres_partes = [
        "Filtro de Combustible", "Filtro de Cabina", "Disco de Freno",
        "Bomba de Agua", "Termostato", "Manija de Puerta", "Espejo Lateral",
        "Faro Delantero LED", "Faro Trasero", "Sensor de Oxígeno",
        "Sensor MAP", "Sensor ABS", "Cable de Acelerador", "Cable de Freno",
        "Manguera de Radiador", "Válvula EGR", "Inyector de Combustible",
        "Bobina de Encendido", "Módulo de Ventilador", "Compresor A/C",
        "Condensador A/C", "Evaporador A/C", "Bomba de Dirección",
        "Cremallera de Dirección", "Rótula de Suspensión", "Terminal de Dirección",
        "Barra Estabilizadora", "Bujes de Suspensión", "Cojinete de Rueda",
        "Semieje Completo",
    ]
    random.shuffle(nombres_partes)

    productos_ids = []
    for i in range(N_PRODUCTOS):
        marca_id = random.choice(marcas_ids)
        marca_nombre = marcas_map[marca_id]
        prefijo = "".join(c for c in marca_nombre.upper() if c.isalpha())[:4]
        n = 100 + i
        while True:
            codigo = f"{prefijo}-{n:03d}"
            if codigo not in codigos_existentes:
                break
            n += 1
        codigos_existentes.add(codigo)

        cantidad = random.choices([0, random.randint(1, 50)], weights=[10, 90])[0]
        estatus  = "agotado" if cantidad == 0 else "en_stock"
        precio   = float(Decimal(str(random.uniform(80, 4500))).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP))
        nombre   = nombres_partes[i % len(nombres_partes)]

        cur.execute(
            """INSERT INTO productos
               (codigo, nombre, descripcion, id_tipo_autoparte, id_marca,
                cantidad, estatus_producto, precio)
               VALUES (%s, %s, %s, %s, %s, %s, %s, %s) RETURNING id""",
            (
                codigo,
                nombre,
                fake.sentence(nb_words=12),
                random.choice(tipos_ids),
                marca_id,
                cantidad,
                estatus,
                precio,
            ),
        )
        productos_ids.append(cur.fetchone()[0])
    conn.commit()

    # ── 8. Pedidos adicionales ───────────────────────────────────────────────
    print(f"Insertando {N_PEDIDOS} pedidos...")

    # Todos los usuarios (no solo clientes nuevos)
    cur.execute("SELECT id, id_direccion FROM usuarios WHERE status = 'usuario' AND activo = TRUE")
    clientes_rows = cur.fetchall()

    # Todos los productos en stock
    cur.execute("SELECT id, precio FROM productos WHERE estatus_producto = 'en_stock'")
    productos_en_stock = cur.fetchall()

    if not clientes_rows or not productos_en_stock:
        print("⚠️ No hay suficientes clientes o productos en stock para generar pedidos.")
    else:
        cur.execute("SELECT codigo_pedido FROM pedidos")
        codigos_pedidos = {r[0] for r in cur.fetchall()}

        # Generar código único ORD-XXXX
        def gen_codigo():
            while True:
                num = random.randint(1, 9999)
                c = f"ORD-{num:04d}"
                if c not in codigos_pedidos:
                    codigos_pedidos.add(c)
                    return c

        base_fecha = datetime.now() - timedelta(days=180)

        for _ in range(N_PEDIDOS):
            cliente_id, cliente_dir = random.choice(clientes_rows)
            dir_envio = cliente_dir if cliente_dir else random.choice(all_dir_ids)
            estado    = random.choice(ESTADOS_PEDIDO)
            n_items   = random.randint(1, 5)
            items     = random.sample(productos_en_stock, min(n_items, len(productos_en_stock)))
            fecha     = base_fecha + timedelta(days=random.randint(0, 180))
            codigo    = gen_codigo()

            subtotal = Decimal("0")
            detalles = []
            for prod_id, prod_precio in items:
                cant = random.randint(1, 4)
                precio_u = Decimal(str(prod_precio))
                subtotal += precio_u * cant
                detalles.append((prod_id, cant, float(precio_u)))

            impuestos = (subtotal * Decimal("0.16")).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP)
            total     = (subtotal + impuestos).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP)

            cur.execute(
                """INSERT INTO pedidos
                   (codigo_pedido, id_usuario, id_direccion_envio, estado_pedido,
                    subtotal, impuestos, total, fecha_pedido)
                   VALUES (%s, %s, %s, %s, %s, %s, %s, %s) RETURNING id""",
                (codigo, cliente_id, dir_envio, estado,
                 float(subtotal), float(impuestos), float(total), fecha),
            )
            pedido_id = cur.fetchone()[0]

            for prod_id, cant, precio_u in detalles:
                cur.execute(
                    """INSERT INTO pedido_productos (id_pedido, id_producto, cantidad, precio_unitario)
                       VALUES (%s, %s, %s, %s)
                       ON CONFLICT (id_pedido, id_producto) DO NOTHING""",
                    (pedido_id, prod_id, cant, precio_u),
                )

        conn.commit()

    # ── Resumen ──────────────────────────────────────────────────────────────
    cur.execute("SELECT COUNT(*) FROM usuarios")
    print(f"\n✅ Seed completado:")
    print(f"   Usuarios:    {cur.fetchone()[0]}")
    cur.execute("SELECT COUNT(*) FROM productos")
    print(f"   Productos:   {cur.fetchone()[0]}")
    cur.execute("SELECT COUNT(*) FROM pedidos")
    print(f"   Pedidos:     {cur.fetchone()[0]}")
    cur.execute("SELECT COUNT(*) FROM direcciones")
    print(f"   Direcciones: {cur.fetchone()[0]}")

    cur.close()
    conn.close()
    print("\nConexión cerrada. ¡Listo!")


if __name__ == "__main__":
    run()
