from flask import Flask, render_template, request

app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        # Aquí se extraerán los datos para enviarlos a la API de FastAPI      
        email = request.form.get('email')
        password = request.form.get('password')

        # TODO: Implementar petición HTTP/REST a FastAPI para obtener el token 

        print(f"Intento de login con: {email}") # Placeholder para debug        

        from flask import redirect, url_for
        return redirect(url_for('inventario'))

    return render_template('login.html')

@app.route('/inventario')
def inventario():
    # Datos simulados (mock) que en el futuro vendrán de la API en FastAPI
    autopartes = [
        {"codigo": "BOSCH-001", "nombre": "Filtro de Aceite Sintético", "categoria": "Motor", "marca": "Bosch", "stock": 45, "precio": 250.00},
        {"codigo": "NGK-002", "nombre": "Bujía de Iridio IX", "categoria": "Encendido", "marca": "NGK", "stock": 5, "precio": 220.50},
        {"codigo": "MIC-003", "nombre": "Neumático 205/55 R16", "categoria": "Ruedas", "marca": "Michelin", "stock": 12, "precio": 1850.00},
        {"codigo": "BRE-004", "nombre": "Balatas Delanteras Cerámicas", "categoria": "Frenos", "marca": "Brembo", "stock": 2, "precio": 950.00},
        {"codigo": "VAL-005", "nombre": "Batería 12V L-74", "categoria": "Eléctrico", "marca": "Valvoline", "stock": 18, "precio": 2100.00}
    ]
    return render_template('inventario.html', autopartes=autopartes)

@app.route('/autoparte/nueva')
def nueva_autoparte():
    return render_template('formulario_autoparte.html', autoparte=None)

@app.route('/autoparte/editar/<codigo>')
def editar_autoparte(codigo):
    # Simulación de obtención de datos desde la API
    autoparte_mock = {
        "codigo": codigo, 
        "nombre": "Filtro de Aceite Sintético", 
        "categoria": "Motor", 
        "stock": 45, 
        "precio": 250.00,
        "descripcion": "Filtro de alto rendimiento, compatible con motores V6 y V8. \nDuración estimada: 15,000 km."
    }
    return render_template('formulario_autoparte.html', autoparte=autoparte_mock)

@app.route('/autoparte/guardar', methods=['POST'])
def guardar_autoparte():
    # Aquí iría la lógica para enviar los datos JSON a la API FastAPI
    datos = request.form
    print("Guardando autoparte:", datos)
    # Redirigir al inventario después de guardar
    from flask import redirect, url_for
    return redirect(url_for('inventario'))

@app.route('/almacen/actualizar')
def vista_actualizacion():
    # Vista inicial vacía para la herramienta utilitaria
    return render_template('actualizacion_rapida.html', pieza=None, sku_buscado=None)

@app.route('/almacen/buscar')
def buscar_pieza_almacen():
    sku = request.args.get('sku', '').strip().upper()
    
    # Simulación de búsqueda en API por SKU
    pieza_mock = None
    if sku == "BOSCH-001":
        pieza_mock = {"codigo": "BOSCH-001", "nombre": "Filtro de Aceite Sintético", "categoria": "Motor", "marca": "Bosch", "stock": 45}
    elif sku == "VAL-005":
        pieza_mock = {"codigo": "VAL-005", "nombre": "Batería 12V L-74", "categoria": "Eléctrico", "marca": "Valvoline", "stock": 18}

    return render_template('actualizacion_rapida.html', pieza=pieza_mock, sku_buscado=sku)

@app.route('/almacen/modificar', methods=['POST'])
def modificar_stock():
    codigo = request.form.get('codigo')
    cantidad = int(request.form.get('cantidad', 1))
    tipo = request.form.get('tipo_ajuste') # 'entrada' o 'salida'
    
    # Aquí se haría la petición HTTP PATCH o PUT a FastAPI para modificar solo el stock.
    print(f"[{tipo.upper()}] Pieza: {codigo} | Cantidad: {cantidad}")
    
    # Regresamos a la vista de búsqueda, idealmente listos para escanear otra pieza
    from flask import redirect, url_for
    return redirect(url_for('vista_actualizacion'))

