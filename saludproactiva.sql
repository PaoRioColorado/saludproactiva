-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2025 a las 04:37:22
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
-- Base de datos: `saludproactiva`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `estudio_id` int(11) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `enviado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `controles`
--

CREATE TABLE `controles` (
  `id` int(11) NOT NULL,
  `dni_paciente` varchar(20) NOT NULL,
  `fecha_control` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `controles`
--

INSERT INTO `controles` (`id`, `dni_paciente`, `fecha_control`) VALUES
(1, '12345678', '2025-09-20'),
(2, '12345679', '2025-09-25'),
(3, '12345680', '2025-09-28'),
(4, '12345681', '2025-10-02'),
(5, '12345682', '2025-10-04'),
(6, '12345683', '2025-10-06'),
(7, '12345684', '2025-10-09'),
(8, '12345685', '2025-10-12'),
(9, '12345686', '2025-10-15'),
(10, '12345687', '2025-10-20'),
(11, '22345678', '2025-09-18'),
(12, '22345679', '2025-09-23'),
(13, '22345680', '2025-09-27'),
(14, '22345681', '2025-10-01'),
(15, '22345682', '2025-10-05'),
(16, '22345683', '2025-10-08'),
(17, '22345684', '2025-10-11'),
(18, '22345685', '2025-10-14'),
(19, '22345686', '2025-10-18'),
(20, '22345687', '2025-10-21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudios`
--

