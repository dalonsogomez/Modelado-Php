-- ============================================
-- EJERCICIO 2: GESTIÓN DE UN GIMNASIO
-- Base de datos para sistema de gestión de gimnasio
-- ============================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS gimnasio;
USE gimnasio;

-- ============================================
-- TABLA: Socio
-- Registra los socios del gimnasio
-- ============================================
CREATE TABLE Socio (
    socioID INT AUTO_INCREMENT PRIMARY KEY,
    nif VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    telefono VARCHAR(9),
    email VARCHAR(50),
    CONSTRAINT chk_nif_socio CHECK (LENGTH(nif) = 9),
    CONSTRAINT chk_telefono_socio CHECK (telefono IS NULL OR LENGTH(telefono) = 9)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: Actividad
-- Almacena las actividades del gimnasio
-- ============================================
CREATE TABLE Actividad (
    actividadID INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50),
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    CONSTRAINT chk_fechas_actividad CHECK (fechaFin >= fechaInicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: Monitor
-- Almacena los monitores del gimnasio
-- ============================================
CREATE TABLE Monitor (
    monitorID INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: Inscripciones
-- Almacena las inscripciones de socios en actividades
-- ============================================
CREATE TABLE Inscripciones (
    inscripcionID INT AUTO_INCREMENT PRIMARY KEY,
    actividadID INT NOT NULL,
    socioID INT NOT NULL,
    monitorID INT NOT NULL,
    fechaInscripcion DATE NOT NULL,
    precioMensual DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (actividadID) REFERENCES Actividad(actividadID) ON DELETE CASCADE,
    FOREIGN KEY (socioID) REFERENCES Socio(socioID) ON DELETE CASCADE,
    FOREIGN KEY (monitorID) REFERENCES Monitor(monitorID) ON DELETE CASCADE,
    CONSTRAINT chk_precio CHECK (precioMensual >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATOS DE PRUEBA
-- ============================================

-- Insertar socios de prueba
INSERT INTO Socio (nif, nombre, telefono, email) VALUES
('11111111A', 'Carlos Fernández López', '611222333', 'carlos.fernandez@email.com'),
('22222222B', 'Laura Martínez Ruiz', '622333444', 'laura.martinez@email.com'),
('33333333C', 'David García Sánchez', '633444555', 'david.garcia@email.com'),
('44444444D', 'Elena Rodríguez Torres', '644555666', 'elena.rodriguez@email.com'),
('55555555E', 'Miguel Ángel Pérez Vega', '655666777', 'miguel.perez@email.com');

-- Insertar monitores de prueba
INSERT INTO Monitor (nombre, descripcion) VALUES
('Juan Carlos', 'Monitor de Spinning'),
('María José', 'Monitor de Yoga'),
('Pedro Luis', 'Monitor de Pilates'),
('Ana Belén', 'Entrenador Personal'),
('Roberto Carlos', 'Monitor de Zumba'),
('Isabel María', 'Monitor de CrossFit');

-- Insertar actividades de prueba (algunas activas, otras finalizadas)
INSERT INTO Actividad (nombre, descripcion, fechaInicio, fechaFin) VALUES
('Spinning Mañana', 'Clases de spinning de 7:00 a 8:00', '2024-01-01', '2025-12-31'),
('Yoga Relajante', 'Sesiones de yoga para principiantes', '2024-02-01', '2025-12-31'),
('Pilates Avanzado', 'Clases avanzadas de pilates', '2024-03-01', '2025-12-31'),
('CrossFit Intensivo', 'Entrenamiento de alta intensidad', '2024-01-15', '2025-12-31'),
('Zumba Fitness', 'Baile y ejercicio cardiovascular', '2024-04-01', '2025-12-31'),
('Entrenamiento Personal', 'Sesiones personalizadas', '2024-01-01', '2025-12-31'),
('Spinning Tarde', 'Clases de spinning de 18:00 a 19:00', '2023-01-01', '2023-12-31');

-- Insertar inscripciones de prueba
INSERT INTO Inscripciones (actividadID, socioID, monitorID, fechaInscripcion, precioMensual) VALUES
(1, 1, 1, '2024-01-15', 450.00),
(2, 2, 2, '2024-02-10', 380.50),
(3, 1, 3, '2024-03-20', 520.75),
(4, 3, 6, '2024-02-05', 680.00),
(5, 4, 5, '2024-04-15', 420.25),
(6, 5, 4, '2024-01-20', 850.00);

-- ============================================
-- CONSULTAS ÚTILES PARA EL SISTEMA
-- ============================================

-- Ver todos los socios
-- SELECT * FROM Socio ORDER BY nombre;

-- Ver actividades activas (fechaFin >= CURDATE())
-- SELECT actividadID, nombre, descripcion, fechaInicio, fechaFin
-- FROM Actividad
-- WHERE fechaFin >= CURDATE()
-- ORDER BY nombre;

-- Ver monitores disponibles
-- SELECT * FROM Monitor ORDER BY descripcion;

-- Ver inscripciones de un socio específico
-- SELECT i.inscripcionID, a.nombre as actividad, m.descripcion as monitor,
--        i.fechaInscripcion, i.precioMensual
-- FROM Inscripciones i
-- INNER JOIN Actividad a ON i.actividadID = a.actividadID
-- INNER JOIN Monitor m ON i.monitorID = m.monitorID
-- WHERE i.socioID = 1
-- ORDER BY i.fechaInscripcion DESC;
