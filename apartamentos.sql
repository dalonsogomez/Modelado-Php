-- Base de datos para la gestión de apartamentos turísticos

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE `Usuario` (
  `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT,
  `NIF` varchar(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  PRIMARY KEY (`ID_Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Usuario`
--

INSERT INTO `Usuario` (`NIF`, `nombre`, `direccion`, `telefono`) VALUES
('12345678A', 'Juan Pérez', 'Calle Falsa 123', '611111111'),
('87654321B', 'Ana López', 'Avenida Siempreviva 742', '622222222'),
('11223344C', 'Carlos García', 'Plaza Mayor 1', '633333333');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inmueble`
--

CREATE TABLE `Inmueble` (
  `ID_Inmueble` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `num_habitaciones` int(11) NOT NULL,
  `precio` float NOT NULL,
  PRIMARY KEY (`ID_Inmueble`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Inmueble`
--

INSERT INTO `Inmueble` (`nombre`, `direccion`, `num_habitaciones`, `precio`) VALUES
('Caserón en Salamanca', 'Calle de los Caídos 10', 5, 120.50),
('Piso en el centro', 'Gran Vía 25', 2, 75.00),
('Apartamento con vistas', 'Paseo Marítimo 3', 1, 90.75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Reserva`
--

CREATE TABLE `Reserva` (
  `ID_Reserva` int(11) NOT NULL AUTO_INCREMENT,
  `ID_inmueble` int(11) NOT NULL,
  `ID_usuario` int(11) NOT NULL,
  `fecha_entrada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  PRIMARY KEY (`ID_Reserva`),
  KEY `ID_inmueble` (`ID_inmueble`),
  KEY `ID_usuario` (`ID_usuario`),
  CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`ID_inmueble`) REFERENCES `Inmueble` (`ID_Inmueble`),
  CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`ID_usuario`) REFERENCES `Usuario` (`ID_Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Reserva` (reservas pasadas)
--

INSERT INTO `Reserva` (`ID_inmueble`, `ID_usuario`, `fecha_entrada`, `fecha_salida`) VALUES
(1, 1, '2023-01-10', '2023-01-15'),
(2, 2, '2023-03-20', '2023-03-25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Comentario`
--

CREATE TABLE `Comentario` (
  `ID_reseña` int(11) NOT NULL AUTO_INCREMENT,
  `ID_reserva` int(11) NOT NULL,
  `puntuacion` float NOT NULL,
  `comentario` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID_reseña`),
  KEY `ID_reserva` (`ID_reserva`),
  CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`ID_reserva`) REFERENCES `Reserva` (`ID_Reserva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `Comentario`
--

INSERT INTO `Comentario` (`ID_reserva`, `puntuacion`, `comentario`) VALUES
(1, 8.5, 'El servicio fue excelente, muy atentos.');