CREATE TABLE `estudios` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `proximo_control` date DEFAULT NULL,
  `frecuencia` int(11) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudios`
--

INSERT INTO `estudios` (`id`, `paciente_id`, `tipo`, `fecha`, `proximo_control`, `frecuencia`, `notas`) VALUES
(21, 9, 'Chequeo general anual', '2025-07-10', '2026-07-10', 12, 'Todo en orden. Revisión pulmonar por asma infantil, sin complicaciones.'),
(22, 10, 'Control traumatológico', '2025-07-18', '2026-01-18', 12, 'Tendinitis rotuliana en seguimiento.'),
(23, 11, 'Chequeo general anual', '2025-07-24', '2026-07-24', 12, 'Sin patologías activas.'),
(24, 12, 'Resonancia de isquiotibiales', '2025-07-30', '2026-07-30', 12, 'Lesión muscular leve en recuperación.'),
(25, 13, 'Control de fisioterapia', '2025-08-03', '2026-02-03', 12, 'Distensión leve. Alta en próxima visita.'),
(26, 14, 'Control de presión arterial', '2025-08-07', '2025-11-07', 12, 'Hipertensión controlada. Sin síntomas.'),
(27, 15, 'Evaluación lumbar', '2025-08-11', '2026-08-11', 12, 'Dolor mecánico. Ejercicios indicados.'),
(28, 16, 'Chequeo general', '2025-08-15', '2026-08-15', 12, 'Alergia estacional leve.'),
(29, 17, 'Control de rendimiento físico', '2025-08-20', '2026-08-20', 12, 'Sin hallazgos patológicos.'),
(30, 18, 'Radiografía cervical', '2025-08-25', '2026-08-25', 12, 'Contracturas frecuentes. Se recomienda fisioterapia preventiva.'),
(31, 19, 'Evaluación post operatoria', '2025-08-29', '2026-02-28', 6, 'Rodilla estable. Seguimiento kinésico.'),
(32, 20, 'Control endocrinológico', '2025-09-03', '2025-12-03', 12, 'TSH y T4 en rango. Continuar tratamiento.'),
(33, 21, 'Prueba de alergias respiratorias', '2025-09-07', '2026-09-07', 12, 'Reacción leve al polen. Tratamiento sintomático.'),
(34, 22, 'Evaluación traumatológica', '2025-09-10', '2026-03-10', 12, 'Dolor lumbar controlado con ejercicios.'),
(35, 23, 'Chequeo general anual', '2025-09-14', '2026-09-14', 12, 'Sin alteraciones. Buen estado físico general.'),
(36, 24, 'Control de hombro', '2025-09-18', '2026-03-18', 12, 'Mejoría parcial. Rehabilitación en curso.'),
(37, 25, 'Control respiratorio', '2025-09-22', '2025-12-22', 12, 'Asma leve compensada.'),
(38, 26, 'Análisis metabólico', '2025-09-26', '2026-03-26', 12, 'Glucemia normal. Mantener dieta balanceada.'),
(39, 27, 'Chequeo general anual', '2025-10-01', '2026-10-01', 12, 'Buen estado general.'),
(40, 28, 'Control neurológico', '2025-10-06', '2026-04-06', 12, 'Migrañas leves, respuesta favorable al tratamiento.'),
(41, 9, 'Chequeo general anual', '2025-07-10', '2026-07-10', 12, 'Sin complicaciones. Función pulmonar normal.'),
(42, 10, 'Control traumatológico', '2025-08-15', '2026-02-15', 12, 'Tendinitis rotuliana estable. Continuar fisioterapia preventiva.'),
(43, 11, 'Chequeo general anual', '2025-07-22', '2026-07-22', 12, 'Buen estado general. Recomendado control odontológico anual.'),
(44, 12, 'Resonancia muscular', '2025-08-03', '2026-08-03', 12, 'Lesión leve en isquiotibiales en recuperación. Alta en 30 días.'),
(45, 13, 'Control de fisioterapia', '2025-08-25', '2026-02-25', 12, 'Distensión crónica estable. Reforzar elongación posterior.'),
(46, 14, 'Control de presión arterial', '2025-09-02', '2025-12-02', 12, 'Hipertensión leve controlada. Mantener medicación actual.'),
(47, 15, 'Evaluación lumbar', '2025-09-20', '2026-09-20', 12, 'Dolor mecánico intermitente. Continuar ejercicios de core.'),
(48, 16, 'Chequeo general', '2025-08-12', '2026-08-12', 12, 'Sin hallazgos. Recomendado control alergológico en primavera.'),
(49, 17, 'Control físico anual', '2025-09-05', '2026-09-05', 12, 'Sin antecedentes patológicos. Examen físico normal.'),
(50, 18, 'Radiografía cervical', '2025-09-28', '2026-09-28', 12, 'Contracturas leves. Se indica fisioterapia preventiva.'),
(51, 19, 'Evaluación post operatoria', '2025-08-14', '2026-02-14', 6, 'Rodilla estable. Continuar fortalecimiento kinésico.'),
(52, 20, 'Control endocrinológico', '2025-07-30', '2025-10-30', 12, 'TSH y T4 normales. Continuar tratamiento para hipotiroidismo.'),
(53, 21, 'Prueba de alergias respiratorias', '2025-08-19', '2026-08-19', 12, 'Reacción leve al polen. Control anual sugerido.'),
(54, 22, 'Evaluación traumatológica', '2025-09-15', '2026-03-15', 12, 'Dolor lumbar leve, responde bien a estiramientos.'),
(55, 23, 'Chequeo general anual', '2025-10-05', '2026-10-05', 12, 'Sin alteraciones. Buen estado físico general.'),
(56, 24, 'Control de hombro', '2025-09-20', '2026-03-20', 12, 'Tendinitis de hombro en recuperación. Sin signos de recaída.'),
(57, 25, 'Control respiratorio', '2025-08-29', '2025-11-29', 12, 'Asma leve compensada. No se requiere ajuste de medicación.'),
(58, 26, 'Análisis metabólico', '2025-09-12', '2026-03-12', 12, 'Glucemia normal. Mantener dieta balanceada.'),
(59, 27, 'Chequeo general anual', '2025-08-21', '2026-08-21', 12, 'Sin antecedentes patológicos. Examen físico normal.'),
(60, 28, 'Control neurológico', '2025-09-27', '2026-03-27', 12, 'Migrañas esporádicas, respuesta favorable al tratamiento.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medico_registrado`
--

CREATE TABLE `medico_registrado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medico_registrado`
--

