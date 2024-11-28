-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2024 a las 04:10:05
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
-- Base de datos: `facetruck`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buscar_fletes`
--

CREATE TABLE `buscar_fletes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `vigencia` tinyint(1) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `tipo_viaje` enum('Local','Foráneo') NOT NULL,
  `estado_partida` varchar(255) NOT NULL,
  `municipio_partida` varchar(255) NOT NULL,
  `estado_destino` varchar(255) NOT NULL,
  `municipio_destino` varchar(255) NOT NULL,
  `kilometraje_aproximado` int(11) NOT NULL,
  `tipo_vehiculo_solicitado` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `pago_ofrecido` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buscar_operadores`
--

CREATE TABLE `buscar_operadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido_paterno` varchar(255) NOT NULL,
  `apellido_materno` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `estado` varchar(255) NOT NULL,
  `municipio` varchar(255) NOT NULL,
  `licencia` enum('Estatal','Federal') NOT NULL,
  `tipo_licencia` varchar(255) NOT NULL,
  `fecha_vigencia` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pregunta_uno_empresas` varchar(255) DEFAULT NULL,
  `pregunta_dos_empresas` varchar(255) DEFAULT NULL,
  `pregunta_tres_empresas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `usuario_id`, `pregunta_uno_empresas`, `pregunta_dos_empresas`, `pregunta_tres_empresas`) VALUES
(1, 21, 'Empresa', 'dos', 'Tres');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hombres_camion`
--

CREATE TABLE `hombres_camion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pregunta_uno_hombres_camion` varchar(255) DEFAULT NULL,
  `pregunta_dos_hombres_camion` varchar(255) DEFAULT NULL,
  `pregunta_tres_hombres_camion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hombres_camion`
--

INSERT INTO `hombres_camion` (`id`, `usuario_id`, `pregunta_uno_hombres_camion`, `pregunta_dos_hombres_camion`, `pregunta_tres_hombres_camion`) VALUES
(1, 20, 'HC_111', 'HC_222', 'HC_333');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas_empleo`
--

CREATE TABLE `ofertas_empleo` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `vigente` tinyint(1) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `municipio` varchar(255) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `sueldo` decimal(10,2) NOT NULL,
  `tipo_viaje` enum('foráneo','local') NOT NULL,
  `descripcion_ruta` text NOT NULL,
  `tipo_vehiculo_remolque` varchar(255) NOT NULL,
  `requisitos` text NOT NULL,
  `prestaciones` text NOT NULL,
  `contacto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas_empresas`
--

CREATE TABLE `ofertas_empresas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `vigente` tinyint(1) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `municipio` varchar(255) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `pago_ofrecido` decimal(10,2) NOT NULL,
  `tipo_viaje` enum('foráneo','local') NOT NULL,
  `descripcion_ruta` text NOT NULL,
  `tipo_vehiculo_remolque` varchar(255) NOT NULL,
  `requisitos` text NOT NULL,
  `contacto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operadores`
--

CREATE TABLE `operadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pregunta_uno_operadores` varchar(255) DEFAULT NULL,
  `pregunta_dos_operadores` varchar(255) DEFAULT NULL,
  `pregunta_tres_operadores` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `operadores`
--

INSERT INTO `operadores` (`id`, `usuario_id`, `pregunta_uno_operadores`, `pregunta_dos_operadores`, `pregunta_tres_operadores`) VALUES
(1, 19, '1', '2', '3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `operador_id` int(11) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('operador','hombreCamion','empresa') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `operador_id`, `correo`, `password`, `tipo_usuario`, `created_at`, `foto_perfil`) VALUES
(19, NULL, 'operador@gmail.com', '$2y$10$rvGRCn5Z95WvG6g9dquMa.YCGqI5qCfSmCeZJoKbUsSIbVUrqp/IW', 'operador', '2024-11-22 14:46:00', 'uploads/67472bc6ba8a7_logoooo.png'),
(20, NULL, 'hc@gmail.com', '$2y$10$sOwjUgbl8EKrYq9ZdDBhiempPhRuHVOhlgG7YtxaxFFfVYbbg0IF2', 'hombreCamion', '2024-11-22 14:56:30', 'uploads/6747aff6085e5_logoooo (2).png'),
(21, NULL, 'em@gmail.com', '$2y$10$KU4lYfOt.RS6VlfaXUo.0uns8MP7jK0XYImHQV6vbSbvhOc.cCIvS', 'empresa', '2024-11-22 17:16:00', NULL);

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `before_usuario_insert` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    DECLARE operador_id INT;
    -- Aquí debes definir la lógica para obtener el operador_id correspondiente
    -- Por ejemplo, puedes asignar un operador_id específico o generar uno nuevo

    -- Asigna el operador_id al nuevo usuario
    SET NEW.operador_id = operador_id;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `buscar_fletes`
--
ALTER TABLE `buscar_fletes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `buscar_operadores`
--
ALTER TABLE `buscar_operadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `hombres_camion`
--
ALTER TABLE `hombres_camion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `ofertas_empleo`
--
ALTER TABLE `ofertas_empleo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `ofertas_empresas`
--
ALTER TABLE `ofertas_empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `operadores`
--
ALTER TABLE `operadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `buscar_fletes`
--
ALTER TABLE `buscar_fletes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `buscar_operadores`
--
ALTER TABLE `buscar_operadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `hombres_camion`
--
ALTER TABLE `hombres_camion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ofertas_empleo`
--
ALTER TABLE `ofertas_empleo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ofertas_empresas`
--
ALTER TABLE `ofertas_empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `operadores`
--
ALTER TABLE `operadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `buscar_fletes`
--
ALTER TABLE `buscar_fletes`
  ADD CONSTRAINT `buscar_fletes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `buscar_operadores`
--
ALTER TABLE `buscar_operadores`
  ADD CONSTRAINT `buscar_operadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `empresas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `hombres_camion`
--
ALTER TABLE `hombres_camion`
  ADD CONSTRAINT `hombres_camion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ofertas_empleo`
--
ALTER TABLE `ofertas_empleo`
  ADD CONSTRAINT `ofertas_empleo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ofertas_empresas`
--
ALTER TABLE `ofertas_empresas`
  ADD CONSTRAINT `ofertas_empresas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `operadores`
--
ALTER TABLE `operadores`
  ADD CONSTRAINT `operadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
