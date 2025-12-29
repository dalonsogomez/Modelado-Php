-- =====================================================
-- BASE DE DATOS: GESTIÓN HOTELERA
-- Ingeniería del Software Web - PHP + MySQL
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS hotel;
USE hotel;

-- =====================================================
-- TABLA: TiposHabitacion
-- =====================================================
CREATE TABLE IF NOT EXISTS TiposHabitacion (
    tipoHabitacionID INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL,
    precioBase DECIMAL(10,2) NOT NULL
);

-- =====================================================
-- TABLA: Habitaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS Habitaciones (
    habitacionID INT AUTO_INCREMENT PRIMARY KEY,
    numeroHabitacion VARCHAR(10) NOT NULL UNIQUE,
    tipoHabitacionID INT NOT NULL,
    FOREIGN KEY (tipoHabitacionID) REFERENCES TiposHabitacion(tipoHabitacionID) ON DELETE CASCADE
);

-- =====================================================
-- TABLA: Clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS Clientes (
    clienteID INT AUTO_INCREMENT PRIMARY KEY,
    nif VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    telefono VARCHAR(9),
    email VARCHAR(50),
    direccion VARCHAR(50),
    tarjetaCredito VARCHAR(20),
    contrasena VARCHAR(255) NOT NULL
);

-- =====================================================
-- TABLA: Reservas
-- =====================================================
CREATE TABLE IF NOT EXISTS Reservas (
    reservaID INT AUTO_INCREMENT PRIMARY KEY,
    habitacionID INT NOT NULL,
    clienteID INT NOT NULL,
    fechaReserva DATE NOT NULL,
    fechaEntrada DATE NOT NULL,
    fechaSalida DATE NOT NULL,
    importeTotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (habitacionID) REFERENCES Habitaciones(habitacionID) ON DELETE CASCADE,
    FOREIGN KEY (clienteID) REFERENCES Clientes(clienteID) ON DELETE CASCADE
);

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Insertar Tipos de Habitación
INSERT INTO TiposHabitacion (descripcion, precioBase) VALUES
('Individual', 45.00),
('Doble', 75.00),
('Suite', 150.00),
('Superior', 120.00),
('Familiar', 95.00);

-- Insertar Habitaciones
INSERT INTO Habitaciones (numeroHabitacion, tipoHabitacionID) VALUES
('101', 1), ('102', 1), ('103', 1),
('201', 2), ('202', 2), ('203', 2), ('204', 2),
('301', 3), ('302', 3),
('401', 4), ('402', 4), ('403', 4),
('501', 5), ('502', 5);

-- Insertar Clientes (contraseña: password123 hasheada)
INSERT INTO Clientes (nif, nombre, telefono, email, direccion, tarjetaCredito, contrasena) VALUES
('12345678A', 'Carlos Álvarez', '600111222', 'carlos@email.com', 'Calle Mayor 1', '4111111111111111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('23456789B', 'María García', '600222333', 'maria@email.com', 'Avenida Central 25', '4222222222222222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('34567890C', 'Juan López', '600333444', 'juan@email.com', 'Plaza España 10', '4333333333333333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertar algunas Reservas de prueba
INSERT INTO Reservas (habitacionID, clienteID, fechaReserva, fechaEntrada, fechaSalida, importeTotal) VALUES
(1, 1, '2025-01-10', '2025-02-01', '2025-02-05', 162.00),
(4, 2, '2025-01-15', '2025-03-10', '2025-03-15', 337.50);