INSERT INTO `medico_registrado` (`id`, `nombre`, `email`, `password`, `creado_en`) VALUES
(1, 'Rene Favaloro', 'doctor@correo.com', '$2y$10$34ILQV1OkgwLeaETd061vuiZRzOlXBftkkHmZrIXH5.THUe25jaH2', '2025-08-26 19:28:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `enfermedades` text DEFAULT NULL,
  `apellido` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `dni` varchar(20) NOT NULL DEFAULT '',
  `estado` varchar(20) NOT NULL DEFAULT '',
  `edad` int(11) DEFAULT NULL,
  `sexo` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `grupo_sanguineo` varchar(5) NOT NULL DEFAULT '',
  `telefono` varchar(20) NOT NULL DEFAULT '',
  `movil` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `direccion` varchar(200) NOT NULL DEFAULT '',
  `obra_social` varchar(100) DEFAULT NULL,
  `num_afiliado` varchar(50) NOT NULL,
  `historia_clinica` varchar(50) NOT NULL DEFAULT '',
  `fecha_ultimo_control` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id`, `fecha_nacimiento`, `enfermedades`, `apellido`, `nombre`, `dni`, `estado`, `edad`, `sexo`, `grupo_sanguineo`, `telefono`, `movil`, `email`, `direccion`, `obra_social`, `num_afiliado`, `historia_clinica`, `fecha_ultimo_control`) VALUES
(5, '1954-05-14', 'Hipertensión, Diabetes', 'Dali', 'Salvador', '9123456', 'Alta', 71, NULL, '0 +', '2914123123', '2915904189', 'dali@gmail.com', 'España 11', 'OSDE', '455', '', NULL),
(7, '1980-10-25', 'Hipertensión', 'Picasso', 'Pablo', '10123456', 'Alta', 44, NULL, '', '', '2914123456', 'picasso@mail.com', 'Malaga 1881', 'MEDIFE', '918', '', NULL),
(8, '1980-12-18', 'Hipotiroidismo', 'Aguilera', 'Cristina', '20123456', 'Alta', 44, NULL, '', '2914123457', '', 'cristina_ag@email.com', 'Staten Island 44', 'Galeno', '45000', '', NULL),
(9, '1987-06-24', 'Asma leve en la infancia (actualmente compensado)', 'Messi', 'Lionel', '12345678', 'Alta', 38, 'Masculino', 'O+', '2915001001', '', 'lmessi@afa.com', 'Rosario 10', 'OSDE', '1001', '', NULL),
(10, '1994-10-10', 'Tendinitis rotuliana', 'Martínez', 'Lautaro', '12345679', 'Alta', 31, 'Masculino', 'A+', '2915001002', '', 'lmartinez@afa.com', 'Bahía Blanca 22', 'Swiss Medical', '1002', '', NULL),
(11, '1998-01-03', 'Sin antecedentes patológicos', 'Alvarez', 'Julián', '12345680', 'Alta', 27, 'Masculino', 'B+', '2915001003', '', 'jalvarez@afa.com', 'Córdoba 33', 'Galeno', '1003', '', NULL),
(12, '1992-02-20', 'Lesión muscular recurrente (isquiotibiales)', 'Dybala', 'Paulo', '12345681', 'Alta', 33, 'Masculino', 'O-', '2915001004', '', 'pdybala@afa.com', 'Laguna Larga 44', 'Medifé', '1004', '', NULL),
(13, '1993-06-11', 'Distensión isquiotibial crónica', 'Di María', 'Ángel', '12345682', 'Alta', 32, 'Masculino', 'A-', '2915001005', '', 'adimaria@afa.com', 'Rosario 55', 'IOMA', '1005', '', NULL),
(14, '1988-04-02', 'Hipertensión leve controlada', 'Otamendi', 'Nicolás', '12345683', 'Alta', 37, 'Masculino', 'O+', '2915001006', '', 'notamendi@afa.com', 'Buenos Aires 66', 'OSDE', '1006', '', NULL),
(15, '1997-04-05', 'Dolor lumbar mecánico', 'Romero', 'Cristian', '12345684', 'Alta', 28, 'Masculino', 'B+', '2915001007', '', 'cromero@afa.com', 'Córdoba 77', 'Galeno', '1007', '', NULL),
(16, '1995-02-22', 'Alergia estacional', 'Martínez', 'Emiliano', '12345685', 'Alta', 30, 'Masculino', 'O-', '2915001008', '', 'emartinez@afa.com', 'Mar del Plata 88', 'Medifé', '1008', '', NULL),
(17, '1994-02-04', 'Sin antecedentes patológicos', 'Lo Celso', 'Giovani', '12345686', 'Alta', 31, 'Masculino', 'A+', '2915001009', '', 'glocelso@afa.com', 'Rosario 99', 'OSDE', '1009', '', NULL),
(18, '1991-02-28', 'Contracturas cervicales frecuentes', 'Tagliafico', 'Nicolás', '12345687', 'Alta', 34, 'Masculino', 'B+', '2915001010', '', 'ntagliafico@afa.com', 'Buenos Aires 100', 'OSDE', '1010', '', NULL),
(19, '1993-02-11', 'Lesión de ligamento cruzado anterior (operada)', 'Banini', 'Estefanía', '22345678', 'Alta', 32, 'Femenino', 'A+', '2916001001', '', 'ebanini@afa.com', 'Mendoza 11', 'OSDE', '2001', '', NULL),
(20, '1990-09-13', 'Hipotiroidismo leve', 'Correa', 'Soledad', '22345679', 'Alta', 35, 'Femenino', 'O+', '2916001002', '', 'scorrea@afa.com', 'Buenos Aires 22', 'Galeno', '2002', '', NULL),
(21, '1996-08-15', 'Alergia respiratoria leve', 'Larroquette', 'Mariana', '22345680', 'Alta', 29, 'Femenino', 'B+', '2916001003', '', 'mlarroquette@afa.com', 'Avellaneda 33', 'Medifé', '2003', '', NULL),
(22, '1994-01-17', 'Dolor lumbar mecánico', 'Bonsegundo', 'Florencia', '22345681', 'Alta', 31, 'Femenino', 'O-', '2916001004', '', 'fbonsegundo@afa.com', 'Morteros 44', 'IOMA', '2004', '', NULL),
(23, '1991-08-08', 'Sin antecedentes patológicos', 'Potassa', 'Belén', '22345682', 'Alta', 34, 'Femenino', 'A-', '2916001005', '', 'bpotassa@afa.com', 'Venado Tuerto 55', 'OSDE', '2005', '', NULL),
(24, '1994-09-07', 'Tendinitis de hombro', 'Cometti', 'Agustina', '22345683', 'Alta', 31, 'Femenino', 'B+', '2916001006', '', 'acomett@afa.com', 'Buenos Aires 66', 'Swiss Medical', '2006', '', NULL),
(25, '1998-07-17', 'Asma leve', 'Bravo', 'Vanina', '22345684', 'Alta', 27, 'Femenino', 'O+', '2916001007', '', 'vbravo@afa.com', 'San Juan 77', 'Galeno', '2007', '', NULL),
(26, '1999-03-25', 'Hipoglucemia reactiva', 'Benítez', 'Ruth', '22345685', 'Alta', 26, 'Femenino', 'A+', '2916001008', '', 'rbenitez@afa.com', 'Córdoba 88', 'OSDE', '2008', '', NULL),
(27, '2000-11-20', 'Sin antecedentes patológicos', 'Chavez', 'Eliana', '22345686', 'Alta', 24, 'Femenino', 'B+', '2916001009', '', 'echavez@afa.com', 'Santa Fe 99', 'Medifé', '2009', '', NULL),
(28, '1997-03-12', 'Migrañas esporádicas', 'Haidar', 'Dalila', '22345687', 'Alta', 28, 'Femenino', 'O-', '2916001010', '', 'dhaidar@afa.com', 'Buenos Aires 100', 'OSDE', '2010', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','confirmado','realizado','cancelado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id`, `paciente_id`, `medico_id`, `fecha`, `motivo`, `notas`, `creado_en`, `estado`) VALUES
(3, 8, 1, '2025-09-06 16:00:00', 'Ecografía', '', '2025-09-04 15:47:59', 'pendiente'),
(5, 5, 1, '2025-09-27 14:15:00', 'Control', NULL, '2025-09-04 17:28:12', 'pendiente'),
(6, 8, 1, '2025-11-07 14:15:00', 'Ecografía', NULL, '2025-09-04 17:43:23', 'confirmado'),
(7, 5, 1, '2025-11-11 08:00:00', 'Resultado laboratorio', NULL, '2025-11-07 20:33:30', 'confirmado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('medico','paciente') NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `estudio_id` (`estudio_id`);

--
-- Indices de la tabla `controles`
--
ALTER TABLE `controles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estudios`
--
ALTER TABLE `estudios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`);

--
-- Indices de la tabla `medico_registrado`
--
ALTER TABLE `medico_registrado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `medico_id` (`medico_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `controles`
--
ALTER TABLE `controles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `estudios`
--
ALTER TABLE `estudios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `medico_registrado`
--
ALTER TABLE `medico_registrado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `alertas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  ADD CONSTRAINT `alertas_ibfk_2` FOREIGN KEY (`estudio_id`) REFERENCES `estudios` (`id`);

--
-- Filtros para la tabla `estudios`
--
ALTER TABLE `estudios`
  ADD CONSTRAINT `estudios_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`);

--
-- Filtros para la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD CONSTRAINT `turnos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
