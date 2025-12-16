-- ============================================
-- BASE DE DATOS: GESTIÓN DE GIMNASIO
-- ============================================
-- Autor: Sistema de Gestión de Gimnasio
-- Fecha: Diciembre 2025
-- Descripción: Sistema completo para gestión de inscripciones en gimnasio
-- ============================================

-- Crear base de datos
DROP DATABASE IF EXISTS gimnasio;
CREATE DATABASE gimnasio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gimnasio;

-- ============================================
-- TABLA: Socio
-- ============================================
-- Registra los socios del gimnasio
CREATE TABLE Socio (
    socioID INT AUTO_INCREMENT PRIMARY KEY,
    nif VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    telefono VARCHAR(9) NOT NULL,
    email VARCHAR(50) NOT NULL,
    CONSTRAINT chk_nif_socio CHECK (nif REGEXP '^[0-9]{8}[A-Z]$'),
    CONSTRAINT chk_telefono_socio CHECK (telefono REGEXP '^[0-9]{9}$'),
    CONSTRAINT chk_email CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Actividad
-- ============================================
-- Almacena datos de las actividades del gimnasio
CREATE TABLE Actividad (
    actividadID INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50) NOT NULL,
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    CONSTRAINT chk_fechas_actividad CHECK (fechaFin > fechaInicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Monitor
-- ============================================
-- Almacena los distintos monitores del gimnasio
CREATE TABLE Monitor (
    monitorID INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Inscripciones
-- ============================================
-- Almacena información sobre las inscripciones
CREATE TABLE Inscripciones (
    inscripcionID INT AUTO_INCREMENT PRIMARY KEY,
    actividadID INT NOT NULL,
    socioID INT NOT NULL,
    monitorID INT NOT NULL,
    fechaInscripcion DATE NOT NULL,
    fechaFinInscripcion DATE NOT NULL,
    precioMensual DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_inscripcion_actividad FOREIGN KEY (actividadID) REFERENCES Actividad(actividadID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inscripcion_socio FOREIGN KEY (socioID) REFERENCES Socio(socioID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inscripcion_monitor FOREIGN KEY (monitorID) REFERENCES Monitor(monitorID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_precio_mensual CHECK (precioMensual >= 100 AND precioMensual <= 1000),
    CONSTRAINT chk_fechas_inscripcion CHECK (fechaFinInscripcion > fechaInscripcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- ============================================
CREATE INDEX idx_inscripcion_socio ON Inscripciones(socioID);
CREATE INDEX idx_inscripcion_actividad ON Inscripciones(actividadID);
CREATE INDEX idx_inscripcion_monitor ON Inscripciones(monitorID);
CREATE INDEX idx_actividad_fechas ON Actividad(fechaInicio, fechaFin);

-- ============================================
-- DATOS DE PRUEBA: Socios
-- ============================================
INSERT INTO Socio (nif, nombre, telefono, email) VALUES
('12345678A', 'Carlos Álvarez', '612345678', 'carlos.alvarez@email.com'),
('23456789B', 'María González', '623456789', 'maria.gonzalez@email.com'),
('34567890C', 'Juan Martínez', '634567890', 'juan.martinez@email.com'),
('45678901D', 'Laura Sánchez', '645678901', 'laura.sanchez@email.com'),
('56789012E', 'Pedro López', '656789012', 'pedro.lopez@email.com'),
('67890123F', 'Ana Fernández', '667890123', 'ana.fernandez@email.com'),
('78901234G', 'David Rodríguez', '678901234', 'david.rodriguez@email.com'),
('89012345H', 'Elena García', '689012345', 'elena.garcia@email.com'),
('90123456I', 'Miguel Torres', '690123456', 'miguel.torres@email.com'),
('01234567J', 'Carmen Ruiz', '601234567', 'carmen.ruiz@email.com');

-- ============================================
-- DATOS DE PRUEBA: Actividades
-- ============================================
INSERT INTO Actividad (nombre, descripcion, fechaInicio, fechaFin) VALUES
('Spinning', 'Clase de ciclismo indoor de alta intensidad', '2024-01-01', '2026-12-31'),
('Yoga', 'Sesiones de yoga para todos los niveles', '2024-01-01', '2026-12-31'),
('Pilates', 'Ejercicios de fortalecimiento y flexibilidad', '2024-02-01', '2026-12-31'),
('Zumba', 'Baile fitness con ritmos latinos', '2024-01-15', '2026-12-31'),
('CrossFit', 'Entrenamiento funcional de alta intensidad', '2024-03-01', '2026-12-31'),
('Natación', 'Clases de natación para adultos', '2024-01-01', '2026-12-31'),
('Boxeo', 'Entrenamiento de boxeo y defensa personal', '2024-02-15', '2026-12-31'),
('Body Pump', 'Tonificación muscular con pesas', '2024-01-01', '2026-12-31'),
('Aerobic', 'Ejercicios aeróbicos con música', '2024-01-10', '2026-12-31'),
('GAP', 'Glúteos, abdominales y piernas', '2024-01-01', '2026-12-31'),
('Actividad Pasada', 'Esta actividad ya finalizó', '2023-01-01', '2023-12-31');

-- ============================================
-- DATOS DE PRUEBA: Monitores
-- ============================================
INSERT INTO Monitor (nombre, descripcion) VALUES
('Roberto Martín', 'Monitor de Spinning'),
('Lucía Pérez', 'Instructora de Yoga'),
('Fernando Gómez', 'Entrenador de Pilates'),
('Sandra Díaz', 'Profesora de Zumba'),
('Alberto Ruiz', 'Coach de CrossFit'),
('Patricia Moreno', 'Monitora de Natación'),
('Javier Sánchez', 'Entrenador de Boxeo'),
('Cristina López', 'Instructora de Body Pump'),
('Manuel García', 'Monitor de Aerobic'),
('Isabel Fernández', 'Entrenadora de GAP');

-- ============================================
-- DATOS DE PRUEBA: Inscripciones
-- ============================================
INSERT INTO Inscripciones (actividadID, socioID, monitorID, fechaInscripcion, fechaFinInscripcion, precioMensual) VALUES
(1, 1, 1, '2024-01-15', '2025-01-15', 450.00),
(2, 2, 2, '2024-02-01', '2025-02-01', 380.00),
(3, 3, 3, '2024-02-15', '2025-02-15', 420.00),
(4, 4, 4, '2024-03-01', '2025-03-01', 350.00),
(5, 5, 5, '2024-03-15', '2025-03-15', 650.00),
(6, 6, 6, '2024-04-01', '2025-04-01', 500.00),
(7, 7, 7, '2024-04-15', '2025-04-15', 550.00),
(8, 8, 8, '2024-05-01', '2025-05-01', 400.00),
(9, 9, 9, '2024-05-15', '2025-05-15', 320.00),
(10, 10, 10, '2024-06-01', '2025-06-01', 380.00),
(1, 2, 1, '2024-06-15', '2025-06-15', 450.00),
(2, 1, 2, '2024-07-01', '2025-07-01', 380.00),
(5, 3, 5, '2024-07-15', '2025-07-15', 650.00);

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: Inscripciones con información completa
CREATE VIEW vista_inscripciones_completas AS
SELECT 
    i.inscripcionID,
    s.nombre AS nombre_socio,
    s.nif,
    s.telefono,
    s.email,
    a.nombre AS nombre_actividad,
    a.descripcion AS descripcion_actividad,
    a.fechaFin AS fecha_fin_actividad,
    m.nombre AS nombre_monitor,
    m.descripcion AS descripcion_monitor,
    i.fechaInscripcion,
    i.fechaFinInscripcion,
    i.precioMensual,
    TIMESTAMPDIFF(MONTH, i.fechaInscripcion, i.fechaFinInscripcion) AS duracion_meses,
    (i.precioMensual * TIMESTAMPDIFF(MONTH, i.fechaInscripcion, i.fechaFinInscripcion)) AS precio_total
FROM Inscripciones i
INNER JOIN Socio s ON i.socioID = s.socioID
INNER JOIN Actividad a ON i.actividadID = a.actividadID
INNER JOIN Monitor m ON i.monitorID = m.monitorID
ORDER BY i.fechaInscripcion DESC;

-- Vista: Actividades activas
CREATE VIEW vista_actividades_activas AS
SELECT 
    actividadID,
    nombre,
    descripcion,
    fechaInicio,
    fechaFin,
    DATEDIFF(fechaFin, CURDATE()) AS dias_restantes
FROM Actividad
WHERE fechaFin >= CURDATE()
ORDER BY nombre;

-- Vista: Estadísticas por actividad
CREATE VIEW vista_estadisticas_actividad AS
SELECT 
    a.actividadID,
    a.nombre,
    a.descripcion,
    COUNT(i.inscripcionID) AS total_inscripciones,
    AVG(i.precioMensual) AS precio_promedio,
    SUM(i.precioMensual * TIMESTAMPDIFF(MONTH, i.fechaInscripcion, i.fechaFinInscripcion)) AS ingresos_totales
FROM Actividad a
LEFT JOIN Inscripciones i ON a.actividadID = i.actividadID
GROUP BY a.actividadID, a.nombre, a.descripcion
ORDER BY total_inscripciones DESC;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

DELIMITER //

-- Procedimiento: Crear inscripción automática
CREATE PROCEDURE crear_inscripcion(
    IN p_socio_id INT,
    IN p_actividad_id INT,
    IN p_monitor_id INT,
    IN p_fecha_inicio DATE,
    OUT p_inscripcion_id INT,
    OUT p_precio_mensual DECIMAL(10,2)
)
BEGIN
    DECLARE v_fecha_fin DATE;
    
    -- Calcular fecha fin (1 año después)
    SET v_fecha_fin = DATE_ADD(p_fecha_inicio, INTERVAL 1 YEAR);
    
    -- Generar precio aleatorio entre 100 y 1000
    SET p_precio_mensual = FLOOR(100 + (RAND() * 900));
    
    -- Insertar inscripción
    INSERT INTO Inscripciones (actividadID, socioID, monitorID, fechaInscripcion, fechaFinInscripcion, precioMensual)
    VALUES (p_actividad_id, p_socio_id, p_monitor_id, p_fecha_inicio, v_fecha_fin, p_precio_mensual);
    
    -- Obtener ID de la inscripción creada
    SET p_inscripcion_id = LAST_INSERT_ID();
END //

-- Procedimiento: Obtener inscripciones de un socio
CREATE PROCEDURE obtener_inscripciones_socio(IN p_socio_id INT)
BEGIN
    SELECT * FROM vista_inscripciones_completas
    WHERE nif = (SELECT nif FROM Socio WHERE socioID = p_socio_id)
    ORDER BY fechaInscripcion DESC;
END //

-- Procedimiento: Verificar disponibilidad de actividad
CREATE PROCEDURE verificar_actividad_activa(
    IN p_actividad_id INT,
    OUT p_activa BOOLEAN
)
BEGIN
    DECLARE v_fecha_fin DATE;
    
    SELECT fechaFin INTO v_fecha_fin
    FROM Actividad
    WHERE actividadID = p_actividad_id;
    
    IF v_fecha_fin >= CURDATE() THEN
        SET p_activa = TRUE;
    ELSE
        SET p_activa = FALSE;
    END IF;
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger: Validar fecha de inscripción antes de insertar
CREATE TRIGGER trg_validar_fecha_inscripcion
BEFORE INSERT ON Inscripciones
FOR EACH ROW
BEGIN
    -- Verificar que la fecha de inscripción no sea anterior a hoy
    IF NEW.fechaInscripcion < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de inscripción no puede ser anterior a la fecha actual';
    END IF;
    
    -- Verificar que la actividad esté activa
    IF (SELECT fechaFin FROM Actividad WHERE actividadID = NEW.actividadID) < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede inscribir en una actividad que ya ha finalizado';
    END IF;
END //

DELIMITER ;

-- ============================================
-- CONSULTAS ÚTILES PARA LA APLICACIÓN
-- ============================================

-- Consulta: Actividades activas con formato para desplegable
-- SELECT 
--     actividadID,
--     CONCAT(nombre, ' (', DATE_FORMAT(fechaFin, '%d/%m/%Y'), ')') AS display_text
-- FROM Actividad
-- WHERE fechaFin >= CURDATE()
-- ORDER BY nombre;

-- Consulta: Monitores con formato para desplegable
-- SELECT 
--     monitorID,
--     descripcion AS display_text
-- FROM Monitor
-- ORDER BY descripcion;

-- Consulta: Socios con formato para desplegable
-- SELECT 
--     socioID,
--     CONCAT(nombre, ' (', nif, ')') AS display_text
-- FROM Socio
-- ORDER BY nombre;

-- Consulta: Inscripciones de un socio específico
-- SELECT * FROM vista_inscripciones_completas WHERE nif = '12345678A';

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
