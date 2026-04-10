-- ============================================================
--  MACUIN — Esquema de Base de Datos PostgreSQL
--  Autor: Sistema Macuin
--  Fecha: 2026-04-09
-- ============================================================

-- ============================================================
--  1. TIPOS ENUM
-- ============================================================

CREATE TYPE estatus_usuario AS ENUM (
    'administrador',
    'superadministrador',
    'usuario'
);

CREATE TYPE estatus_producto AS ENUM (
    'en_stock',
    'agotado',
    'descontinuado'
);

CREATE TYPE estado_pedido AS ENUM (
    'pendiente',
    'surtido',
    'enviado',
    'en_camino',
    'entregado',
    'cancelado'
);


-- ============================================================
--  2. TABLAS CATÁLOGO DE DIRECCIÓN
-- ============================================================

-- 2.1 Estados de la República
CREATE TABLE estados (
    id    SERIAL       PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- 2.2 Municipios (FK → estados)
CREATE TABLE municipios (
    id        SERIAL       PRIMARY KEY,
    nombre    VARCHAR(150) NOT NULL,
    id_estado INT          NOT NULL REFERENCES estados(id) ON DELETE CASCADE,
    UNIQUE (nombre, id_estado)
);

-- 2.3 Códigos Postales
CREATE TABLE codigos_postales (
    id     SERIAL      PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL UNIQUE
);

-- 2.4 Calles
CREATE TABLE calles (
    id     SERIAL       PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL
);

-- 2.5 Números de Vivienda
CREATE TABLE numeros_vivienda (
    id     SERIAL      PRIMARY KEY,
    numero VARCHAR(20) NOT NULL
);


-- ============================================================
--  3. TABLA DIRECCIONES (compuesta de FKs)
-- ============================================================

CREATE TABLE direcciones (
    id                 SERIAL PRIMARY KEY,
    id_calle           INT    NOT NULL REFERENCES calles(id)            ON DELETE RESTRICT,
    id_numero_vivienda INT    NOT NULL REFERENCES numeros_vivienda(id)  ON DELETE RESTRICT,
    id_codigo_postal   INT    NOT NULL REFERENCES codigos_postales(id)  ON DELETE RESTRICT,
    id_municipio       INT    NOT NULL REFERENCES municipios(id)        ON DELETE RESTRICT,
    id_estado          INT    NOT NULL REFERENCES estados(id)           ON DELETE RESTRICT
);

CREATE INDEX idx_direcciones_estado    ON direcciones(id_estado);
CREATE INDEX idx_direcciones_municipio ON direcciones(id_municipio);
CREATE INDEX idx_direcciones_cp        ON direcciones(id_codigo_postal);


-- ============================================================
--  4. TABLA USUARIOS
-- ============================================================

CREATE TABLE usuarios (
    id            SERIAL           PRIMARY KEY,
    nombre        VARCHAR(100)     NOT NULL,
    apellidos     VARCHAR(100)     NOT NULL,
    email         VARCHAR(150)     NOT NULL UNIQUE,
    password_hash VARCHAR(255)     NOT NULL,
    password_plain VARCHAR(255),
    id_direccion  INT              REFERENCES direcciones(id) ON DELETE SET NULL,
    status        estatus_usuario  NOT NULL DEFAULT 'usuario',
    activo        BOOLEAN          NOT NULL DEFAULT TRUE,
    created_at    TIMESTAMP        NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP        NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_usuarios_status ON usuarios(status);
CREATE INDEX idx_usuarios_email  ON usuarios(email);


-- ============================================================
--  5. CATÁLOGOS — Tipos de Autoparte y Marcas
-- ============================================================

CREATE TABLE tipos_autoparte (
    id     SERIAL       PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE marcas (
    id     SERIAL       PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);


-- ============================================================
--  6. TABLA PRODUCTOS (Inventario)
-- ============================================================

CREATE TABLE productos (
    id                 SERIAL           PRIMARY KEY,
    codigo             VARCHAR(50)      NOT NULL UNIQUE,
    nombre             VARCHAR(200)     NOT NULL,
    descripcion        TEXT,
    imagen_url         TEXT,
    id_tipo_autoparte  INT              REFERENCES tipos_autoparte(id) ON DELETE SET NULL,
    id_marca           INT              REFERENCES marcas(id)          ON DELETE SET NULL,
    cantidad           INT              NOT NULL DEFAULT 0 CHECK (cantidad >= 0),
    estatus_producto   estatus_producto NOT NULL DEFAULT 'en_stock',
    precio             DECIMAL(12, 2)   NOT NULL DEFAULT 0.00 CHECK (precio >= 0),
    created_at         TIMESTAMP        NOT NULL DEFAULT NOW(),
    updated_at         TIMESTAMP        NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_productos_codigo  ON productos(codigo);
CREATE INDEX idx_productos_estatus ON productos(estatus_producto);
CREATE INDEX idx_productos_tipo    ON productos(id_tipo_autoparte);
CREATE INDEX idx_productos_marca   ON productos(id_marca);


-- ============================================================
--  6. TABLA PEDIDOS
-- ============================================================

CREATE TABLE pedidos (
    id                 SERIAL         PRIMARY KEY,
    codigo_pedido      VARCHAR(20)    NOT NULL UNIQUE,
    id_usuario         INT            NOT NULL REFERENCES usuarios(id)     ON DELETE RESTRICT,
    id_direccion_envio INT            NOT NULL REFERENCES direcciones(id)  ON DELETE RESTRICT,
    estado_pedido      estado_pedido  NOT NULL DEFAULT 'pendiente',
    subtotal           DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    impuestos          DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    total              DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    fecha_pedido       TIMESTAMP      NOT NULL DEFAULT NOW(),
    updated_at         TIMESTAMP      NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_pedidos_usuario ON pedidos(id_usuario);
CREATE INDEX idx_pedidos_estado  ON pedidos(estado_pedido);
CREATE INDEX idx_pedidos_fecha   ON pedidos(fecha_pedido);


-- ============================================================
--  7. TABLA PEDIDO_PRODUCTOS (relación muchos a muchos)
-- ============================================================

CREATE TABLE pedido_productos (
    id              SERIAL         PRIMARY KEY,
    id_pedido       INT            NOT NULL REFERENCES pedidos(id)   ON DELETE CASCADE,
    id_producto     INT            NOT NULL REFERENCES productos(id) ON DELETE RESTRICT,
    cantidad        INT            NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(12, 2) NOT NULL CHECK (precio_unitario >= 0),
    UNIQUE (id_pedido, id_producto)
);

CREATE INDEX idx_pp_pedido   ON pedido_productos(id_pedido);
CREATE INDEX idx_pp_producto ON pedido_productos(id_producto);


-- ============================================================
--  8. FUNCIÓN TRIGGER — Actualizar updated_at automáticamente
-- ============================================================

CREATE OR REPLACE FUNCTION actualizar_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Triggers para cada tabla con updated_at
CREATE TRIGGER trg_usuarios_updated_at
    BEFORE UPDATE ON usuarios
    FOR EACH ROW EXECUTE FUNCTION actualizar_updated_at();

CREATE TRIGGER trg_productos_updated_at
    BEFORE UPDATE ON productos
    FOR EACH ROW EXECUTE FUNCTION actualizar_updated_at();

CREATE TRIGGER trg_pedidos_updated_at
    BEFORE UPDATE ON pedidos
    FOR EACH ROW EXECUTE FUNCTION actualizar_updated_at();


-- ============================================================
--  9. DATOS SEED DE PRUEBA
-- ============================================================

-- 9.1 Estados
INSERT INTO estados (nombre) VALUES
    ('Querétaro'),
    ('Ciudad de México'),
    ('Estado de México'),
    ('Jalisco'),
    ('Nuevo León');

-- 9.2 Municipios
INSERT INTO municipios (nombre, id_estado) VALUES
    ('Querétaro',         1),
    ('El Marqués',        1),
    ('Corregidora',       1),
    ('Cuauhtémoc',        2),
    ('Benito Juárez',     2),
    ('Tlalnepantla',      3),
    ('Naucalpan',         3),
    ('Guadalajara',       4),
    ('Zapopan',           4),
    ('Monterrey',         5),
    ('San Pedro Garza García', 5);

-- 9.3 Códigos Postales
INSERT INTO codigos_postales (codigo) VALUES
    ('76000'),
    ('76100'),
    ('76240'),
    ('06000'),
    ('03100'),
    ('54000'),
    ('53000'),
    ('44100'),
    ('45110'),
    ('64000'),
    ('66220');

-- 9.4 Calles
INSERT INTO calles (nombre) VALUES
    ('Av. Universidad'),
    ('Blvd. Bernardo Quintana'),
    ('Calle 5 de Febrero'),
    ('Av. Reforma'),
    ('Calle Insurgentes'),
    ('Av. Constituyentes'),
    ('Blvd. López Mateos'),
    ('Calle Morelos'),
    ('Av. Vallarta'),
    ('Av. Revolución');

-- 9.5 Números de Vivienda
INSERT INTO numeros_vivienda (numero) VALUES
    ('123'),
    ('456'),
    ('789'),
    ('1010'),
    ('22-A'),
    ('500'),
    ('15'),
    ('88-B'),
    ('300'),
    ('42');

-- 9.6 Direcciones
INSERT INTO direcciones (id_calle, id_numero_vivienda, id_codigo_postal, id_municipio, id_estado) VALUES
    (1, 2, 1, 1, 1),   -- Av. Universidad 456, CP 76000, Querétaro, Qro.
    (2, 1, 2, 2, 1),   -- Blvd. B. Quintana 123, CP 76100, El Marqués, Qro.
    (4, 4, 4, 4, 2),   -- Av. Reforma 1010, CP 06000, Cuauhtémoc, CDMX
    (5, 5, 5, 5, 2),   -- Insurgentes 22-A, CP 03100, Benito Juárez, CDMX
    (9, 6, 8, 8, 4),   -- Av. Vallarta 500, CP 44100, Guadalajara, Jal.
    (10, 9, 10, 10, 5); -- Av. Revolución 300, CP 64000, Monterrey, NL

-- 9.7 Usuarios
--   Password de todos: "macuin2026" (hash bcrypt de ejemplo)
INSERT INTO usuarios (nombre, apellidos, email, password_hash, password_plain, id_direccion, status, activo) VALUES
    ('Carlos',   'Herrera Mendoza',    'superadmin@macuin.com',   '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 1, 'superadministrador', TRUE),
    ('Laura',    'Mendoza García',     'laura@macuin.com',        '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 2, 'administrador',      TRUE),
    ('Roberto',  'Sánchez López',      'roberto@macuin.com',      '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 1, 'administrador',      FALSE),
    ('Ana',      'López Ramírez',      'ana@cliente.com',         '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 3, 'usuario',            TRUE),
    ('Miguel',   'Torres Fernández',   'miguel@gmail.com',        '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 4, 'usuario',            TRUE),
    ('Sofía',    'Ramírez Castro',     'sofia@hotmail.com',       '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 5, 'usuario',            TRUE),
    ('Diego',    'Fernández Morales',  'diego@gmail.com',         '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 6, 'usuario',            FALSE),
    ('Valeria',  'Castro Ríos',        'valeria@yahoo.com',       '$2b$12$NWCr70IlwSWQrUaa0ndXyqsw65CafdnUOxifFiNlx1XVAfBy', 'macuin2026', 5, 'usuario',            TRUE);

-- 9.8 Tipos de Autoparte
INSERT INTO tipos_autoparte (nombre) VALUES
    ('Filtros'),('Bujías'),('Neumáticos'),('Frenos'),('Baterías'),
    ('Amortiguadores'),('Alternadores'),('Bandas y Cadenas'),('Radiadores'),('Embragues'),
    ('Escape'),('Encendido'),('Suspensión'),('Dirección'),('Transmisión'),
    ('Climatización'),('Iluminación'),('Lubricantes'),('Sensores'),('Carrocería');

-- 9.9 Marcas
INSERT INTO marcas (nombre) VALUES
    ('Bosch'),('NGK'),('Michelin'),('Brembo'),('Varta'),
    ('Monroe'),('Denso'),('Gates'),('Wagner'),('ACDelco');

-- 9.10 Productos (Inventario Macuin — Autopartes)
INSERT INTO productos (codigo, nombre, descripcion, imagen_url, id_tipo_autoparte, id_marca, cantidad, estatus_producto, precio) VALUES
    ('BOSCH-001', 'Filtro de Aceite Sintético',    'Filtro de alto rendimiento compatible con motores V6 y V8. Duración estimada: 15,000 km.',                    'https://cdn-icons-png.flaticon.com/512/3659/3659898.png', 1, 1,  45, 'en_stock',        250.00),
    ('NGK-002',   'Bujía de Iridio IX',            'Bujía de encendido de iridio, mejora la eficiencia del motor. Compatible con vehículos 2015+.',                'https://cdn-icons-png.flaticon.com/512/2917/2917995.png', 2, 2,  5,  'en_stock',        220.50),
    ('MIC-003',   'Neumático 205/55 R16',          'Neumático radial para vehículos sedán. Excelente tracción en seco y mojado.',                                  'https://cdn-icons-png.flaticon.com/512/3774/3774278.png', 3, 3,  12, 'en_stock',       1850.00),
    ('BRE-004',   'Balatas Delanteras Cerámicas',   'Balatas de disco cerámicas Brembo, bajo nivel de ruido y polvo. Para vehículos medianos y SUVs.',              'https://cdn-icons-png.flaticon.com/512/2830/2830312.png', 4, 4,  2,  'en_stock',        950.00),
    ('VAL-005',   'Batería 12V L-74',              'Batería libre de mantenimiento, 74 Ah. Ideal para climas extremos.',                                           'https://cdn-icons-png.flaticon.com/512/3659/3659899.png', 5, 5,  18, 'en_stock',       2100.00),
    ('MON-006',   'Amortiguador Trasero Gas',       'Amortiguador de gas Monroe para suspensión trasera. Recorrido suave y estable.',                               'https://cdn-icons-png.flaticon.com/512/2917/2917991.png', 6, 6,  0,  'agotado',         780.00),
    ('DEN-007',   'Alternador 120A Remanufacturado','Alternador Denso remanufacturado, 120 amperes. Garantía de 12 meses.',                                        'https://cdn-icons-png.flaticon.com/512/2917/2917984.png', 7, 7,  8,  'en_stock',       3200.00),
    ('GAT-008',   'Banda de Distribución Kit',      'Kit completo con banda, tensor e idler Gates. Para motores 4 cilindros.',                                      'https://cdn-icons-png.flaticon.com/512/2917/2917999.png', 8, 8,  0,  'descontinuado',  1450.00),
    ('WAG-009',   'Pastillas de Freno Traseras',    'Pastillas Wagner ThermoQuiet. Frenado silencioso y duradero.',                                                  'https://cdn-icons-png.flaticon.com/512/2830/2830305.png', 4, 9,  30, 'en_stock',        480.00),
    ('ACDl-010',  'Filtro de Aire Motor',           'Filtro de aire profesional ACDelco. Mejora el flujo de aire al motor hasta un 20%.',                           'https://cdn-icons-png.flaticon.com/512/3659/3659910.png', 1, 10, 22, 'en_stock',        185.00);

-- 9.9 Pedidos
INSERT INTO pedidos (codigo_pedido, id_usuario, id_direccion_envio, estado_pedido, subtotal, impuestos, total) VALUES
    ('ORD-9011', 4, 3, 'pendiente',  5355.00,  856.80,  6211.80),
    ('ORD-9010', 5, 4, 'surtido',    3400.00,  544.00,  3944.00),
    ('ORD-9009', 6, 5, 'enviado',   45800.00, 7328.00, 53128.00),
    ('ORD-9008', 7, 6, 'entregado',   950.00,  152.00,  1102.00),
    ('ORD-9007', 8, 5, 'cancelado',  6700.00, 1072.00,  7772.00);

-- 9.10 Detalle de Pedidos (pedido_productos)
INSERT INTO pedido_productos (id_pedido, id_producto, cantidad, precio_unitario) VALUES
    -- ORD-9011
    (1, 1, 5,  250.00),   -- 5x Filtro de Aceite
    (1, 2, 10, 220.50),   -- 10x Bujía de Iridio
    (1, 4, 2,  950.00),   -- 2x Balatas Cerámicas
    -- ORD-9010
    (2, 3, 1, 1850.00),   -- 1x Neumático
    (2, 9, 2,  480.00),   -- 2x Pastillas Traseras
    -- ORD-9009
    (3, 5, 10, 2100.00),  -- 10x Batería
    (3, 7, 5,  3200.00),  -- 5x Alternador
    (3, 10, 20, 185.00),  -- 20x Filtro de Aire
    -- ORD-9008
    (4, 4, 1,  950.00),   -- 1x Balatas Cerámicas
    -- ORD-9007
    (5, 3, 2, 1850.00),   -- 2x Neumático
    (5, 9, 4,  480.00),   -- 4x Pastillas Traseras
    (5, 10, 5, 185.00);   -- 5x Filtro de Aire


-- ============================================================
--  FIN DEL SCRIPT DE INICIALIZACIÓN
-- ============================================================
