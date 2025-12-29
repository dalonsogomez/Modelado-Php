-- =====================================================
-- BASE DE DATOS: GESTIÓN DE APARTAMENTOS TURÍSTICOS
-- Ingeniería del Software Web - PHP + MySQL
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS apartamentos;
USE apartamentos;

-- =====================================================
-- TABLA: Usuario
-- =====================================================
CREATE TABLE IF NOT EXISTS Usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    NIF VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(50),
    telefono VARCHAR(9)
);

-- =====================================================
-- TABLA: Inmueble
-- =====================================================
CREATE TABLE IF NOT EXISTS Inmueble (
    ID_Inmueble INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(50),
    num_habitaciones INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL
);

-- =====================================================
-- TABLA: Reserva
-- =====================================================
CREATE TABLE IF NOT EXISTS Reserva (
    ID_Reserva INT AUTO_INCREMENT PRIMARY KEY,
    ID_inmueble INT NOT NULL,
    ID_usuario INT NOT NULL,
    fecha_entrada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    FOREIGN KEY (ID_inmueble) REFERENCES Inmueble(ID_Inmueble) ON DELETE CASCADE,
    FOREIGN KEY (ID_usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE
);

-- =====================================================
-- TABLA: Comentario
-- =====================================================
CREATE TABLE IF NOT EXISTS Comentario (
    ID_resena INT AUTO_INCREMENT PRIMARY KEY,
    ID_reserva INT NOT NULL UNIQUE,
    puntuacion DECIMAL(3,1) NOT NULL CHECK (puntuacion >= 0 AND puntuacion <= 10),
    comentario VARCHAR(200),
    FOREIGN KEY (ID_reserva) REFERENCES Reserva(ID_Reserva) ON DELETE CASCADE
);

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Insertar Usuarios
INSERT INTO Usuario (NIF, nombre, direccion, telefono) VALUES
('12345678A', 'Carlos Álvarez', 'Calle Mayor 1, Madrid', '600111222'),
('23456789B', 'María García', 'Avenida Central 25, Barcelona', '600222333'),
('34567890C', 'Juan López', 'Plaza España 10, Sevilla', '600333444'),
('45678901D', 'Ana Martínez', 'Calle Real 15, Valencia', '600444555'),
('56789012E', 'Pedro Sánchez', 'Gran Vía 50, Bilbao', '600555666');

-- Insertar Inmuebles
INSERT INTO Inmueble (nombre, direccion, num_habitaciones, precio) VALUES
('Caserón en Salamanca', 'Calle Toro 45, Salamanca', 4, 120.00),
('Apartamento Playa', 'Paseo Marítimo 12, Málaga', 2, 85.00),
('Ático Centro', 'Gran Vía 78, Madrid', 3, 150.00),
('Casa Rural Sierra', 'Camino Viejo 5, Ávila', 5, 95.00),
('Piso Céntrico', 'Ramblas 30, Barcelona', 2, 110.00),
('Villa con Piscina', 'Urbanización Sol 15, Marbella', 6, 250.00);

-- Insertar algunas Reservas de prueba
INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida) VALUES
(1, 1, '2025-01-15', '2025-01-20'),
(2, 2, '2025-02-01', '2025-02-07'),
(3, 1, '2025-03-10', '2025-03-15'),
(4, 3, '2025-01-25', '2025-01-30');

-- Insertar algunos Comentarios de prueba
INSERT INTO Comentario (ID_reserva, puntuacion, comentario) VALUES
(1, 8.5, 'Excelente estancia, muy recomendable. El trato fue muy bueno.'),
(2, 7.0, 'Buen apartamento, aunque un poco ruidoso por las noches.');
