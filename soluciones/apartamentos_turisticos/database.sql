-- ============================================
-- BASE DE DATOS: GESTIÓN DE APARTAMENTOS TURÍSTICOS
-- ============================================
-- Autor: Sistema de Gestión de Reservas
-- Fecha: Diciembre 2025
-- Descripción: Sistema completo para gestión de reservas de apartamentos turísticos
-- ============================================

-- Crear base de datos
DROP DATABASE IF EXISTS apartamentos_turisticos;
CREATE DATABASE apartamentos_turisticos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE apartamentos_turisticos;

-- ============================================
-- TABLA: Usuario
-- ============================================
-- Almacena información de los usuarios/clientes del sistema
CREATE TABLE Usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    NIF VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(50) NOT NULL,
    telefono VARCHAR(9) NOT NULL,
    CONSTRAINT chk_nif_usuario CHECK (NIF REGEXP '^[0-9]{8}[A-Z]$'),
    CONSTRAINT chk_telefono_usuario CHECK (telefono REGEXP '^[0-9]{9}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Inmueble
-- ============================================
-- Registra los inmuebles disponibles para alquiler
CREATE TABLE Inmueble (
    ID_Inmueble INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(50) NOT NULL,
    num_habitaciones INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    CONSTRAINT chk_habitaciones CHECK (num_habitaciones > 0),
    CONSTRAINT chk_precio CHECK (precio > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Reserva
-- ============================================
-- Almacena las reservas realizadas por los usuarios
CREATE TABLE Reserva (
    ID_Reserva INT AUTO_INCREMENT PRIMARY KEY,
    ID_inmueble INT NOT NULL,
    ID_usuario INT NOT NULL,
    fecha_entrada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_reserva_inmueble FOREIGN KEY (ID_inmueble) REFERENCES Inmueble(ID_Inmueble) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reserva_usuario FOREIGN KEY (ID_usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_fechas CHECK (fecha_salida > fecha_entrada),
    CONSTRAINT chk_precio_total CHECK (precio_total > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: Comentario
-- ============================================
-- Almacena comentarios y valoraciones sobre las reservas
CREATE TABLE Comentario (
    ID_resena INT AUTO_INCREMENT PRIMARY KEY,
    ID_reserva INT NOT NULL,
    puntuacion DECIMAL(3,1) NOT NULL,
    comentario VARCHAR(200) NOT NULL,
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comentario_reserva FOREIGN KEY (ID_reserva) REFERENCES Reserva(ID_Reserva) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_puntuacion CHECK (puntuacion >= 0 AND puntuacion <= 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- ============================================
CREATE INDEX idx_reserva_usuario ON Reserva(ID_usuario);
CREATE INDEX idx_reserva_inmueble ON Reserva(ID_inmueble);
CREATE INDEX idx_reserva_fechas ON Reserva(fecha_entrada, fecha_salida);
CREATE INDEX idx_comentario_reserva ON Comentario(ID_reserva);

-- ============================================
-- DATOS DE PRUEBA: Usuarios
-- ============================================
INSERT INTO Usuario (NIF, nombre, direccion, telefono) VALUES
('12345678A', 'Juan Pérez García', 'Calle Mayor 15, Madrid', '612345678'),
('23456789B', 'María López Martínez', 'Avenida Libertad 23, Barcelona', '623456789'),
('34567890C', 'Carlos Sánchez Ruiz', 'Plaza España 8, Valencia', '634567890'),
('45678901D', 'Ana Fernández Torres', 'Calle Sol 42, Sevilla', '645678901'),
('56789012E', 'Pedro Gómez Díaz', 'Avenida Constitución 67, Bilbao', '656789012'),
('67890123F', 'Laura Martín Jiménez', 'Calle Luna 19, Málaga', '667890123'),
('78901234G', 'David Rodríguez Moreno', 'Plaza Mayor 5, Zaragoza', '678901234'),
('89012345H', 'Elena García Álvarez', 'Avenida del Mar 88, Alicante', '689012345');

-- ============================================
-- DATOS DE PRUEBA: Inmuebles
-- ============================================
INSERT INTO Inmueble (nombre, direccion, num_habitaciones, precio) VALUES
('Caserón en Salamanca', 'Plaza Mayor 12, Salamanca', 5, 150.00),
('Apartamento Playa Benidorm', 'Avenida Mediterráneo 45, Benidorm', 2, 80.00),
('Chalet Sierra Madrid', 'Urbanización El Pinar 23, Cercedilla', 4, 200.00),
('Piso Centro Barcelona', 'Rambla Catalunya 78, Barcelona', 3, 120.00),
('Villa Costa del Sol', 'Paseo Marítimo 156, Marbella', 6, 300.00),
('Estudio Granada Centro', 'Calle Reyes Católicos 34, Granada', 1, 60.00),
('Dúplex Valencia Puerto', 'Marina Real Juan Carlos I, Valencia', 3, 140.00),
('Casa Rural Asturias', 'Pueblo de Covadonga s/n, Asturias', 4, 110.00),
('Ático Sevilla Triana', 'Calle Betis 89, Sevilla', 2, 95.00),
('Loft Bilbao Casco Viejo', 'Plaza Nueva 7, Bilbao', 1, 85.00);

-- ============================================
-- DATOS DE PRUEBA: Reservas
-- ============================================
INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida, precio_total) VALUES
(1, 1, '2024-01-15', '2024-01-20', 750.00),
(2, 2, '2024-02-10', '2024-02-17', 560.00),
(3, 3, '2024-03-05', '2024-03-12', 1400.00),
(4, 1, '2024-04-20', '2024-04-25', 600.00),
(5, 4, '2024-05-15', '2024-05-22', 2100.00),
(6, 5, '2024-06-01', '2024-06-08', 420.00),
(7, 2, '2024-07-10', '2024-07-17', 980.00),
(8, 6, '2024-08-05', '2024-08-12', 770.00),
(9, 7, '2024-09-15', '2024-09-20', 475.00),
(10, 8, '2024-10-01', '2024-10-05', 340.00),
(1, 3, '2024-11-10', '2024-11-15', 750.00),
(2, 4, '2024-12-20', '2024-12-27', 560.00);

-- ============================================
-- DATOS DE PRUEBA: Comentarios
-- ============================================
INSERT INTO Comentario (ID_reserva, puntuacion, comentario) VALUES
(1, 9.5, 'Excelente estancia, el caserón es precioso y muy bien ubicado.'),
(2, 8.0, 'Muy buena experiencia, apartamento limpio y cerca de la playa.'),
(3, 9.0, 'Chalet espectacular, perfecto para desconectar en la sierra.'),
(4, 7.5, 'Bien ubicado pero un poco ruidoso por la noche.'),
(5, 10.0, 'Villa increíble, superó todas nuestras expectativas. Volveremos seguro.'),
(6, 6.5, 'Estudio pequeño pero funcional, buena relación calidad-precio.'),
(7, 8.5, 'Dúplex moderno con vistas al puerto, muy recomendable.'),
(8, 9.2, 'Casa rural encantadora, entorno natural maravilloso.'),
(9, 8.8, 'Ático con vistas al río, ubicación perfecta en Triana.'),
(10, 7.8, 'Loft bien equipado, ideal para una escapada urbana.');

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: Reservas con información completa
CREATE VIEW vista_reservas_completas AS
SELECT 
    r.ID_Reserva,
    u.nombre AS nombre_usuario,
    u.NIF,
    u.telefono,
    i.nombre AS nombre_inmueble,
    i.direccion AS direccion_inmueble,
    i.num_habitaciones,
    i.precio AS precio_noche,
    r.fecha_entrada,
    r.fecha_salida,
    DATEDIFF(r.fecha_salida, r.fecha_entrada) AS num_noches,
    r.precio_total
FROM Reserva r
INNER JOIN Usuario u ON r.ID_usuario = u.ID_Usuario
INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
ORDER BY r.fecha_entrada DESC;

-- Vista: Comentarios con información de reserva
CREATE VIEW vista_comentarios_completos AS
SELECT 
    c.ID_resena,
    c.puntuacion,
    c.comentario,
    c.fecha_comentario,
    r.ID_Reserva,
    u.nombre AS nombre_usuario,
    i.nombre AS nombre_inmueble,
    r.fecha_entrada,
    r.fecha_salida
FROM Comentario c
INNER JOIN Reserva r ON c.ID_reserva = r.ID_Reserva
INNER JOIN Usuario u ON r.ID_usuario = u.ID_Usuario
INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
ORDER BY c.fecha_comentario DESC;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

DELIMITER //

-- Procedimiento: Calcular precio total de reserva
CREATE PROCEDURE calcular_precio_reserva(
    IN p_id_inmueble INT,
    IN p_fecha_entrada DATE,
    IN p_fecha_salida DATE,
    OUT p_precio_total DECIMAL(10,2)
)
BEGIN
    DECLARE v_precio_noche DECIMAL(10,2);
    DECLARE v_num_noches INT;
    
    -- Obtener precio por noche del inmueble
    SELECT precio INTO v_precio_noche
    FROM Inmueble
    WHERE ID_Inmueble = p_id_inmueble;
    
    -- Calcular número de noches
    SET v_num_noches = DATEDIFF(p_fecha_salida, p_fecha_entrada);
    
    -- Calcular precio total
    SET p_precio_total = v_precio_noche * v_num_noches;
END //

-- Procedimiento: Obtener estadísticas de usuario
CREATE PROCEDURE estadisticas_usuario(IN p_id_usuario INT)
BEGIN
    SELECT 
        COUNT(*) AS total_reservas,
        SUM(precio_total) AS gasto_total,
        AVG(precio_total) AS gasto_promedio,
        MIN(fecha_entrada) AS primera_reserva,
        MAX(fecha_salida) AS ultima_reserva
    FROM Reserva
    WHERE ID_usuario = p_id_usuario;
END //

DELIMITER ;

-- ============================================
-- CONSULTAS ÚTILES PARA LA APLICACIÓN
-- ============================================

-- Consulta: Inmuebles disponibles (sin reservas en un rango de fechas)
-- SELECT i.* FROM Inmueble i
-- WHERE i.ID_Inmueble NOT IN (
--     SELECT r.ID_inmueble FROM Reserva r
--     WHERE (r.fecha_entrada <= '2025-12-31' AND r.fecha_salida >= '2025-12-20')
-- );

-- Consulta: Reservas de un usuario específico
-- SELECT * FROM vista_reservas_completas WHERE NIF = '12345678A';

-- Consulta: Promedio de puntuación por inmueble
-- SELECT 
--     i.nombre,
--     AVG(c.puntuacion) AS puntuacion_promedio,
--     COUNT(c.ID_resena) AS num_comentarios
-- FROM Inmueble i
-- LEFT JOIN Reserva r ON i.ID_Inmueble = r.ID_inmueble
-- LEFT JOIN Comentario c ON r.ID_Reserva = c.ID_reserva
-- GROUP BY i.ID_Inmueble, i.nombre;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
