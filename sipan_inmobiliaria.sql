-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-08-2025 a las 20:19:46
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
-- Base de datos: `sipan_inmobiliaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `calificacion` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'activo',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `nombre`, `email`, `calificacion`, `comentario`, `fecha_creacion`, `estado`, `ip_address`, `user_agent`) VALUES
(1, 'Carlos Mendoza', 'carlos@ejemplo.com', 5, 'Excelente servicio y atención. Encontré mi casa ideal gracias a Sipán Inmobiliaria. Muy profesionales y confiables.', '2025-08-02 22:37:16', 'activo', '192.168.1.1', NULL),
(2, 'Ana Torres', 'ana@ejemplo.com', 5, 'Proceso muy transparente y profesional. Recomiendo totalmente sus servicios para encontrar la propiedad perfecta.', '2025-08-02 22:37:16', 'activo', '192.168.1.2', NULL),
(3, 'María González', 'maria@ejemplo.com', 5, 'Sipán Inmobiliaria hizo realidad mi sueño de tener mi propia casa. El proceso fue transparente, profesional y muy eficiente.', '2025-08-02 22:37:16', 'activo', '192.168.1.3', NULL),
(4, 'Luis Ramírez', 'luis@ejemplo.com', 4, 'Buen servicio, encontré lo que buscaba. El personal es muy atento y profesional.', '2025-08-02 22:37:16', 'activo', '192.168.1.4', NULL),
(5, 'Carmen Silva', 'carmen@ejemplo.com', 5, 'Increíble experiencia. Me ayudaron a encontrar la casa perfecta para mi familia. Altamente recomendados.', '2025-08-02 22:37:16', 'activo', '192.168.1.5', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_calificaciones`
--

CREATE TABLE `estadisticas_calificaciones` (
  `id` int(11) NOT NULL,
  `total_calificaciones` int(11) DEFAULT 0,
  `promedio_calificacion` decimal(3,2) DEFAULT 0.00,
  `porcentaje_satisfechos` decimal(5,2) DEFAULT 0.00,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadisticas_calificaciones`
--

INSERT INTO `estadisticas_calificaciones` (`id`, `total_calificaciones`, `promedio_calificacion`, `porcentaje_satisfechos`, `ultima_actualizacion`) VALUES
(1, 5, 4.80, 100.00, '2025-08-02 22:37:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente',
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_calificaciones_fecha` (`fecha_creacion`),
  ADD KEY `idx_calificaciones_estado` (`estado`),
  ADD KEY `idx_calificaciones_calificacion` (`calificacion`);

--
-- Indices de la tabla `estadisticas_calificaciones`
--
ALTER TABLE `estadisticas_calificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contactos_fecha` (`fecha_creacion`),
  ADD KEY `idx_contactos_estado` (`estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estadisticas_calificaciones`
--
ALTER TABLE `estadisticas_calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
