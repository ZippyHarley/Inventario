-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-09-2025 a las 16:59:41
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
-- Base de datos: `arepas_erp2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hechuras`
--

CREATE TABLE `hechuras` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hechuras`
--

INSERT INTO `hechuras` (`id`, `fecha`, `cantidad`, `costo_unitario`, `creado_en`, `nombre`) VALUES
(1, '2025-08-18', 0, 0.00, '2025-08-18 05:26:58', 'hechura numero uno'),
(3, '2025-09-26', 0, 0.00, '2025-09-26 13:22:19', 'Arepa de pollo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hechura_ingredientes`
--

CREATE TABLE `hechura_ingredientes` (
  `id` int(11) NOT NULL,
  `hechura_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `unidad` varchar(50) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nombre`, `unidad`, `costo_unitario`, `creado_en`) VALUES
(4, 'Maiz', 'unidad', 20000.00, '2025-08-17 22:51:19'),
(8, 'azucar', 'unidad', 20000.00, '2025-09-03 02:32:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inversiones`
--

CREATE TABLE `inversiones` (
  `id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inversiones`
--

INSERT INTO `inversiones` (`id`, `ingrediente_id`, `cantidad`, `costo_unitario`, `fecha`) VALUES
(1, 4, 2.00, 10000.00, '2025-08-17 22:51:34'),
(2, 4, 2.00, 12000.00, '2025-08-18 04:20:50'),
(3, 8, 3.00, 25000.00, '2025-09-03 02:32:53'),
(4, 8, 2.00, 25000.00, '2025-09-11 23:17:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `miembros`
--

CREATE TABLE `miembros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `miembros`
--

INSERT INTO `miembros` (`id`, `nombre`, `porcentaje`, `creado_en`) VALUES
(1, 'Miembro 1', 19.07, '2025-08-17 21:54:49'),
(2, 'Miembro 2', 13.49, '2025-08-17 21:54:49'),
(3, 'Miembro 3', 13.49, '2025-08-17 21:54:49'),
(4, 'Miembro 4', 13.49, '2025-08-17 21:54:49'),
(5, 'Miembro 5', 13.49, '2025-08-17 21:54:49'),
(6, 'Miembro 6', 13.49, '2025-08-17 21:54:49'),
(7, 'Miembro 7', 13.49, '2025-08-17 21:54:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `precio` decimal(10,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `stock`, `precio`, `creado_en`) VALUES
(1, 'Arepa de Queso', 3, 10000.00, '2025-08-17 22:31:14'),
(4, 'Arepa de Queso', 4, 20000.00, '2025-09-03 02:33:46'),
(5, 'Arepa de pollo', 6, 20000.00, '2025-09-11 23:17:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_mensual`
--

CREATE TABLE `resumen_mensual` (
  `mes` varchar(7) DEFAULT NULL,
  `total_hechuras` decimal(32,2) DEFAULT NULL,
  `total_ventas` decimal(32,2) DEFAULT NULL,
  `total_inversiones` decimal(32,2) DEFAULT NULL,
  `utilidad` decimal(33,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `creado_en`) VALUES
(1, 'Administrador', 'admin@arepas.com', '$2y$10$ouyTiJxXqr19ay68ku4HvOG.d.93YmwVZTd0TIrCHrxKXZokMqc32', 'admin', '2025-08-17 21:05:50'),
(2, 'Usuario Demo', 'usuario@arepas.com', '$2y$10$dUy566Yse6UdxfF2HjRg..rThVlmRwltedfL75XlCcOreAa5V3AE6', 'usuario', '2025-08-17 21:05:50'),
(9, 'jorge', 'jorgeestivenmendez@gmail.com', '123456', 'usuario', '2025-08-17 22:32:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `producto` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `producto`, `cantidad`, `precio_unitario`, `creado_en`) VALUES
(1, '2025-08-18', 'Arepa de Queso', 5, 10000.00, '2025-08-17 22:31:32'),
(3, '2025-08-18', 'Arepa de pollo', 2, 20000.00, '2025-08-18 04:27:38'),
(4, '2025-09-03', 'Arepa de Queso', 2, 10000.00, '2025-09-03 02:33:28'),
(5, '2025-09-12', 'Arepa de pollo', 2, 20000.00, '2025-09-11 23:20:22'),
(6, '2025-09-26', 'Arepa de pollo', 2, 20000.00, '2025-09-26 13:21:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `hechuras`
--
ALTER TABLE `hechuras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `hechura_ingredientes`
--
ALTER TABLE `hechura_ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hechura_id` (`hechura_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`);

--
-- Indices de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ingredientes_nombre` (`nombre`);

--
-- Indices de la tabla `inversiones`
--
ALTER TABLE `inversiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`);

--
-- Indices de la tabla `miembros`
--
ALTER TABLE `miembros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `hechuras`
--
ALTER TABLE `hechuras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `hechura_ingredientes`
--
ALTER TABLE `hechura_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `inversiones`
--
ALTER TABLE `inversiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `miembros`
--
ALTER TABLE `miembros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `hechura_ingredientes`
--
ALTER TABLE `hechura_ingredientes`
  ADD CONSTRAINT `hechura_ingredientes_ibfk_1` FOREIGN KEY (`hechura_id`) REFERENCES `hechuras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hechura_ingredientes_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inversiones`
--
ALTER TABLE `inversiones`
  ADD CONSTRAINT `inversiones_ibfk_1` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
