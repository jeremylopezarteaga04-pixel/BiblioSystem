-- BiblioSystem: esquema SQLite independiente de MySQL, DevHarbor y MariaDB.
-- El archivo bibliosystem.sqlite ya incluye estas tablas, categorías y autores.

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS autores (
    id_autor INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    nacionalidad TEXT,
    fecha_nacimiento TEXT
);

CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL UNIQUE,
    descripcion TEXT,
    estado TEXT NOT NULL DEFAULT 'ACTIVO' CHECK (estado IN ('ACTIVO', 'INACTIVO'))
);

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
    cedula TEXT NOT NULL UNIQUE,
    nombres TEXT NOT NULL,
    apellidos TEXT NOT NULL,
    correo TEXT NOT NULL UNIQUE,
    telefono TEXT,
    direccion TEXT,
    estado TEXT NOT NULL DEFAULT 'ACTIVO' CHECK (estado IN ('ACTIVO', 'INACTIVO')),
    fecha_registro TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cuentas (
    id_cuenta INTEGER PRIMARY KEY AUTOINCREMENT,
    id_usuario INTEGER NOT NULL UNIQUE,
    password_hash TEXT,
    rol TEXT NOT NULL DEFAULT 'USUARIO' CHECK (rol IN ('ADMIN', 'USUARIO')),
    estado_cuenta TEXT NOT NULL DEFAULT 'PENDIENTE' CHECK (estado_cuenta IN ('PENDIENTE', 'ACTIVADA')),
    token_activacion TEXT,
    fecha_activacion TEXT,
    fecha_creacion TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS libros (
    id_libro INTEGER PRIMARY KEY AUTOINCREMENT,
    codigo TEXT NOT NULL UNIQUE,
    titulo TEXT NOT NULL,
    id_autor INTEGER NOT NULL,
    id_categoria INTEGER NOT NULL,
    editorial TEXT,
    anio_publicacion INTEGER,
    isbn TEXT UNIQUE,
    cantidad_total INTEGER NOT NULL DEFAULT 1 CHECK (cantidad_total >= 0),
    cantidad_disponible INTEGER NOT NULL DEFAULT 1 CHECK (cantidad_disponible >= 0),
    descripcion TEXT,
    imagen TEXT,
    estado TEXT NOT NULL DEFAULT 'ACTIVO' CHECK (estado IN ('ACTIVO', 'INACTIVO')),
    fecha_registro TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_autor) REFERENCES autores (id_autor),
    FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria)
);

CREATE TABLE IF NOT EXISTS prestamos (
    id_prestamo INTEGER PRIMARY KEY AUTOINCREMENT,
    id_usuario INTEGER NOT NULL,
    id_libro INTEGER NOT NULL,
    fecha_prestamo TEXT NOT NULL,
    fecha_devolucion_programada TEXT NOT NULL,
    fecha_devolucion_real TEXT,
    estado TEXT NOT NULL DEFAULT 'ACTIVO' CHECK (estado IN ('ACTIVO', 'DEVUELTO', 'ATRASADO')),
    observacion TEXT,
    fecha_registro TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
    FOREIGN KEY (id_libro) REFERENCES libros (id_libro)
);

