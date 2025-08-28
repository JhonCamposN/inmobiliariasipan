-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-08-2025 a las 19:02:47
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
  `user_agent` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `nombre`, `email`, `calificacion`, `comentario`, `fecha_creacion`, `estado`, `ip_address`, `user_agent`, `activo`) VALUES
(2, 'Ana Torres', 'ana@ejemplo.com', 5, 'Proceso muy transparente y profesional. Recomiendo totalmente sus servicios para encontrar la propiedad perfecta.', '2025-08-02 22:37:16', 'activo', '192.168.1.2', NULL, 1),
(3, 'María González', 'maria@ejemplo.com', 5, 'Sipán Inmobiliaria hizo realidad mi sueño de tener mi propia casa. El proceso fue transparente, profesional y muy eficiente.', '2025-08-02 22:37:16', 'activo', '192.168.1.3', NULL, 1),
(4, 'Luis Ramírez', 'luis@ejemplo.com', 4, 'Buen servicio, encontré lo que buscaba. El personal es muy atento y profesional.', '2025-08-02 22:37:16', 'activo', '192.168.1.4', NULL, 1),
(5, 'Carmen Silva', 'carmen@ejemplo.com', 2, 'Increíble experiencia. Me ayudaron a encontrar la casa perfecta para mi familia. Altamente recomendados.', '2025-08-02 22:37:16', 'activo', '192.168.1.5', NULL, 1),
(6, 'Jhon Antony Campos Nuñez', 'antonycamposnunez@gmail.com', 4, 'Buena recepción', '2025-08-22 00:05:47', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1),
(7, 'juanita', 'correo@gmail.com', 5, 'buena experiencia', '2025-08-22 00:18:44', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1),
(10, 'Usuario de Prueba', 'correo@gmail.com', 5, 'La mejor atención que se pueda recibir, me encanto, y sus proyecto de lo mejor que hay en el rubro', '2025-08-27 22:23:05', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1),
(11, 'asd', 'correo@gmail.com', 4, 'buena recepcion', '2025-08-27 22:32:00', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1),
(12, 'Jhon Nuñez', 'jhon@gmail.com', 5, 'Muy buena asesoría', '2025-08-27 23:44:25', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1),
(13, 'Antony Campos', 'campos@gmail.com', 5, 'La mejor experiencia, buena recepción y asesoría', '2025-08-27 23:45:05', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1),
(14, 'Fabiola Mayta', 'fabiola@gmail.com', 5, 'Buena atención y excelentes precios', '2025-08-27 23:46:37', 'activo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(8) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente',
  `ip_address` varchar(45) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `nombre`, `dni`, `email`, `telefono`, `mensaje`, `fecha_creacion`, `estado`, `ip_address`, `activo`) VALUES
