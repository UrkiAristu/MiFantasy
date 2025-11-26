-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2025 a las 13:34:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mi_fantasy`
--

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'Zarpaiak', NULL, NOW(), NOW()),
(2, 'Aietzak', NULL, NOW(), NOW()),
(3, 'Equipo de Prueba', NULL, NOW(), NOW());

--
-- Volcado de datos para la tabla `equipo_jugador`
--

INSERT INTO `equipo_jugador` (`id`, `equipo_id`, `jugador_id`, `fecha_union`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NOW(), NOW()),
(2, 1, 2, NULL, NOW(), NOW()),
(3, 1, 3, NULL, NOW(), NOW()),
(4, 1, 4, NULL, NOW(), NOW()),
(5, 1, 5, NULL, NOW(), NOW()),
(6, 1, 6, NULL, NOW(), NOW()),
(7, 1, 7, NULL, NOW(), NOW()),
(8, 1, 8, NULL, NOW(), NOW()),
(9, 2, 9, NULL, NOW(), NOW()),
(10, 2, 10, NULL, NOW(), NOW()),
(11, 2, 11, NULL, NOW(), NOW()),
(12, 2, 12, NULL, NOW(), NOW()),
(13, 2, 13, NULL, NOW(), NOW()),
(14, 2, 14, NULL, NOW(), NOW()),
(15, 2, 15, NULL, NOW(), NOW()),
(16, 2, 16, NULL, NOW(), NOW());

--
-- Volcado de datos para la tabla `equipo_jugador_torneo`
--

INSERT INTO `equipo_jugador_torneo` (`id`, `jugador_id`, `equipo_id`, `torneo_id`, `goles`, `asistencias`, `puntos`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 0, 0, 0, NOW(), NOW()),
(2, 2, 1, 1, 0, 0, 0, NOW(), NOW()),
(3, 3, 1, 1, 0, 0, 0, NOW(), NOW()),
(4, 4, 1, 1, 0, 0, 0, NOW(), NOW()),
(5, 5, 1, 1, 0, 0, 0, NOW(), NOW()),
(6, 6, 1, 1, 0, 0, 0, NOW(), NOW()),
(7, 7, 1, 1, 0, 0, 0, NOW(), NOW()),
(8, 8, 1, 1, 0, 0, 0, NOW(), NOW()),
(9, 9, 2, 1, 0, 0, 0, NOW(), NOW()),
(10, 10, 2, 1, 0, 0, 0, NOW(), NOW()),
(11, 11, 2, 1, 0, 0, 0, NOW(), NOW()),
(12, 12, 2, 1, 0, 0, 0, NOW(), NOW()),
(13, 13, 2, 1, 0, 0, 0, NOW(), NOW()),
(14, 14, 2, 1, 0, 0, 0, NOW(), NOW()),
(15, 15, 2, 1, 0, 0, 0, NOW(), NOW()),
(16, 16, 2, 1, 0, 0, 0, NOW(), NOW());

--
-- Volcado de datos para la tabla `equipo_torneo`
--

INSERT INTO `equipo_torneo` (`id`, `equipo_id`, `torneo_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NOW(), NOW()),
(2, 2, 1, NOW(), NOW()),
(3, 3, 1, NOW(), NOW());

--
-- Volcado de datos para la tabla `jornadas`
--

