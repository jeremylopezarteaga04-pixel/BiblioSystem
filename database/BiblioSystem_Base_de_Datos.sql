-- WebEdit Full Export: devhara1f3f9_dh_jeloarte3b8f7e
-- 2026-08-20 00:49:04

SET FOREIGN_KEY_CHECKS=0;

-- Table: autores
DROP TABLE IF EXISTS `autores`;
CREATE TABLE `autores` (
  `id_autor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  PRIMARY KEY (`id_autor`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `autores` VALUES ('1', 'Gabriel García Márquez', 'Colombiana', '1927-03-06');
INSERT INTO `autores` VALUES ('2', 'George Orwell', 'Británica', '1903-06-25');
INSERT INTO `autores` VALUES ('3', 'J. K. Rowling', 'Británica', '1965-07-31');
INSERT INTO `autores` VALUES ('4', 'Miguel de Cervantes', 'Española', '1547-09-29');
INSERT INTO `autores` VALUES ('5', 'Isaac Asimov', 'Estadounidense', '1920-01-02');
INSERT INTO `autores` VALUES ('6', 'Robert C. Martin', 'Estadounidense', '1952-12-05');

-- Table: bitacora
DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `accion` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `id_registro` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_bitacora`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table: categorias
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `categorias` VALUES ('1', 'Novela', 'Libros de narrativa y ficción', 'ACTIVO');
INSERT INTO `categorias` VALUES ('2', 'Ciencia', 'Libros relacionados con ciencia', 'ACTIVO');
INSERT INTO `categorias` VALUES ('3', 'Tecnología', 'Libros de informática y tecnología', 'ACTIVO');
INSERT INTO `categorias` VALUES ('4', 'Historia', 'Libros históricos', 'ACTIVO');
INSERT INTO `categorias` VALUES ('5', 'Literatura', 'Obras literarias', 'ACTIVO');
INSERT INTO `categorias` VALUES ('6', 'Educación', 'Material educativo', 'ACTIVO');

-- Table: libros
DROP TABLE IF EXISTS `libros`;
CREATE TABLE `libros` (
  `id_libro` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `id_autor` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `editorial` varchar(150) DEFAULT NULL,
  `anio_publicacion` int(11) DEFAULT NULL,
  `isbn` varchar(30) DEFAULT NULL,
  `cantidad_total` int(11) NOT NULL DEFAULT 1,
  `cantidad_disponible` int(11) NOT NULL DEFAULT 1,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_libro`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `fk_libro_autor` (`id_autor`),
  KEY `fk_libro_categoria` (`id_categoria`),
  KEY `idx_libros_titulo` (`titulo`),
  CONSTRAINT `fk_libro_autor` FOREIGN KEY (`id_autor`) REFERENCES `autores` (`id_autor`),
  CONSTRAINT `fk_libro_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `libros` VALUES ('1', 'LIB-001', 'Cien años de soledad', '1', '1', 'Sudamericana', '1967', '9780307474728', '5', '5', 'Novela representativa del realismo mágico.', NULL, 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `libros` VALUES ('2', 'LIB-002', '1984', '2', '1', 'Secker & Warburg', '1949', '9780451524935', '4', '4', 'Novela distópica sobre una sociedad vigilada.', NULL, 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `libros` VALUES ('3', 'LIB-003', 'Harry Potter y la piedra filosofal', '3', '1', 'Bloomsbury', '1997', '9780747532699', '6', '6', 'Primera novela de la saga Harry Potter.', NULL, 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `libros` VALUES ('4', 'LIB-004', 'Don Quijote de la Mancha', '4', '5', 'Francisco de Robles', '1605', '9788420412146', '3', '3', 'Obra clásica de la literatura española.', NULL, 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `libros` VALUES ('5', 'LIB-005', 'Fundación', '5', '2', 'Gnome Press', '1951', '9780553293357', '4', '4', 'Novela clásica de ciencia ficción.', NULL, 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `libros` VALUES ('6', 'LIB-006', 'Clean Code', '6', '3', 'Prentice Hall', '2008', '9780132350884', '2', '2', 'Libro sobre buenas prácticas de programación.', NULL, 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `libros` VALUES ('9', 'LIB-007', 'Arquitectura Moderna', '1', '1', NULL, NULL, NULL, '1', '5', NULL, NULL, 'ACTIVO', '2026-08-15 22:18:14');
INSERT INTO `libros` VALUES ('10', 'LIB-008', 'DjangoRest', '1', '1', NULL, NULL, NULL, '1', '5', NULL, NULL, 'ACTIVO', '2026-08-15 22:29:14');

-- Table: prestamos
DROP TABLE IF EXISTS `prestamos`;
CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion_programada` date NOT NULL,
  `fecha_devolucion_real` date DEFAULT NULL,
  `estado` enum('ACTIVO','DEVUELTO','ATRASADO') NOT NULL DEFAULT 'ACTIVO',
  `observacion` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_prestamo`),
  KEY `fk_prestamo_usuario` (`id_usuario`),
  KEY `fk_prestamo_libro` (`id_libro`),
  KEY `idx_prestamos_estado` (`estado`),
  CONSTRAINT `fk_prestamo_libro` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id_libro`),
  CONSTRAINT `fk_prestamo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `prestamos` VALUES ('1', '4', '1', '2026-08-15', '2026-08-17', NULL, 'ACTIVO', NULL, '2026-08-15 21:16:31');
INSERT INTO `prestamos` VALUES ('2', '4', '3', '2026-08-15', '2026-08-17', NULL, 'ACTIVO', NULL, '2026-08-15 22:29:17');

-- Table: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `cedula` (`cedula`),
  UNIQUE KEY `correo` (`correo`),
  KEY `idx_usuarios_cedula` (`cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `usuarios` VALUES ('1', '0912345678', 'Juan', 'Pérez', 'juan.perez@email.com', '0991234567', 'Guayaquil', 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `usuarios` VALUES ('2', '0923456789', 'María', 'González', 'maria.gonzalez@email.com', '0987654321', 'Guayaquil', 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `usuarios` VALUES ('3', '0934567890', 'Carlos', 'Rodríguez', 'carlos.rodriguez@email.com', '0971234567', 'Samborondón', 'ACTIVO', '2026-08-15 20:06:49');
INSERT INTO `usuarios` VALUES ('4', '0944234467', 'Jeremy Mike', 'Lopez Arteaga', 'jeloarte@espol.edu.ec', '0991063391', 'AV 43 908B, SO Guayaquil', 'ACTIVO', '2026-08-15 21:08:17');
INSERT INTO `usuarios` VALUES ('5', '0944234475', 'Isaias Elias', 'Lopez Arteaga', 'isaias08@outlook.com', '0919212415', 'AV 43 908B, SO Guayaquil', 'ACTIVO', '2026-08-15 22:29:10');

SET FOREIGN_KEY_CHECKS=1;