(4, 'Jhon Antony Campos Nuñez', '12345678', 'antonycamposnunez@gmail.com', '985978801', 'DATOS DEL CONTACTO:\nNombre: Jhon Antony Campos Nuñez\nEmail: antonycamposnunez@gmail.com\nTeléfono: 985978801\nTipo de consulta: dominio-media-luna\n\nMENSAJE:\nNecesito más información', '2025-08-20 23:54:47', 'pendiente', '::1', 1),
(5, 'juanita perez', '87654321', 'correo@gmail.com', '987654321', 'DATOS DEL CONTACTO:\nNombre: juanita perez\nEmail: correo@gmail.com\nTeléfono: 987654321\nTipo de consulta: dominio-media-luna\n\nMENSAJE:\nnecesito mas informacion', '2025-08-22 00:20:05', 'atendido', '::1', 1),
(7, 'Pedro Gómez', '11223344', 'pedro.gomez@example.com', '912345678', 'DATOS DEL CONTACTO:\nNombre: Pedro Gómez\nEmail: pedro.gomez@example.com\nTeléfono: 912345678\nTipo de consulta: proyecto-central\n\nMENSAJE:\nMe gustaría agendar una visita al proyecto.', '2025-08-28 15:00:00', 'pendiente', '192.168.1.10', 0),
(8, 'Ana Torres', '22334455', 'ana.torres@example.com', '998877665', 'DATOS DEL CONTACTO:\nNombre: Ana Torres\nEmail: ana.torres@example.com\nTeléfono: 998877665\nTipo de consulta: venta-terreno-rural\n\nMENSAJE:\nQuiero saber el precio y la disponibilidad.', '2025-08-28 15:05:00', 'atendido', '192.168.1.11', 1),
(9, 'Luis Fernández', '33445566', 'luis.f@example.com', '954321098', 'DATOS DEL CONTACTO:\nNombre: Luis Fernández\nEmail: luis.f@example.com\nTeléfono: 954321098\nTipo de consulta: alquiler-apartamento-lima\n\nMENSAJE:\nBusco un apartamento en alquiler por 6 meses.', '2025-08-28 15:10:00', 'pendiente', '192.168.1.12', 1),
(10, 'Sofía Ramos', '44556677', 'sofia.ramos@example.com', '932109876', 'DATOS DEL CONTACTO:\nNombre: Sofía Ramos\nEmail: sofia.ramos@example.com\nTeléfono: 932109876\nTipo de consulta: informacion-general\n\nMENSAJE:\nNecesito información sobre los servicios que ofrecen.', '2025-08-28 15:15:00', 'atendido', '192.168.1.13', 1),
(11, 'Carlos Castro', '55667788', 'carlos.c@example.com', '987654321', 'DATOS DEL CONTACTO:\nNombre: Carlos Castro\nEmail: carlos.c@example.com\nTeléfono: 987654321\nTipo de consulta: venta-casa-playa\n\nMENSAJE:\nEstoy interesado en una casa de playa, ¿tienen disponibilidad?', '2025-08-28 15:20:00', 'pendiente', '192.168.1.14', 0),
(12, 'María Lopez', '66778899', 'maria.l@example.com', '965432109', 'DATOS DEL CONTACTO:\nNombre: María Lopez\nEmail: maria.l@example.com\nTeléfono: 965432109\nTipo de consulta: tasacion-inmueble\n\nMENSAJE:\nQuiero solicitar una tasación para mi propiedad.', '2025-08-28 15:25:00', 'pendiente', '192.168.1.15', 1),
(13, 'Jorge Valdivia', '77889900', 'jorge.v@example.com', '943210987', 'DATOS DEL CONTACTO:\nNombre: Jorge Valdivia\nEmail: jorge.v@example.com\nTeléfono: 943210987\nTipo de consulta: financiamiento\n\nMENSAJE:\nRequiero información sobre opciones de financiamiento.', '2025-08-28 15:30:00', 'atendido', '192.168.1.16', 1),
(14, 'Gabriela Soto', '88990011', 'gabriela.s@example.com', '921098765', 'DATOS DEL CONTACTO:\nNombre: Gabriela Soto\nEmail: gabriela.s@example.com\nTeléfono: 921098765\nTipo de consulta: compra-oficina-surco\n\nMENSAJE:\nBuscando comprar una oficina en Surco, ¿cuáles son sus opciones?', '2025-08-28 15:35:00', 'pendiente', '192.168.1.17', 0),
(15, 'Ricardo Morales', '99001122', 'ricardo.m@example.com', '909876543', 'DATOS DEL CONTACTO:\nNombre: Ricardo Morales\nEmail: ricardo.m@example.com\nTeléfono: 909876543\nTipo de consulta: proyecto-miraflores\n\nMENSAJE:\nEstoy interesado en el proyecto de Miraflores. Envienme un brochure.', '2025-08-28 15:40:00', 'atendido', '192.168.1.18', 1),
(16, 'Patricia Diaz', '00112233', 'patricia.d@example.com', '978654321', 'DATOS DEL CONTACTO:\nNombre: Patricia Diaz\nEmail: patricia.d@example.com\nTeléfono: 978654321\nTipo de consulta: inversion-inmobiliaria\n\nMENSAJE:\nQuiero invertir en bienes raíces. ¿Qué me recomiendan?', '2025-08-28 15:45:00', 'pendiente', '192.168.1.19', 1),
(17, 'Jhony Prueba', NULL, 'correoprueba@gmail.com', '789456123', 'DATOS DEL CONTACTO:\nNombre: Jhony Prueba\nEmail: correoprueba@gmail.com\nTeléfono: 789456123\nTipo de consulta: dominio-media-luna\n\nMENSAJE:\nmas información', '2025-08-28 17:02:17', 'pendiente', '::1', 1);

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
  ADD KEY `idx_contactos_estado` (`estado`),
  ADD KEY `idx_contactos_dni` (`dni`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