CREATE TABLE IF NOT EXISTS bitacora (
    id_bitacora INTEGER PRIMARY KEY AUTOINCREMENT,
    accion TEXT NOT NULL,
    descripcion TEXT,
    tabla_afectada TEXT,
    id_registro INTEGER,
    fecha TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_libros_titulo ON libros (titulo);
CREATE INDEX IF NOT EXISTS idx_usuarios_cedula ON usuarios (cedula);
CREATE INDEX IF NOT EXISTS idx_prestamos_estado ON prestamos (estado);
CREATE INDEX IF NOT EXISTS idx_cuentas_estado ON cuentas (estado_cuenta);
CREATE INDEX IF NOT EXISTS idx_cuentas_rol ON cuentas (rol);

INSERT INTO categorias (nombre, descripcion) VALUES
    ('Novela', 'Narrativa contemporánea y grandes historias de ficción'),
    ('Literatura clásica', 'Obras clásicas de la literatura universal'),
    ('Fantasía', 'Mundos imaginarios, magia y aventuras fantásticas'),
    ('Ciencia ficción', 'Futuros posibles, tecnología y universos alternativos'),
    ('Romance', 'Historias de amor y relaciones humanas'),
    ('Misterio y suspenso', 'Investigación, intriga y relatos policiales'),
    ('Programación', 'Desarrollo de software, algoritmos y buenas prácticas'),
    ('Desarrollo web', 'HTML, CSS, JavaScript y aplicaciones web'),
    ('Inteligencia artificial', 'Aprendizaje automático, datos e inteligencia artificial'),
    ('Bases de datos', 'Diseño, administración y consultas de bases de datos'),
    ('Ciencia', 'Divulgación científica y conocimientos de investigación'),
    ('Historia', 'Acontecimientos, sociedades y personajes históricos'),
    ('Desarrollo personal', 'Hábitos, liderazgo y crecimiento personal'),
    ('Poesía', 'Poemas, versos y expresiones literarias');

INSERT INTO autores (nombre, nacionalidad, fecha_nacimiento) VALUES
    ('Gabriel García Márquez', 'Colombiana', '1927-03-06'),
    ('Isabel Allende', 'Chilena', '1942-08-02'),
    ('George Orwell', 'Británica', '1903-06-25'),
    ('Jane Austen', 'Británica', '1775-12-16'),
    ('J. K. Rowling', 'Británica', '1965-07-31'),
    ('J. R. R. Tolkien', 'Británica', '1892-01-03'),
    ('Agatha Christie', 'Británica', '1890-09-15'),
    ('Antoine de Saint-Exupéry', 'Francesa', '1900-06-29'),
    ('Mario Vargas Llosa', 'Peruana', '1936-03-28'),
    ('Julio Cortázar', 'Argentina', '1914-08-26'),
    ('Miguel de Cervantes', 'Española', '1547-09-29'),
    ('Stephen King', 'Estadounidense', '1947-09-21'),
    ('Robert C. Martin', 'Estadounidense', '1952-12-05'),
    ('Martin Fowler', 'Británica', '1963-12-18'),
    ('Eric Matthes', 'Estadounidense', NULL),
    ('Jon Duckett', 'Británica', NULL),
    ('Andrew Hunt', 'Estadounidense', NULL),
    ('David Thomas', 'Británica', NULL),
    ('Stuart Russell', 'Británica', '1962-01-01'),
    ('Peter Norvig', 'Estadounidense', '1956-12-14');

INSERT INTO usuarios (cedula,nombres,apellidos,correo,telefono,direccion,estado) VALUES
    ('0999999991','Juan Carlos','Rodriguez Perez','admin@bibliosystem.com',NULL,NULL,'ACTIVO'),
    ('0999999992','Administrador','Secundario','admin2@bibliosystem.com',NULL,NULL,'ACTIVO');

-- admin123
-- admin1234

INSERT INTO cuentas (id_usuario,password_hash,rol,estado_cuenta)
SELECT
    id_usuario,
    CASE
        WHEN correo = 'admin@bibliosystem.com'
            THEN '$2y$10$Syc2N6Pk0PcYYlrlRphM.uGuc8WujmMhBW8MtF0wSXBsISAwLY6YC'
        WHEN correo = 'admin2@bibliosystem.com'
            THEN '$2y$10$BfQ1iNxowDUYqvxEVFg/Zuq6VVWac6AZgIMDSw5VTvZm0y1xj5uZC'
    END,
    'ADMIN',
    'ACTIVADA'
FROM usuarios
WHERE correo IN (
    'admin@bibliosystem.com',
    'admin2@bibliosystem.com'
);