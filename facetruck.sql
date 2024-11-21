-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2024 a las 21:25:34
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
(1, 15, 'rrrrr', '3453453', '3');

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
(1, 16, 'sadasdas', 'asdasdas', 'asdasd');

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
(1, 17, 'Pregunta uno (1)', 'Pregunta dos (2)', 'Pregunta (3)');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `operador_id`, `correo`, `password`, `tipo_usuario`, `created_at`) VALUES
(11, NULL, 'aaa@m.com', '$2y$10$m.3S7vR117IupQjysqRmNuZpFQyT5MbhgprAZRqLMt7TYe5jYAUK2', 'hombreCamion', '2024-11-20 04:54:25'),
(12, NULL, 'ybam89@gmail.com', '$2y$10$vF/UF5pzA34OSIooMaZowuFWACHrFDZgUSmZqwgaG16PNPdy.UatK', 'empresa', '2024-11-20 05:05:41'),
(13, NULL, 'a5546945370@gmail.com', '$2y$10$r.jNG4xalul6z2zljeFVT.D0iuTyto9k7VO8g1S0gLq6vIJ8BJJJe', 'operador', '2024-11-20 05:08:51'),
(14, NULL, 'ggg@sms.com', '$2y$10$G537wjfV8E3Q24rXGTymOecXA/Gi6kJRiQY04SkurNLSjRpJ/P0me', 'hombreCamion', '2024-11-20 05:24:05'),
(15, NULL, 'asasa@dsdaa.com', '$2y$10$MnHWuJlPROmWtSJDav4jn.72ImAC.f54nytQAXleOossS1oZlQVuK', 'empresa', '2024-11-20 05:35:53'),
(16, NULL, 'hm@camion.com', '$2y$10$hfBwjhrkOHyra5iJR.Pis.w2u5t801d0kKJ4eo3t3SaXqn0pE1y8a', 'hombreCamion', '2024-11-20 18:23:12'),
(17, NULL, 'jp@gmail.com', '$2y$10$3/hf8o9c.anFSHQnG2QPleN3aL4vm3uOswuDMgt.RE0wKhxPm1SB6', 'operador', '2024-11-21 17:53:08');

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
-- AUTO_INCREMENT de la tabla `operadores`
--
ALTER TABLE `operadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

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
-- Filtros para la tabla `operadores`
--
ALTER TABLE `operadores`
  ADD CONSTRAINT `operadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
