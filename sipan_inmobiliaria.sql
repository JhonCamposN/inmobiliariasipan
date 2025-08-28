-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 23:37:52
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
  `activo` tinyint(1) DEFAULT 1,
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
(5, 'Carmen Silva', 'carmen@ejemplo.com', 5, 'Increíble experiencia. Me ayudaron a encontrar la casa perfecta para mi familia. Altamente recomendados.', '2025-08-02 22:37:16', 'activo', '192.168.1.5', NULL),
(6, 'Jhon Antony Campos Nuñez', 'antonycamposnunez@gmail.com', 4, 'Buena recepción', '2025-08-22 00:05:47', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(7, 'juanita', 'correo@gmail.com', 5, 'buena experiencia', '2025-08-22 00:18:44', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');

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
  `activo` tinyint(1) DEFAULT 1,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `nombre`, `email`, `telefono`, `mensaje`, `fecha_creacion`, `estado`, `ip_address`) VALUES
(4, 'Jhon Antony Campos Nuñez', 'antonycamposnunez@gmail.com', '985978801', 'DATOS DEL CONTACTO:\nNombre: Jhon Antony Campos Nuñez\nEmail: antonycamposnunez@gmail.com\nTeléfono: 985978801\nTipo de consulta: dominio-media-luna\n\nMENSAJE:\nNecesito más información', '2025-08-21 23:54:47', 'pendiente', '::1'),
(5, 'juanita perez', 'correo@gmail.com', '987654321', 'DATOS DEL CONTACTO:\nNombre: juanita perez\nEmail: correo@gmail.com\nTeléfono: 987654321\nTipo de consulta: dominio-media-luna\n\nMENSAJE:\nnecesito mas informacion', '2025-08-22 00:20:05', 'pendiente', '::1');

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
-- Estructura de tabla para la tabla `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_admin`
--

INSERT INTO `usuarios_admin` (`id`, `usuario`, `password`, `nombre_completo`, `email`, `fecha_creacion`, `ultimo_acceso`, `estado`) VALUES
(1, 'admin', '$2y$10$E6.Uj0q7aUX5.5J5J5J5JeJ5J5J5J5J5J5J5J5J5J5J5J5J5J5J5J5J', 'Administrador Sipán', 'admin@sipaninmobiliaria.com', '2025-08-21 23:07:27', NULL, 'activo');

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
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contactos_fecha` (`fecha_creacion`),
  ADD KEY `idx_contactos_estado` (`estado`);

--
-- Indices de la tabla `estadisticas_calificaciones`
--
ALTER TABLE `estadisticas_calificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `idx_usuario` (`usuario`),
  ADD KEY `idx_estado` (`estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estadisticas_calificaciones`
--
ALTER TABLE `estadisticas_calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