INSERT INTO `jornadas` (`id`, `torneo_id`, `nombre`, `orden`, `fecha_inicio`, `fecha_fin`, `fecha_cierre_alineaciones`, `alineaciones_congeladas`, `created_at`, `updated_at`) VALUES
(1, 1, 'Jornada 1', 1, '2026-03-30', '2026-03-30', '2025-03-30 11:00:00', 0, NOW(), NOW()),
(2, 1, 'Jornada 2', 2, '2026-03-31', '2026-03-31', '2025-03-31 11:00:00', 0, NOW(), NOW()),
(3, 1, 'Repesca', 3, '2026-04-01', '2026-04-01', '2026-04-01 11:00:00', 0, NOW(), NOW()),
(4, 1, 'Cuartos', 4, '2026-04-02', '2026-04-02', '2025-04-02 11:00:00', 0, NOW(), NOW()),
(5, 1, 'Semifinales', 5, '2026-04-03', '2026-04-03', '2025-04-03 11:00:00', 0, NOW(), NOW()),
(6, 1, 'Finales', 6, '2026-04-04', '2026-04-04', '2026-04-04 11:00:00', 0, NOW(), NOW());
--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id`, `nombre`, `apellido1`, `apellido2`, `fecha_nacimiento`, `posicion`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'Urki', 'Aristu', 'Viela', '2002-04-03', NULL, NULL, NOW(), NOW()),
(2, 'Ibai', 'Garaikoetxea', 'Arretxea', '2002-09-05', NULL, NULL, NOW(), NOW()),
(3, 'Julen', 'Tranche', 'Moreno', '2025-11-25', NULL, NULL, NOW(), NOW()),
(4, 'Unai', 'Echeverria', 'Goñi', '2002-12-04', NULL, NULL, NOW(), NOW()),
(5, 'Haritz', 'Nuñez', 'Goñi', '2002-05-29', NULL, NULL, NOW(), NOW()),
(6, 'Aimar', 'Larralde', 'Jaurena', '2001-07-22', NULL, NULL, NOW(), NOW()),
(7, 'Beñat', 'Erro', 'Mujica', '2002-09-05', NULL, NULL, NOW(), NOW()),
(8, 'Aaron', 'Damboriena', 'Vicondoa', '2002-04-08', NULL, NULL, NOW(), NOW()),
(9, 'Josu', 'Daguerre', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(10, 'Oier', 'Ariztegi', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(11, 'Gaizka', 'Ayesa', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(12, 'Hasier', 'Plaza', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(13, 'Gilen', 'Quertier', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(14, 'Ibai', 'Merino', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(15, 'Javi', 'Oscoz', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(16, 'Egoi', 'Celayeta', 'Pruebas', '2025-08-11', NULL, NULL, NOW(), NOW()),
(17, 'Aritz', 'Maisterrena', 'Pruebas', '2002-05-05', NULL, NULL, NOW(), NOW());

--
-- Volcado de datos para la tabla `jugador_plantilla`
--

INSERT INTO `jugador_plantilla` (`id`, `plantilla_id`, `jugador_id`, `posicion`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, NOW(), NOW()),
(2, 1, 13, NULL, NOW(), NOW()),
(3, 1, 11, NULL, NOW(), NOW()),
(4, 1, 5, NULL, NOW(), NOW()),
(5, 1, 4, NULL, NOW(), NOW()),
(6, 1, 15, NULL, NOW(), NOW()),
(7, 1, 6, NULL, NOW(), NOW()),
(8, 1, 7, NULL, NOW(), NOW());

--
-- Volcado de datos para la tabla `liguillas`
--

INSERT INTO `liguillas` (`id`, `nombre`, `torneo_id`, `max_usuarios`, `codigo_unico`, `creador_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Liga de amigos', 1, 10, '3MCOH2YG', 1, 'activa', NOW(), NOW());

--
-- Volcado de datos para la tabla `liguilla_usuario`
--

INSERT INTO `liguilla_usuario` (`id`, `liguilla_id`, `user_id`, `nombre_equipo`, `puesto`, `puntos`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, 0, NOW(), NOW());

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id`, `jornada_id`, `equipo_local_id`, `equipo_visitante_id`, `fecha_partido`, `goles_local`, `goles_visitante`, `estado`, `eventos`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, '2026-03-30 16:00:00', NULL, NULL, 'programado', NULL, NOW(), NOW()),
(2, 2, 2, 1, '2026-03-31 17:00:00', NULL, NULL, 'programado', NULL, NOW(), NOW()),
(3, 4, 1, 2, '2026-04-02 20:00:00', NULL, NULL, 'programado', NULL, NOW(), NOW()),
(4, 5, 1, 2, '2026-04-03 00:00:00', NULL, NULL, 'programado', NULL, NOW(), NOW());

--
-- Volcado de datos para la tabla `plantillas`
--

INSERT INTO `plantillas` (`id`, `user_id`, `liguilla_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NOW(), NOW());

--
-- Volcado de datos para la tabla `torneos`
--

INSERT INTO `torneos` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `descripcion`, `logo`, `estado`, `jugadores_por_equipo`, `usa_posiciones`, `created_at`, `updated_at`) VALUES
(1, 'Onddo Cup 2026', '2026-03-30', '2026-04-04', 'Torneo solidario de Futbol Sala celebrado en semana santa en Elizondo.', NULL, '1', 5, 0, NOW(), NOW());

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `active`, `admin`, `created_at`, `updated_at`) VALUES
(1, 'Admin','admin@admin.es', NULL, '$2y$12$EEtG8qtCWNjTTSXMwDGDpe1aL78DrxWGP/sXbTivCITZp.HZuyO6m', NULL, '1', '1', NOW(), NOW());
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