@app.route('/pedidos')
def dashboard_pedidos():
    # Simulación de respuesta JSON gigante de la API (Paginada por FastAPI)
    pedidos_mock = [
        {"id": "ORD-9011", "fecha": "2026-03-03", "cliente": {"nombre": "Refaccionaria El Pistón", "telefono": "442-111-2233"}, "articulos": 12, "total": 12500.50, "estatus": "Pendiente"},
        {"id": "ORD-9010", "fecha": "2026-03-02", "cliente": {"nombre": "Taller Mecánico Hermanos M", "telefono": "442-555-8899"}, "articulos": 4, "total": 3400.00, "estatus": "Surtido"},
        {"id": "ORD-9009", "fecha": "2026-03-01", "cliente": {"nombre": "AutoTech Querétaro", "telefono": "442-777-6655"}, "articulos": 25, "total": 45800.00, "estatus": "Enviado"},
        {"id": "ORD-9008", "fecha": "2026-02-28", "cliente": {"nombre": "Frenos y Clutch El Chino", "telefono": "442-222-3344"}, "articulos": 2, "total": 950.00, "estatus": "Entregado"},
        {"id": "ORD-9007", "fecha": "2026-02-28", "cliente": {"nombre": "Suspensiones del Bajío", "telefono": "442-999-0000"}, "articulos": 8, "total": 6700.00, "estatus": "Cancelado"},
    ]
    return render_template('pedidos_dashboard.html', pedidos=pedidos_mock)

@app.route('/pedidos/detalle/<id>')
def detalle_pedido(id):
    # Simulación de un GET request a la API FastAPI para un ID específico
    pedido_mock = {
        "id": id,
        "fecha_creacion": "03 de Marzo, 2026 - 10:45 AM",
        "estatus": "recibido", # Opciones de backend: 'recibido', 'surtido', 'enviado'
        "cliente": {
            "nombre": "Refaccionaria El Pistón S.A. de C.V.",
            "telefono": "442-111-2233",
            "email": "contacto@elpiston.com.mx",
            "direccion": "Av. Universidad 456, Col. Centro, Querétaro, Qro. CP 76000"
        },
        "articulos": [
            {"codigo": "BOSCH-001", "nombre": "Filtro de Aceite Sintético", "cantidad": 5, "precio_unitario": 250.00},
            {"codigo": "NGK-002", "nombre": "Bujía de Iridio IX", "cantidad": 10, "precio_unitario": 220.50},
            {"codigo": "BRE-004", "nombre": "Balatas Delanteras Cerámicas", "cantidad": 2, "precio_unitario": 950.00}
        ],
        "finanzas": {
            "subtotal": 5355.00,
            "impuestos": 856.80,
            "total": 6211.80
        }
    }
    return render_template('detalle_pedido.html', pedido=pedido_mock)

@app.route('/pedidos/actualizar', methods=['POST'])
def actualizar_estatus_pedido():
    # En un caso real, esto se mapearía a una solicitud PUT o PATCH en la API de FastAPI
    pedido_id = request.form.get('pedido_id')
    nuevo_estatus = request.form.get('estatus')
    
    print(f"Enviando actualización a FastAPI -> Pedido: {pedido_id}, Estatus: {nuevo_estatus}")
    
    from flask import redirect, url_for
    return redirect(url_for('detalle_pedido', id=pedido_id))

@app.route('/reportes')
def vista_reportes():
    # Filtros
    tipo_reporte = request.args.get('tipo', 'ventas')
    
    # KPI Dummy (Vendrían de agregaciones en el Backend)
    kpis = {
        "ventas": 245900.50,
        "pedidos": 128,
        "clientes": 14
    }
    
    # Datos para tabla resumen (Dummy switch según el tipo)
    datos = []
    if tipo_reporte == 'ventas':
        datos = [
            {"segmento": "Motor y Transmisión", "valor": "$120,400"},
            {"segmento": "Frenos y Suspensión", "valor": "$85,000"},
            {"segmento": "Eléctrico", "valor": "$40,500.50"}
        ]
    elif tipo_reporte == 'pedidos':
         datos = [
            {"segmento": "Entregados", "valor": "85 ped."},
            {"segmento": "En Tránsito", "valor": "32 ped."},
            {"segmento": "Cancelados", "valor": "11 ped."}
        ]
    elif tipo_reporte == 'clientes':
         datos = [
            {"segmento": "Talleres Mecánicos", "valor": "8 nuevos"},
            {"segmento": "Refaccionarias", "valor": "4 nuevos"},
            {"segmento": "Público General", "valor": "2 nuevos"}
        ]

    return render_template('reportes.html', report_type=tipo_reporte, kpi=kpis, datos_tabla=datos)

if __name__ == '__main__':
    app.run(debug=True, port=5000)
