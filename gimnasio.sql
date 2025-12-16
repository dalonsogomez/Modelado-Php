-- Base de datos para la gestión de un gimnasio

--
-- Estructura de tabla para la tabla `Socio`
--

CREATE TABLE `Socio` (
  `socioID` int(11) NOT NULL AUTO_INCREMENT,
  `nif` varchar(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`socioID`),
  UNIQUE KEY `nif` (`nif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Socio`
--

INSERT INTO `Socio` (`nif`, `nombre`, `telefono`, `email`) VALUES
('12345678A', 'Carlos Álvarez', '611223344', 'carlos.alvarez@email.com'),
('55667788B', 'María Jiménez', '655667788', 'maria.jimenez@email.com'),
('99887766C', 'Pedro Sánchez', '699887766', 'pedro.sanchez@email.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Actividad`
--

CREATE TABLE `Actividad` (
  `actividadID` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  PRIMARY KEY (`actividadID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Actividad`
--

INSERT INTO `Actividad` (`nombre`, `descripcion`, `fechaInicio`, `fechaFin`) VALUES
('Spinning Mañanas', 'Clase de spinning a primera hora', '2023-01-01', '2026-12-31'),
('Yoga Terapéutico', 'Yoga suave para rehabilitación', '2023-01-01', '2026-12-31'),
('Boxeo Iniciación', 'Curso de boxeo para principiantes', '2023-01-01', '2024-06-30'); -- Actividad pasada para pruebas

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Monitor`
--

CREATE TABLE `Monitor` (
  `monitorID` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`monitorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Monitor`
--

INSERT INTO `Monitor` (`nombre`, `descripcion`) VALUES
('Javier García', 'Monitor de Spinning'),
('Laura Vidal', 'Monitora de Yoga'),
('Miguel Fernández', 'Monitor de Boxeo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inscripciones`
--

CREATE TABLE `Inscripciones` (
  `inscripcionID` int(11) NOT NULL AUTO_INCREMENT,
  `actividadID` int(11) NOT NULL,
  `socioID` int(11) NOT NULL,
  `monitorID` int(11) NOT NULL,
  `fechaInscripcion` date NOT NULL,
  `precioMensual` float NOT NULL,
  PRIMARY KEY (`inscripcionID`),
  KEY `actividadID` (`actividadID`),
  KEY `socioID` (`socioID`),
  KEY `monitorID` (`monitorID`),
  CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`actividadID`) REFERENCES `Actividad` (`actividadID`),
  CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`socioID`) REFERENCES `Socio` (`socioID`),
  CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`monitorID`) REFERENCES `Monitor` (`monitorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Inscripciones` (inscripciones pasadas)
--

INSERT INTO `Inscripciones` (`actividadID`, `socioID`, `monitorID`, `fechaInscripcion`, `precioMensual`) VALUES
(1, 1, 1, '2023-02-15', 35.50),
(3, 1, 3, '2023-03-01', 40.00);

