-- =====================================================
-- BASE DE DATOS: GESTIÓN DE GIMNASIO
-- Ingeniería del Software Web - PHP + MySQL
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS gimnasio;
USE gimnasio;

-- =====================================================
-- TABLA: Actividad
-- =====================================================
CREATE TABLE IF NOT EXISTS Actividad (
    actividadID INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50),
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL
);

-- =====================================================
-- TABLA: Socio
-- =====================================================
CREATE TABLE IF NOT EXISTS Socio (
    socioID INT AUTO_INCREMENT PRIMARY KEY,
    nif VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    telefono VARCHAR(9),
    email VARCHAR(50)
);

-- =====================================================
-- TABLA: Monitor
-- =====================================================
CREATE TABLE IF NOT EXISTS Monitor (
    monitorID INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50)
);

-- =====================================================
-- TABLA: Inscripciones
-- Clave primaria compuesta (actividadID, socioID, monitorID)
-- =====================================================
CREATE TABLE IF NOT EXISTS Inscripciones (
    actividadID INT NOT NULL,
    socioID INT NOT NULL,
    monitorID INT NOT NULL,
    fechaInscripcion DATE NOT NULL,
    precioMensual DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (actividadID, socioID, monitorID),
    FOREIGN KEY (actividadID) REFERENCES Actividad(actividadID) ON DELETE CASCADE,
    FOREIGN KEY (socioID) REFERENCES Socio(socioID) ON DELETE CASCADE,
    FOREIGN KEY (monitorID) REFERENCES Monitor(monitorID) ON DELETE CASCADE
);

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Insertar Actividades
INSERT INTO Actividad (nombre, descripcion, fechaInicio, fechaFin) VALUES
('Spinning', 'Ciclismo en sala', '2025-01-01', '2025-12-31'),
('Yoga', 'Relajación y flexibilidad', '2025-01-15', '2025-11-30'),
('CrossFit', 'Entrenamiento funcional', '2025-02-01', '2025-12-15'),
('Pilates', 'Fortalecimiento core', '2025-03-01', '2026-02-28'),
('Zumba', 'Baile fitness', '2024-06-01', '2024-12-31');

-- Insertar Socios
INSERT INTO Socio (nif, nombre, telefono, email) VALUES
('12345678A', 'Carlos Álvarez', '600111222', 'carlos@email.com'),
('23456789B', 'María García', '600222333', 'maria@email.com'),
('34567890C', 'Juan López', '600333444', 'juan@email.com'),
('45678901D', 'Ana Martínez', '600444555', 'ana@email.com'),
('56789012E', 'Pedro Sánchez', '600555666', 'pedro@email.com');

-- Insertar Monitores
INSERT INTO Monitor (nombre, descripcion) VALUES
('Laura Fernández', 'Monitor de Spinning'),
('Roberto García', 'Monitor de Yoga'),
('Carmen Ruiz', 'Monitor de CrossFit'),
('Miguel Torres', 'Monitor de Pilates'),
('Sofía Moreno', 'Monitor de Zumba');

-- Insertar Inscripciones de prueba
INSERT INTO Inscripciones (actividadID, socioID, monitorID, fechaInscripcion, precioMensual) VALUES
(1, 1, 1, '2025-01-10', 250.50),
(2, 2, 2, '2025-01-20', 180.00),
(3, 3, 3, '2025-02-15', 320.75);
