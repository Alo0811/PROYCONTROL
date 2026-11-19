-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2026 a las 07:48:06
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
-- Base de datos: `proycontrol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectores`
--

CREATE TABLE `proyectores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `estado` enum('Disponible','Prestado') DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectores`
--

INSERT INTO `proyectores` (`id`, `nombre`, `estado`) VALUES
(1, 'Proyector 1', 'Disponible'),
(2, 'Proyector 2', 'Disponible'),
(3, 'Proyector 3', 'Disponible'),
(4, 'Proyector 4', 'Disponible'),
(5, 'Proyector 5', 'Disponible'),
(6, 'Proyector 6', 'Disponible'),
(7, 'Proyector 7', 'Disponible'),
(8, 'Proyector 8', 'Disponible'),
(9, 'Proyector 9', 'Disponible'),
(10, 'Proyector 10', 'Disponible'),
(11, 'Proyector 11', 'Disponible'),
(12, 'Proyector 12', 'Disponible'),
(13, 'Proyector 13', 'Disponible'),
(14, 'Proyector 14', 'Disponible'),
(15, 'Proyector 15', 'Disponible'),
(16, 'Proyector 16', 'Disponible'),
(17, 'Proyector 17', 'Disponible'),
(18, 'Proyector 18', 'Disponible'),
(19, 'Proyector 19', 'Disponible'),
(20, 'Proyector 20', 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` enum('Pendiente','Aprobada','Denegada','Devuelto') DEFAULT 'Pendiente',
  `fecha_devolucion` date DEFAULT NULL,
  `hora_devolucion` time DEFAULT NULL,
  `proyector_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `usuario_id`, `fecha`, `hora`, `estado`, `fecha_devolucion`, `hora_devolucion`, `proyector_id`) VALUES
(4, 15, '2026-06-12', '23:27:30', '', '2026-06-12', '23:29:12', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervisor`
--

CREATE TABLE `supervisor` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervisor`
--

INSERT INTO `supervisor` (`id`, `usuario`, `password`) VALUES
(2, 'Supervisor', 'Admin@2026');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `rol` enum('Jefe','Subjefe','Tercero') NOT NULL,
  `salon` varchar(20) NOT NULL,
  `carrera` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `credencial` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `rol`, `salon`, `carrera`, `correo`, `credencial`, `fecha_registro`) VALUES
(15, 'Avril Suarez ', 'Jefe', '2-36', 'Licenciatura en Informática', 'avril@fcaei.uaem.edu.mx', 'uploads/credenciales/1781328440_credencial.jpeg', '2026-06-13 05:27:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `proyectores`
--
ALTER TABLE `proyectores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `supervisor`
--
ALTER TABLE `supervisor`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `proyectores`
--
ALTER TABLE `proyectores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `supervisor`
--
ALTER TABLE `supervisor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
