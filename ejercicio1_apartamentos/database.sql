-- ============================================
-- EJERCICIO 1: GESTIÓN DE RESERVAS DE APARTAMENTOS TURÍSTICOS
-- Base de datos para sistema de alquiler de apartamentos
-- ============================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS apartamentos_turisticos;
USE apartamentos_turisticos;

-- ============================================
-- TABLA: Usuario
-- Almacena información de los usuarios del sistema
-- ============================================
CREATE TABLE Usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    NIF VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(50),
    telefono VARCHAR(9),
    CONSTRAINT chk_nif CHECK (LENGTH(NIF) = 9),
    CONSTRAINT chk_telefono CHECK (LENGTH(telefono) = 9)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: Inmueble
-- Registra los apartamentos disponibles para alquiler
-- ============================================
CREATE TABLE Inmueble (
    ID_Inmueble INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(50) NOT NULL,
    num_habitaciones INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    CONSTRAINT chk_habitaciones CHECK (num_habitaciones > 0),
    CONSTRAINT chk_precio CHECK (precio > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: Reserva
-- Almacena las reservas realizadas por los usuarios
-- ============================================
CREATE TABLE Reserva (
    ID_Reserva INT AUTO_INCREMENT PRIMARY KEY,
    ID_inmueble INT NOT NULL,
    ID_usuario INT NOT NULL,
    fecha_entrada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    FOREIGN KEY (ID_inmueble) REFERENCES Inmueble(ID_Inmueble) ON DELETE CASCADE,
    FOREIGN KEY (ID_usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE,
    CONSTRAINT chk_fechas CHECK (fecha_salida > fecha_entrada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: Comentario
-- Almacena las valoraciones y comentarios sobre el servicio
-- ============================================
CREATE TABLE Comentario (
    ID_resena INT AUTO_INCREMENT PRIMARY KEY,
    ID_reserva INT NOT NULL,
    puntuacion DECIMAL(3,1) NOT NULL,
    comentario VARCHAR(200),
    FOREIGN KEY (ID_reserva) REFERENCES Reserva(ID_Reserva) ON DELETE CASCADE,
    CONSTRAINT chk_puntuacion CHECK (puntuacion >= 0 AND puntuacion <= 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATOS DE PRUEBA
-- ============================================

-- Insertar usuarios de prueba
INSERT INTO Usuario (NIF, nombre, direccion, telefono) VALUES
('12345678A', 'Juan Pérez García', 'C/ Mayor 15, Madrid', '654321987'),
('23456789B', 'María López Ruiz', 'Avda. Constitución 23, Sevilla', '612345678'),
('34567890C', 'Pedro Martínez Sanz', 'C/ Alcalá 45, Madrid', '698765432'),
('45678901D', 'Ana García Torres', 'C/ Gran Vía 78, Barcelona', '645678901'),
('56789012E', 'Carlos Rodríguez Vega', 'Paseo Gracia 12, Barcelona', '687654321');

-- Insertar inmuebles de prueba
INSERT INTO Inmueble (nombre, direccion, num_habitaciones, precio) VALUES
('Apartamento Centro Madrid', 'C/ Sol 10, Madrid', 2, 85.50),
('Ático con vistas Barcelona', 'Ramblas 25, Barcelona', 3, 120.00),
('Estudio Plaza Mayor', 'Plaza Mayor 5, Madrid', 1, 65.00),
('Piso familiar Sevilla', 'C/ Sierpes 30, Sevilla', 4, 95.75),
('Loft moderno Valencia', 'C/ Colón 18, Valencia', 2, 78.00),
('Caserón en Salamanca', 'Plaza Mayor 3, Salamanca', 5, 150.00),
('Apartamento playa Málaga', 'Paseo Marítimo 45, Málaga', 2, 90.00);

-- Insertar reservas de prueba (fechas pasadas para tener historial)
INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida) VALUES
(1, 1, '2024-01-15', '2024-01-20'),
(2, 2, '2024-02-10', '2024-02-15'),
(1, 3, '2024-03-05', '2024-03-10'),
(3, 1, '2024-04-20', '2024-04-25'),
(4, 4, '2024-05-15', '2024-05-22'),
(5, 2, '2024-06-10', '2024-06-17'),
(2, 5, '2024-07-01', '2024-07-10'),
(6, 3, '2024-08-15', '2024-08-25');

-- Insertar comentarios de prueba
INSERT INTO Comentario (ID_reserva, puntuacion, comentario) VALUES
(1, 8.5, 'Excelente ubicación, apartamento limpio y cómodo'),
(2, 9.0, 'Vistas espectaculares, muy recomendable'),
(3, 7.5, 'Buen apartamento aunque un poco ruidoso'),
(4, 8.0, 'Perfecto para una estancia corta en el centro'),
(5, 9.5, 'Apartamento familiar muy espacioso, todo perfecto'),
(6, 8.8, 'Moderno y bien equipado, gran experiencia');

-- ============================================
-- CONSULTAS ÚTILES PARA EL SISTEMA
-- ============================================

-- Ver todos los usuarios
-- SELECT * FROM Usuario;

-- Ver todos los inmuebles disponibles
-- SELECT ID_Inmueble, nombre, direccion, num_habitaciones, precio
-- FROM Inmueble ORDER BY nombre;

-- Ver reservas de un usuario específico
-- SELECT r.ID_Reserva, i.nombre, r.fecha_entrada, r.fecha_salida,
--        DATEDIFF(r.fecha_salida, r.fecha_entrada) as dias,
--        i.precio * DATEDIFF(r.fecha_salida, r.fecha_entrada) as precio_total
-- FROM Reserva r
-- INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
-- WHERE r.ID_usuario = 1
-- ORDER BY r.fecha_entrada DESC;

-- Ver comentarios de una reserva
-- SELECT c.puntuacion, c.comentario
-- FROM Comentario c
-- WHERE c.ID_reserva = 1;
