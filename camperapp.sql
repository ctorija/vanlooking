-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-06-2025 a las 19:28:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `camperapp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenvehiculo`
--

CREATE TABLE `imagenvehiculo` (
  `id` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `es_principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `imagenvehiculo`
--

INSERT INTO `imagenvehiculo` (`id`, `vehiculo_id`, `url`, `es_principal`) VALUES
(16, 12, 'img_683b1079313241.58097599.jpg', 1),
(17, 12, 'img_683b10793209c5.09551540.jpg', 0),
(18, 12, 'img_683b1079334600.60051883.jpg', 0),
(19, 12, 'img_683b1079340f86.57020365.jpg', 0),
(20, 12, 'img_683b107935cf24.40396156.png', 0),
(21, 13, 'img_683b11444b46b4.52660021.jpg', 0),
(22, 13, 'img_683b11444bf9d6.04407687.jpg', 1),
(23, 13, 'img_683b11444cb639.66754768.jpg', 0),
(24, 13, 'img_683b11444eee31.34953095.jpg', 0),
(25, 13, 'img_683b11444fae51.24875370.jpg', 0),
(26, 14, 'img_683b11d06c87f5.76009369.jpg', 1),
(27, 14, 'img_683b11d06da033.49337908.jpg', 0),
(28, 14, 'img_683b11d06eb051.02046905.jpg', 0),
(29, 14, 'img_683b11d06f37f3.17982636.jpg', 0),
(30, 14, 'img_683b11d06fc010.24446544.jpg', 0),
(31, 15, 'img_683b127f160df6.98813836.jpg', 0),
(32, 15, 'img_683b127f16aad7.42384712.jpg', 1),
(33, 15, 'img_683b127f172fb1.57729884.jpg', 0),
(34, 15, 'img_683b127f17bae6.61241615.jpg', 0),
(35, 15, 'img_683b127f18dd65.47503006.jpg', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  `monto_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id`, `vehiculo_id`, `usuario_id`, `fecha_inicio`, `fecha_fin`, `estado`, `monto_total`) VALUES
(6, 14, 10, '2025-06-01', '2025-06-08', 'pendiente', 640.00),
(7, 12, 10, '2025-06-08', '2025-06-08', 'pendiente', 150.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','propietario','cliente') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Laura', 'laura@example.com', '$2y$10$F24DMRQGn5G/5PQ/7j/OdeZ2k3QIFDq1..WYmQU4PVaI5oPeHvNaO', 'cliente'),
(10, 'CARLOS', 'carlos@example.com', '$2y$10$vC0lgL5vaU580mQZg9cyhOtkduonMVNaYtYAGuwMd5i5tEkpMaeE2', 'cliente'),
(11, 'Propietario', 'propietario@example.com', '$2y$10$BsD4xLzO3Gt2PBhk5enuKeyJhRRRv4OxA4JtX2EfnpNiQAE7TFYrq', 'propietario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculo`
--

CREATE TABLE `vehiculo` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `tipo` enum('autocaravana','camper') NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `ano` year(4) DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `precio_dia` decimal(10,2) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `vehiculo`
--

INSERT INTO `vehiculo` (`id`, `propietario_id`, `titulo`, `tipo`, `marca`, `modelo`, `ano`, `ubicacion`, `latitud`, `longitud`, `precio_dia`, `descripcion`) VALUES
(12, 11, 'Autocaravana Blucamp 6 personas', 'autocaravana', 'Blucamp', 'Ocean 650', '2018', 'Madrid, Comunidad de Madrid, España', 40.4167047, -3.7035825, 150.00, 'Preciosa autocaravana para 6 personas viajar/dormir con inodoro químico, toldo, batería auxiliar y enseres.'),
(13, 11, 'Camper Possl Summit 540 3 personas', 'camper', 'Possl', 'Summit 540', '2022', 'Barcelona, Cataluña, España', 0.0000000, 0.0000000, 90.00, 'Camper Summit 540 totalmente equipada para 3 personas viajar/dormir. Ideal para recorrer espacios pequeños y llegar a cualquier parte.'),
(14, 11, 'Camper Volkswagen 2 personas', 'camper', 'Volkswagen', 'California', '2024', 'Lugo, Galicia, España', 0.0000000, 0.0000000, 80.00, 'Preciosa camper rutera para dos personas equipada con todo lo necesario para tu viaje.'),
(15, 11, 'Autocaravana Benimar Tessoro 463 5 personas', 'autocaravana', 'Benimar', 'Tessoro 463', '2021', 'Huelva, Andalucía, España', 37.5019895, -6.9227985, 160.00, 'Increíble autocaravana equipada con enseres, inodoro químico, sillas y mesas, varias camas y KM ilimitados.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `imagenvehiculo`
--
ALTER TABLE `imagenvehiculo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propietario_id` (`propietario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `imagenvehiculo`
--
ALTER TABLE `imagenvehiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `imagenvehiculo`
--
ALTER TABLE `imagenvehiculo`
  ADD CONSTRAINT `imagenvehiculo_ibfk_1` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculo` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD CONSTRAINT `vehiculo_ibfk_1` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
