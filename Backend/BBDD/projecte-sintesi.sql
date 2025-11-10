-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 14-10-2025 a las 08:56:58
-- Versión del servidor: 8.0.43
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pruebas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aplicacio`
--

CREATE TABLE `aplicacio` (
  `id_aplicacio` int NOT NULL,
  `id_pla` int DEFAULT NULL,
  `data` date NOT NULL,
  `hora_inici` time DEFAULT NULL,
  `hora_fi` time DEFAULT NULL,
  `metode` enum('Fertirrigacio','Foliar','Sòl','Altres') DEFAULT NULL,
  `condicions_ambientals` text,
  `id_operari` int DEFAULT NULL,
  `id_equip` int DEFAULT NULL,
  `observacions` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `aplicacio`
--

INSERT INTO `aplicacio` (`id_aplicacio`, `id_pla`, `data`, `hora_inici`, `hora_fi`, `metode`, `condicions_ambientals`, `id_operari`, `id_equip`, `observacions`) VALUES
(1, 31, '2025-03-02', '08:00:00', '10:00:00', 'Foliar', 'Viento suave', 1, 1, 'Sin incidencias'),
(2, 32, '2025-05-12', '07:30:00', '09:00:00', 'Foliar', 'Mañana fresca', 2, 2, 'Plaga visible en bordes'),
(3, 33, '2025-02-02', '09:00:00', '11:00:00', 'Fertirrigacio', 'Nublado', 3, NULL, 'Aplicación en cabecera'),
(4, 34, '2025-10-02', '08:15:00', '09:45:00', 'Foliar', 'Seco y templado', 4, 3, 'Buena cobertura'),
(5, 35, '2025-09-02', '10:00:00', '11:30:00', 'Sòl', 'Suelo húmedo', 5, 7, 'Incorporado superficialmente'),
(6, 36, '2025-07-02', '06:30:00', '08:00:00', 'Foliar', 'Alta insolación', 6, 6, 'Evitar horas centrales'),
(7, 37, '2025-04-02', '08:45:00', '10:15:00', 'Fertirrigacio', 'Riego continuo', 7, NULL, 'Ajuste de caudal'),
(8, 38, '2025-05-03', '07:00:00', '08:30:00', 'Sòl', 'Post-lluvia', 8, 8, 'Buen control de hierbas'),
(9, 39, '2025-03-16', '09:30:00', '10:30:00', 'Foliar', 'Floración inicial', 9, 9, 'Aporte de calcio'),
(10, 40, '2025-08-03', '07:15:00', '08:45:00', 'Foliar', 'Calor moderado', 10, NULL, 'Revisión general'),
(11, 41, '2025-02-03', '09:45:00', '11:45:00', 'Altres', 'Viento suave', 11, 11, 'Sin incidencias'),
(12, 42, '2025-03-04', '06:15:00', '08:15:00', 'Fertirrigacio', 'Cielo despejado', 12, 12, 'Cobertura homogénea'),
(13, 43, '2025-04-05', '07:30:00', '09:30:00', 'Foliar', 'Viento suave', 13, 13, 'Sin incidencias'),
(14, 44, '2025-05-06', '08:45:00', '10:45:00', 'Sòl', 'Cielo despejado', 14, 14, 'Cobertura homogénea'),
(15, 45, '2025-06-07', '09:15:00', '11:15:00', 'Altres', 'Viento suave', 15, 15, 'Sin incidencias'),
(16, 46, '2025-07-08', '06:30:00', '08:30:00', 'Fertirrigacio', 'Cielo despejado', 16, 16, 'Cobertura homogénea'),
(17, 47, '2025-08-09', '07:45:00', '09:45:00', 'Foliar', 'Viento suave', 17, 17, 'Sin incidencias'),
(18, 48, '2025-09-01', '08:15:00', '10:15:00', 'Sòl', 'Cielo despejado', 18, 18, 'Cobertura homogénea'),
(19, 49, '2025-10-02', '09:30:00', '11:30:00', 'Altres', 'Viento suave', 19, 19, 'Sin incidencias'),
(20, 50, '2025-11-03', '06:45:00', '08:45:00', 'Fertirrigacio', 'Cielo despejado', 20, 20, 'Cobertura homogénea');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aplicacio_fila`
--

CREATE TABLE `aplicacio_fila` (
  `id_aplicacio` int NOT NULL,
  `id_fila` int NOT NULL,
  `estat` enum('Pendent','Fet','Parcial') DEFAULT 'Fet',
  `volum_caldo_l` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `aplicacio_fila`
--

INSERT INTO `aplicacio_fila` (`id_aplicacio`, `id_fila`, `estat`, `volum_caldo_l`) VALUES
(1, 1, 'Fet', 500.00),
(2, 2, 'Fet', 400.00),
(3, 1, 'Fet', 600.00),
(4, 2, 'Fet', 350.00),
(5, 1, 'Fet', 300.00),
(6, 2, 'Parcial', 280.00),
(7, 1, 'Fet', 450.00),
(8, 2, 'Fet', 320.00),
(9, 1, 'Fet', 250.00),
(10, 2, 'Fet', 300.00),
(11, 3, 'Fet', 340.00),
(12, 4, 'Parcial', 380.00),
(13, 5, 'Fet', 420.00),
(14, 6, 'Fet', 460.00),
(15, 7, 'Parcial', 300.00),
(16, 8, 'Fet', 340.00),
(17, 9, 'Fet', 380.00),
(18, 10, 'Parcial', 420.00),
(19, 11, 'Fet', 460.00),
(20, 12, 'Fet', 300.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aplicacio_producte`
--

CREATE TABLE `aplicacio_producte` (
  `id_aplicacio` int NOT NULL,
  `id_producte` int NOT NULL,
  `quantitat` decimal(10,3) NOT NULL,
  `unitat` enum('L','mL','kg','g') NOT NULL,
  `concentracio` varchar(50) DEFAULT NULL,
  `lot_referencia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `aplicacio_producte`
--

INSERT INTO `aplicacio_producte` (`id_aplicacio`, `id_producte`, `quantitat`, `unitat`, `concentracio`, `lot_referencia`) VALUES
(1, 1, 20.000, 'L', '2%', 'L001'),
(2, 2, 12.000, 'L', '1%', 'L002'),
(3, 3, 25.000, 'kg', '—', 'L003'),
(4, 8, 10.000, 'kg', '—', 'L008'),
(5, 7, 5.000, 'L', '—', 'L007'),
(6, 6, 3.000, 'L', '—', 'L006'),
(7, 9, 6.000, 'L', '—', 'L009'),
(8, 4, 8.000, 'L', '—', 'L004'),
(9, 5, 10.000, 'kg', '—', 'L005'),
(10, 10, 4.000, 'L', '—', 'L010'),
(11, 11, 6.000, 'L', '—', 'L011'),
(12, 12, 7.000, 'kg', '—', 'L012'),
(13, 13, 8.000, 'L', '—', 'L013'),
(14, 14, 9.000, 'kg', '—', 'L014'),
(15, 15, 5.000, 'L', '—', 'L015'),
(16, 16, 6.000, 'kg', '—', 'L016'),
(17, 17, 7.000, 'L', '—', 'L017'),
(18, 18, 8.000, 'kg', '—', 'L018'),
(19, 19, 9.000, 'L', '—', 'L019'),
(20, 20, 5.000, 'kg', '—', 'L020');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clima`
--

CREATE TABLE `clima` (
  `id_clima` int NOT NULL,
  `id_plantacio` int NOT NULL,
  `any_temp` int NOT NULL,
  `temperatura_mitjana` float DEFAULT NULL,
  `precipitacio_total` float DEFAULT NULL,
  `altres_factors` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clima`
--

INSERT INTO `clima` (`id_clima`, `id_plantacio`, `any_temp`, `temperatura_mitjana`, `precipitacio_total`, `altres_factors`) VALUES
(1, 1, 2025, 18.5, 450, 'Invierno suave'),
(2, 2, 2024, 20.2, 300, 'Verano muy seco'),
(3, 3, 2025, 20.5, 490, 'Registro automático estación'),
(4, 4, 2024, 21.5, 520, 'Registro automático estación'),
(5, 5, 2023, 17.5, 550, 'Registro automático estación'),
(6, 6, 2025, 18.5, 400, 'Registro automático estación'),
(7, 7, 2024, 19.5, 430, 'Registro automático estación'),
(8, 8, 2023, 20.5, 460, 'Registro automático estación'),
(9, 9, 2025, 21.5, 490, 'Registro automático estación'),
(10, 10, 2024, 17.5, 520, 'Registro automático estación'),
(11, 11, 2023, 18.5, 550, 'Registro automático estación'),
(12, 12, 2025, 19.5, 400, 'Registro automático estación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `data`
--

CREATE TABLE `data` (
  `id_data` int NOT NULL,
  `data_inici` date NOT NULL,
  `data_fi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `data`
--

INSERT INTO `data` (`id_data`, `data_inici`, `data_fi`) VALUES
(1, '2025-01-01', '2025-12-31'),
(2, '2024-01-01', '2024-12-31'),
(3, '2023-01-01', '2023-12-31'),
(4, '2022-01-01', '2022-12-31'),
(5, '2021-01-01', '2021-12-31'),
(6, '2020-01-01', '2020-12-31'),
(7, '2019-01-01', '2019-12-31'),
(8, '2018-01-01', '2018-12-31'),
(9, '2017-01-01', '2017-12-31'),
(10, '2016-01-01', '2016-12-31'),
(11, '2015-01-01', '2015-12-31'),
(12, '2014-01-01', '2014-12-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equip`
--

CREATE TABLE `equip` (
  `id_equip` int NOT NULL,
  `tipus` varchar(100) NOT NULL,
  `descripcio` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `equip`
--

INSERT INTO `equip` (`id_equip`, `tipus`, `descripcio`) VALUES
(1, 'Pulverizador', 'Equipo de mochila manual'),
(2, 'Tractor', 'Tractor John Deere 5050E'),
(3, 'Atomizador', 'Atomizador de arrastre 1000L'),
(4, 'Bomba eléctrica', 'Bomba de riego portátil'),
(5, 'Sistema de goteo', 'Instalación de riego por goteo'),
(6, 'Desbrozadora', 'Desbrozadora Stihl FS 260'),
(7, 'Sembradora', 'Sembradora mecánica'),
(8, 'Cisterna', 'Cisterna de agua 3000L'),
(9, 'Carretilla', 'Carretilla manual de transporte'),
(10, 'Equipo de protección', 'EPIs para aplicación de productos'),
(11, 'Podadora eléctrica', 'Tijera eléctrica de poda'),
(12, 'Remolque agrícola', 'Remolque basculante 3T'),
(13, 'Estación meteorológica', 'Registro meteo automático'),
(14, 'Dron agrícola', 'Dron para monitoreo NDVI'),
(15, 'GPS agrícola', 'Guía paralela en tractor'),
(16, 'Motocultor', 'Motocultor gasolina 7HP'),
(17, 'Hidrolavadora', 'Limpieza de equipos'),
(18, 'Medidor pH', 'Medidor pH/CE portátil'),
(19, 'Cuba 2000L', 'Depósito móvil 2000L'),
(20, 'Trituradora de restos', 'Trituradora de martillos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especie`
--

CREATE TABLE `especie` (
  `id_especie` int NOT NULL,
  `nom_cientific` varchar(255) NOT NULL,
  `nom_comu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `especie`
--

INSERT INTO `especie` (`id_especie`, `nom_cientific`, `nom_comu`) VALUES
(1, 'Prunus persica', 'Melocotonero'),
(2, 'Olea europaea', 'Olivo'),
(3, 'Vitis vinifera', 'Vid'),
(4, 'Citrus sinensis', 'Naranjo dulce'),
(5, 'Citrus limon', 'Limonero'),
(6, 'Pyrus communis', 'Peral'),
(7, 'Malus domestica', 'Manzano'),
(8, 'Prunus dulcis', 'Almendro'),
(9, 'Prunus armeniaca', 'Albaricoquero'),
(10, 'Prunus avium', 'Cerezo'),
(11, 'Ficus carica', 'Higuera'),
(12, 'Punica granatum', 'Granado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estat_fenologic`
--

CREATE TABLE `estat_fenologic` (
  `id_estat` int NOT NULL,
  `codi` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estat_fenologic`
--

INSERT INTO `estat_fenologic` (`id_estat`, `codi`, `nom`) VALUES
(1, 'E1', 'Reposo invernal'),
(2, 'E2', 'Brotación'),
(3, 'E3', 'Floración'),
(4, 'E4', 'Cuajado'),
(5, 'E5', 'Fructificación'),
(6, 'E6', 'Engorde'),
(7, 'E7', 'Envero'),
(8, 'E8', 'Maduración'),
(9, 'E9', 'Postcosecha'),
(10, 'E10', 'Parada vegetativa'),
(11, 'E11', 'Fase 11'),
(12, 'E12', 'Fase 12'),
(13, 'E13', 'Fase 13'),
(14, 'E14', 'Fase 14'),
(15, 'E15', 'Fase 15'),
(16, 'E16', 'Fase 16'),
(17, 'E17', 'Fase 17'),
(18, 'E18', 'Fase 18'),
(19, 'E19', 'Fase 19'),
(20, 'E20', 'Fase 20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fila`
--

CREATE TABLE `fila` (
  `id_fila` int NOT NULL,
  `id_increment` int NOT NULL,
  `numero_fila` int NOT NULL,
  `geometria_fila` geometry NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `fila`
--

INSERT INTO `fila` (`id_fila`, `id_increment`, `numero_fila`, `geometria_fila`) VALUES
(1, 1, 1, 0xe610000001020000000200000000000000000000000000000000000000000000000000f03f0000000000000000),
(2, 1, 2, 0xe61000000102000000020000000000000000000000000000000000f03f000000000000f03f000000000000f03f),
(3, 3, 1, 0xe6100000010200000002000000295c8fc2f5b84340c2f5285c8fc2e1bf295c8fc2f5b84340999999999999e1bf),
(4, 4, 1, 0xe61000000102000000020000007b14ae47e1ba434048e17a14ae47e1bf7b14ae47e1ba43401f85eb51b81ee1bf),
(5, 5, 1, 0xe6100000010200000002000000cdccccccccbc4340cdcccccccccce0bfcdccccccccbc4340a4703d0ad7a3e0bf),
(6, 6, 1, 0xe61000000102000000020000001f85eb51b8be434052b81e85eb51e0bf1f85eb51b8be4340295c8fc2f528e0bf),
(7, 7, 1, 0xe6100000010200000002000000703d0ad7a3c04340ae47e17a14aedfbf703d0ad7a3c043405c8fc2f5285cdfbf),
(8, 8, 1, 0xe6100000010200000002000000c2f5285c8fc24340b81e85eb51b8debfc2f5285c8fc24340666666666666debf),
(9, 9, 1, 0xe610000001020000000200000014ae47e17ac44340c2f5285c8fc2ddbf14ae47e17ac44340703d0ad7a370ddbf),
(10, 10, 1, 0xe61000000102000000020000006666666666c64340ccccccccccccdcbf6666666666c643407a14ae47e17adcbf),
(11, 11, 1, 0xe6100000010200000002000000b81e85eb51c84340d7a3703d0ad7dbbfb81e85eb51c8434085eb51b81e85dbbf),
(12, 12, 1, 0xe61000000102000000020000000ad7a3703dca4340e17a14ae47e1dabf0ad7a3703dca43408fc2f5285c8fdabf);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infraestructura`
--

CREATE TABLE `infraestructura` (
  `id_infra` int NOT NULL,
  `id_parcela` int DEFAULT NULL,
  `tipus` varchar(100) DEFAULT NULL,
  `descripcio` text,
  `geometria` geometry NOT NULL SRID 4326,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `infraestructura`
--

INSERT INTO `infraestructura` (`id_infra`, `id_parcela`, `tipus`, `descripcio`, `geometria`, `geometria_kml`, `foto_url`, `created_at`) VALUES
(1, 1, 'Riego', 'Sistema de riego por goteo', 0xe61000000101000000000000000000e03f000000000000e03f, '<kml></kml>', 'riego.jpg', '2025-09-30 07:58:01'),
(2, 2, 'Camino', 'Acceso principal', 0xe61000000101000000000000000000f83f000000000000f83f, '<kml></kml>', 'camino.jpg', '2025-09-30 07:58:01'),
(3, 3, 'Pozo', 'Infraestructura Pozo 3', 0xe61000000101000000e27a14ae47c143405d8fc2f5285cdfbf, '<kml></kml>', 'infra3.jpg', '2025-10-14 08:53:14'),
(4, 4, 'Caseta', 'Infraestructura Caseta 4', 0xe61000000101000000a4703d0ad7c3434015ae47e17a14debf, '<kml></kml>', 'infra4.jpg', '2025-10-14 08:53:14'),
(5, 5, 'Balsa', 'Infraestructura Balsa 5', 0xe610000001010000006766666666c64340ceccccccccccdcbf, '<kml></kml>', 'infra5.jpg', '2025-10-14 08:53:14'),
(6, 6, 'Valla', 'Infraestructura Valla 6', 0xe61000000101000000295c8fc2f5c8434086eb51b81e85dbbf, '<kml></kml>', 'infra6.jpg', '2025-10-14 08:53:14'),
(7, 7, 'Camino', 'Infraestructura Camino 7', 0xe61000000101000000ec51b81e85cb43403e0ad7a3703ddabf, '<kml></kml>', 'infra7.jpg', '2025-10-14 08:53:14'),
(8, 8, 'Riego', 'Infraestructura Riego 8', 0xe61000000101000000ae47e17a14ce4340f6285c8fc2f5d8bf, '<kml></kml>', 'infra8.jpg', '2025-10-14 08:53:14'),
(9, 9, 'Aljibe', 'Infraestructura Aljibe 9', 0xe61000000101000000713d0ad7a3d04340af47e17a14aed7bf, '<kml></kml>', 'infra9.jpg', '2025-10-14 08:53:14'),
(10, 10, 'Punto carga', 'Infraestructura Punto carga 10', 0xe610000001010000003433333333d34340676666666666d6bf, '<kml></kml>', 'infra10.jpg', '2025-10-14 08:53:14'),
(11, 11, 'Cuarto aperos', 'Infraestructura Cuarto aperos 11', 0xe61000000101000000f6285c8fc2d543402085eb51b81ed5bf, '<kml></kml>', 'infra11.jpg', '2025-10-14 08:53:14'),
(12, 12, 'Meteo', 'Infraestructura Meteo 12', 0xe61000000101000000b91e85eb51d84340d8a3703d0ad7d3bf, '<kml></kml>', 'infra12.jpg', '2025-10-14 08:53:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infra_parcela`
--

CREATE TABLE `infra_parcela` (
  `id_infra` int NOT NULL,
  `id_parcela` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `infra_parcela`
--

INSERT INTO `infra_parcela` (`id_infra`, `id_parcela`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `magatzem`
--

CREATE TABLE `magatzem` (
  `id_magatzem` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `ubicacio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `magatzem`
--

INSERT INTO `magatzem` (`id_magatzem`, `nom`, `ubicacio`) VALUES
(1, 'Almacén Central', 'Valencia'),
(2, 'Depósito Norte', 'Castellón'),
(3, 'Almacén Sur', 'Alicante'),
(4, 'Depósito Este', 'Requena'),
(5, 'Zona Fría', 'Valencia'),
(6, 'Depósito de líquidos', 'Alzira'),
(7, 'Almacén de sólidos', 'Sueca'),
(8, 'Depósito de fertilizantes', 'Xàtiva'),
(9, 'Almacén antiguo', 'Gandía'),
(10, 'Depósito auxiliar', 'Onda'),
(11, 'Almacén Oeste', 'Utiel'),
(12, 'Depósito Sur 2', 'Elx'),
(13, 'Almacén Montes', 'Morella'),
(14, 'Depósito Ribera', 'Cullera'),
(15, 'Cámara frigorífica', 'Sagunt'),
(16, 'Nave Auxiliar', 'Ontinyent'),
(17, 'Depósito químicos', 'Paterna'),
(18, 'Almacén recambios', 'Torrent'),
(19, 'Depósito fitosanitarios', 'Alfafar'),
(20, 'Base de campo', 'Buñol');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moviment_estoc`
--

CREATE TABLE `moviment_estoc` (
  `id_mov` int NOT NULL,
  `id_lot` int NOT NULL,
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quantitat` decimal(12,3) NOT NULL,
  `motiu` enum('Compra','Ajust','Aplicacio') NOT NULL,
  `id_aplicacio` int DEFAULT NULL,
  `observacions` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `moviment_estoc`
--

INSERT INTO `moviment_estoc` (`id_mov`, `id_lot`, `data`, `quantitat`, `motiu`, `id_aplicacio`, `observacions`) VALUES
(1, 1, '2025-01-01 10:00:00', 50.000, 'Compra', NULL, 'Alta de stock'),
(2, 2, '2025-02-01 10:00:00', 40.000, 'Compra', NULL, 'Alta de stock'),
(3, 3, '2025-03-01 10:00:00', 100.000, 'Compra', NULL, 'Alta de stock'),
(4, 4, '2025-03-20 10:00:00', 75.000, 'Compra', NULL, 'Alta de stock'),
(5, 5, '2025-04-01 10:00:00', 90.000, 'Compra', NULL, 'Alta de stock'),
(6, 1, '2025-03-02 11:00:00', -20.000, 'Aplicacio', 1, 'Usado en aplicació 1'),
(7, 2, '2025-05-12 09:10:00', -12.000, 'Aplicacio', 2, 'Usado en aplicació 2'),
(8, 3, '2025-02-02 11:10:00', -25.000, 'Aplicacio', 3, 'Usado en aplicació 3'),
(9, 8, '2025-10-02 09:50:00', -10.000, 'Aplicacio', 4, 'Usado en aplicació 4'),
(10, 5, '2025-03-16 10:40:00', -10.000, 'Aplicacio', 9, 'Usado en aplicació 9'),
(11, 11, '2025-01-11 00:00:00', 70.000, 'Compra', NULL, 'Alta de stock'),
(12, 12, '2025-06-02 00:00:00', -5.000, 'Aplicacio', 12, 'Consumo en aplicación 12'),
(13, 13, '2025-01-13 00:00:00', 60.000, 'Compra', NULL, 'Alta de stock'),
(14, 14, '2025-06-04 00:00:00', -9.000, 'Aplicacio', 14, 'Consumo en aplicación 14'),
(15, 15, '2025-01-15 00:00:00', 50.000, 'Compra', NULL, 'Alta de stock'),
(16, 16, '2025-06-06 00:00:00', -5.000, 'Aplicacio', 16, 'Consumo en aplicación 16'),
(17, 17, '2025-01-17 00:00:00', 70.000, 'Compra', NULL, 'Alta de stock'),
(18, 18, '2025-06-08 00:00:00', -9.000, 'Aplicacio', 18, 'Consumo en aplicación 18'),
(19, 19, '2025-01-19 00:00:00', 60.000, 'Compra', NULL, 'Alta de stock'),
(20, 20, '2025-06-10 00:00:00', -5.000, 'Aplicacio', 20, 'Consumo en aplicación 20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operari`
--

CREATE TABLE `operari` (
  `id_operari` int NOT NULL,
  `nom` varchar(200) NOT NULL,
  `carnet_aplicador` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `operari`
--

INSERT INTO `operari` (`id_operari`, `nom`, `carnet_aplicador`) VALUES
(1, 'Juan Pérez', 'APL-001'),
(2, 'María Gómez', 'APL-002'),
(3, 'Carlos Ruiz', 'APL-003'),
(4, 'Lucía Torres', 'APL-004'),
(5, 'Miguel Sánchez', 'APL-005'),
(6, 'Ana López', 'APL-006'),
(7, 'David Morales', 'APL-007'),
(8, 'Sara Fernández', 'APL-008'),
(9, 'Jorge Navarro', 'APL-009'),
(10, 'Elena Ramos', 'APL-010'),
(11, 'Pedro Castillo', 'APL-011'),
(12, 'Laura Medina', 'APL-012'),
(13, 'Hugo Martínez', 'APL-013'),
(14, 'Patricia Vega', 'APL-014'),
(15, 'Alberto Ortiz', 'APL-015'),
(16, 'Cristina Núñez', 'APL-016'),
(17, 'Sergio Romero', 'APL-017'),
(18, 'Natalia Ibáñez', 'APL-018'),
(19, 'Óscar Díaz', 'APL-019'),
(20, 'Verónica Gil', 'APL-020');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parcela`
--

CREATE TABLE `parcela` (
  `id_parcela` int NOT NULL,
  `ref_cadastral` varchar(50) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `descripcio` text,
  `municipi` varchar(100) DEFAULT NULL,
  `geometria` geometry NOT NULL SRID 4326,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `edafo` text,
  `documentacio` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `parcela`
--

INSERT INTO `parcela` (`id_parcela`, `ref_cadastral`, `nom`, `superficie`, `descripcio`, `municipi`, `geometria`, `geometria_kml`, `foto_url`, `edafo`, `documentacio`, `created_at`) VALUES
(1, '123ABC', 'Parcela Norte', 15000.00, 'Parcela de prueba', 'Valencia', 0xe6100000010100000000000000000000000000000000000000, '<kml></kml>', 'foto1.jpg', 'Edafo info', 'Doc info', '2025-09-30 07:57:46'),
(2, '456DEF', 'Parcela Sur', 12000.00, 'Otra parcela', 'Castellón', 0xe61000000101000000000000000000f03f000000000000f03f, '<kml></kml>', 'foto2.jpg', 'Edafo info', 'Doc info', '2025-09-30 07:57:46'),
(3, '300XYZ', 'Parcela 3', 11500.00, 'Parcela 3 de prueba', 'Valencia', 0xe61000000101000000a4703d0ad7c3434014ae47e17a14debf, '<kml></kml>', 'foto3.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(4, '400XYZ', 'Parcela 4', 12000.00, 'Parcela 4 de prueba', 'Castellón', 0xe6100000010100000085eb51b81ec54340713d0ad7a370ddbf, '<kml></kml>', 'foto4.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(5, '500XYZ', 'Parcela 5', 12500.00, 'Parcela 5 de prueba', 'Alicante', 0xe610000001010000006666666666c64340cdccccccccccdcbf, '<kml></kml>', 'foto5.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(6, '600XYZ', 'Parcela 6', 13000.00, 'Parcela 6 de prueba', 'Xàtiva', 0xe6100000010100000048e17a14aec74340295c8fc2f528dcbf, '<kml></kml>', 'foto6.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(7, '700XYZ', 'Parcela 7', 13500.00, 'Parcela 7 de prueba', 'Requena', 0xe61000000101000000295c8fc2f5c8434085eb51b81e85dbbf, '<kml></kml>', 'foto7.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(8, '800XYZ', 'Parcela 8', 14000.00, 'Parcela 8 de prueba', 'Gandía', 0xe610000001010000000ad7a3703dca4340e17a14ae47e1dabf, '<kml></kml>', 'foto8.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(9, '900XYZ', 'Parcela 9', 14500.00, 'Parcela 9 de prueba', 'Ontinyent', 0xe61000000101000000ec51b81e85cb43403e0ad7a3703ddabf, '<kml></kml>', 'foto9.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(10, '1000XYZ', 'Parcela 10', 15000.00, 'Parcela 10 de prueba', 'Alzira', 0xe61000000101000000cdcccccccccc43409a9999999999d9bf, '<kml></kml>', 'foto10.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(11, '1100XYZ', 'Parcela 11', 15500.00, 'Parcela 11 de prueba', 'Sagunt', 0xe61000000101000000ae47e17a14ce4340f6285c8fc2f5d8bf, '<kml></kml>', 'foto11.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14'),
(12, '1200XYZ', 'Parcela 12', 16000.00, 'Parcela 12 de prueba', 'Onda', 0xe610000001010000008fc2f5285ccf434052b81e85eb51d8bf, '<kml></kml>', 'foto12.jpg', 'Edafo info', 'Doc info', '2025-10-14 08:53:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantacio`
--

CREATE TABLE `plantacio` (
  `id_plantacio` int NOT NULL,
  `id_sector` int NOT NULL,
  `id_varietat` int NOT NULL,
  `data_plantacio` date NOT NULL,
  `data_inici` date NOT NULL,
  `data_fi` date DEFAULT NULL,
  `marc_plantacio_files` decimal(5,2) DEFAULT NULL,
  `marc_plantacio_arbres` decimal(5,2) DEFAULT NULL,
  `num_arbres_total` int DEFAULT NULL,
  `origen_material` varchar(255) DEFAULT NULL,
  `certificacions` varchar(255) DEFAULT NULL,
  `sistema_formacio` varchar(100) DEFAULT NULL,
  `inversio_inicial` decimal(12,2) DEFAULT NULL,
  `entrada_produccio_prevista` year DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `plantacio`
--

INSERT INTO `plantacio` (`id_plantacio`, `id_sector`, `id_varietat`, `data_plantacio`, `data_inici`, `data_fi`, `marc_plantacio_files`, `marc_plantacio_arbres`, `num_arbres_total`, `origen_material`, `certificacions`, `sistema_formacio`, `inversio_inicial`, `entrada_produccio_prevista`) VALUES
(1, 1, 1, '2025-02-01', '2025-02-01', NULL, 3.00, 2.00, 500, 'Vivero Local', 'Certif ECO', 'Eje central', 10000.00, '2027'),
(2, 2, 2, '2024-03-01', '2024-03-01', NULL, 4.00, 2.50, 400, 'Vivero Regional', 'Certif BIO', 'Vaso libre', 8000.00, '2026'),
(3, 3, 3, '2024-04-01', '2024-04-01', NULL, 3.00, 3.50, 360, 'Vivero Regional', 'Certif ECO', 'Eje central', 7900.00, '2026'),
(4, 4, 4, '2024-05-01', '2024-05-01', NULL, 3.50, 2.00, 380, 'Vivero Regional', 'Certif ECO', 'Vaso libre', 8200.00, '2027'),
(5, 5, 5, '2024-06-01', '2024-06-01', NULL, 4.00, 2.50, 400, 'Vivero Regional', 'Certif ECO', 'Eje central', 8500.00, '2028'),
(6, 6, 6, '2024-07-01', '2024-07-01', NULL, 3.00, 3.00, 420, 'Vivero Regional', 'Certif ECO', 'Vaso libre', 8800.00, '2026'),
(7, 7, 7, '2024-08-01', '2024-08-01', NULL, 3.50, 3.50, 440, 'Vivero Regional', 'Certif ECO', 'Eje central', 9100.00, '2027'),
(8, 8, 8, '2024-09-01', '2024-09-01', NULL, 4.00, 2.00, 460, 'Vivero Regional', 'Certif ECO', 'Vaso libre', 9400.00, '2028'),
(9, 9, 9, '2024-10-01', '2024-10-01', NULL, 3.00, 2.50, 480, 'Vivero Regional', 'Certif ECO', 'Eje central', 9700.00, '2026'),
(10, 10, 10, '2024-11-01', '2024-11-01', NULL, 3.50, 3.00, 500, 'Vivero Regional', 'Certif ECO', 'Vaso libre', 10000.00, '2027'),
(11, 11, 11, '2024-12-01', '2024-12-01', NULL, 4.00, 3.50, 520, 'Vivero Regional', 'Certif ECO', 'Eje central', 10300.00, '2028'),
(12, 12, 12, '2024-01-01', '2024-01-01', NULL, 3.00, 2.00, 540, 'Vivero Regional', 'Certif ECO', 'Vaso libre', 10600.00, '2026');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pla_fila`
--

CREATE TABLE `pla_fila` (
  `id_pla` int NOT NULL,
  `id_fila` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pla_fila`
--

INSERT INTO `pla_fila` (`id_pla`, `id_fila`) VALUES
(31, 1),
(33, 1),
(35, 1),
(37, 1),
(39, 1),
(32, 2),
(34, 2),
(36, 2),
(38, 2),
(40, 2),
(41, 3),
(42, 4),
(43, 5),
(44, 6),
(45, 7),
(46, 8),
(47, 9),
(48, 10),
(49, 11),
(50, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pla_producte`
--

CREATE TABLE `pla_producte` (
  `id_pla` int NOT NULL,
  `id_producte` int NOT NULL,
  `dosi` varchar(100) NOT NULL,
  `volum_caldo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pla_producte`
--

INSERT INTO `pla_producte` (`id_pla`, `id_producte`, `dosi`, `volum_caldo`) VALUES
(31, 1, '200 ml/hl', '1000 L'),
(32, 2, '100 ml/hl', '800 L'),
(33, 3, '500 g/100L', '1200 L'),
(34, 8, '150 g/hl', '900 L'),
(35, 7, '1 L/ha', '—'),
(36, 6, '0.5 L/ha', '—'),
(37, 9, '2 L/ha', '—'),
(38, 4, '2 L/ha', '—'),
(39, 5, '2 kg/ha', '—'),
(40, 10, '0.3 L/ha', '—'),
(41, 11, 'ver etiqueta', '1000 L'),
(42, 12, 'ver etiqueta', '1000 L'),
(43, 13, 'ver etiqueta', '1000 L'),
(44, 14, 'ver etiqueta', '1000 L'),
(45, 15, 'ver etiqueta', '1000 L'),
(46, 16, 'ver etiqueta', '1000 L'),
(47, 17, 'ver etiqueta', '1000 L'),
(48, 18, 'ver etiqueta', '1000 L'),
(49, 19, 'ver etiqueta', '1000 L'),
(50, 20, 'ver etiqueta', '1000 L');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pla_sector`
--

CREATE TABLE `pla_sector` (
  `id_pla` int NOT NULL,
  `id_sector` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pla_sector`
--

INSERT INTO `pla_sector` (`id_pla`, `id_sector`) VALUES
(31, 1),
(33, 1),
(35, 1),
(37, 1),
(39, 1),
(32, 2),
(34, 2),
(36, 2),
(38, 2),
(40, 2),
(41, 3),
(42, 4),
(43, 5),
(44, 6),
(45, 7),
(46, 8),
(47, 9),
(48, 10),
(49, 11),
(50, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pla_tractament`
--

CREATE TABLE `pla_tractament` (
  `id_pla` int NOT NULL,
  `nom` varchar(200) NOT NULL,
  `tipus` enum('Preventiu','Curatiu') NOT NULL,
  `id_estat_inici` int DEFAULT NULL,
  `id_estat_fi` int DEFAULT NULL,
  `id_varietat` int DEFAULT NULL,
  `finestra_data_inici` date DEFAULT NULL,
  `finestra_data_fi` date DEFAULT NULL,
  `plaga_malaltia_objectiu` varchar(255) DEFAULT NULL,
  `observacions` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pla_tractament`
--

INSERT INTO `pla_tractament` (`id_pla`, `nom`, `tipus`, `id_estat_inici`, `id_estat_fi`, `id_varietat`, `finestra_data_inici`, `finestra_data_fi`, `plaga_malaltia_objectiu`, `observacions`) VALUES
(31, 'Tratamiento Primavera', 'Preventiu', 1, 1, 1, '2025-03-01', '2025-03-10', 'Mildiu', 'Aplicar con temperatura inferior a 25°C'),
(32, 'Control de Pulgones', 'Curatiu', 1, 1, 2, '2025-05-10', '2025-05-20', 'Pulgón', 'Repetir si reaparece plaga'),
(33, 'Fertilización Inicial', 'Preventiu', 1, 1, 1, '2025-02-01', '2025-02-05', 'Déficit de nitrógeno', 'Aplicar antes de brotación'),
(34, 'Tratamiento Otoño', 'Curatiu', 1, 1, 2, '2025-10-01', '2025-10-10', 'Hongos foliares', 'Aplicar tras poda'),
(35, 'Abono Orgánico', 'Preventiu', 1, 1, 1, '2025-09-01', '2025-09-10', 'Mejora del suelo', 'Aplicar compost'),
(36, 'Tratamiento Verano', 'Curatiu', 1, 1, 2, '2025-07-01', '2025-07-05', 'Araña roja', 'Aplicar acaricida B'),
(37, 'Micronutrientes', 'Preventiu', 1, 1, 1, '2025-04-01', '2025-04-05', 'Deficiencias menores', 'Zinc y manganeso'),
(38, 'Control Hierbas', 'Curatiu', 1, 1, 2, '2025-05-01', '2025-05-10', 'Hierbas adventicias', 'Aplicar herbicida Z'),
(39, 'Refuerzo Floración', 'Preventiu', 1, 1, 1, '2025-03-15', '2025-03-20', 'Floración débil', 'Añadir calcio'),
(40, 'Mantenimiento General', 'Curatiu', 1, 1, 2, '2025-08-01', '2025-08-10', 'Plagas múltiples', 'Revisión general'),
(41, 'Pla 41 Prev', 'Preventiu', 11, 11, 3, '2025-06-01', '2025-06-10', 'Oídio', 'Aplicar según etiqueta'),
(42, 'Pla 42 Cur', 'Curatiu', 12, 12, 4, '2025-07-01', '2025-07-10', 'Mosca', 'Aplicar según etiqueta'),
(43, 'Pla 43 Prev', 'Preventiu', 13, 13, 5, '2025-08-01', '2025-08-10', 'Oídio', 'Aplicar según etiqueta'),
(44, 'Pla 44 Cur', 'Curatiu', 14, 14, 6, '2025-09-01', '2025-09-10', 'Mosca', 'Aplicar según etiqueta'),
(45, 'Pla 45 Prev', 'Preventiu', 15, 15, 7, '2025-10-01', '2025-10-10', 'Oídio', 'Aplicar según etiqueta'),
(46, 'Pla 46 Cur', 'Curatiu', 16, 16, 8, '2025-11-01', '2025-11-10', 'Mosca', 'Aplicar según etiqueta'),
(47, 'Pla 47 Prev', 'Preventiu', 17, 17, 9, '2025-12-01', '2025-12-10', 'Oídio', 'Aplicar según etiqueta'),
(48, 'Pla 48 Cur', 'Curatiu', 18, 18, 10, '2025-01-01', '2025-01-10', 'Mosca', 'Aplicar según etiqueta'),
(49, 'Pla 49 Prev', 'Preventiu', 19, 19, 11, '2025-02-01', '2025-02-10', 'Oídio', 'Aplicar según etiqueta'),
(50, 'Pla 50 Cur', 'Curatiu', 20, 20, 12, '2025-03-01', '2025-03-10', 'Mosca', 'Aplicar según etiqueta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producte`
--

CREATE TABLE `producte` (
  `id_producte` int NOT NULL,
  `tipus` enum('Fitosanitari','Fertilitzant') NOT NULL,
  `nom_comercial` varchar(255) NOT NULL,
  `materia_activa` varchar(255) DEFAULT NULL,
  `concentracio` varchar(100) DEFAULT NULL,
  `espectre_accio` text,
  `cultius_autoritzats` text,
  `dosi_recomendada` varchar(100) DEFAULT NULL,
  `dosi_maxima` varchar(100) DEFAULT NULL,
  `termini_seguretat_dies` int DEFAULT NULL,
  `classificacio_toxicologica` varchar(100) DEFAULT NULL,
  `restriccions_usu` text,
  `compatible_integrada` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `producte`
--

INSERT INTO `producte` (`id_producte`, `tipus`, `nom_comercial`, `materia_activa`, `concentracio`, `espectre_accio`, `cultius_autoritzats`, `dosi_recomendada`, `dosi_maxima`, `termini_seguretat_dies`, `classificacio_toxicologica`, `restriccions_usu`, `compatible_integrada`) VALUES
(1, 'Fitosanitari', 'Fungicida X', 'Cobre', '20%', 'Control de mildiu', 'Melocotonero, Olivo', '200 ml/hl', '300 ml/hl', 14, 'Nocivo', 'Evitar altas temperaturas', 1),
(2, 'Fitosanitari', 'Insecticida A', 'Imidacloprid', '10%', 'Control de pulgones', 'Olivo', '100 ml/hl', '150 ml/hl', 21, 'Tóxico', 'Prohibido en floración', 0),
(3, 'Fertilitzant', 'NPK 20-20-20', 'N-P-K', '20%', 'Crecimiento general', 'Todos', '500 g/100L', '1 kg/100L', 0, 'No tóxico', '', 1),
(4, 'Fitosanitari', 'Herbicida Z', 'Glifosato', '36%', 'Control de malas hierbas', 'Olivar', '2 L/ha', '4 L/ha', 30, 'Peligroso', 'Evitar contacto con suelo húmedo', 0),
(5, 'Fertilitzant', 'Calcio Plus', 'CaCO3', '15%', 'Fortalecimiento del fruto', 'Melocotonero', '2 kg/ha', '5 kg/ha', 0, 'No tóxico', '', 1),
(6, 'Fitosanitari', 'Acaricida B', 'Abamectina', '1.8%', 'Control de ácaros', 'Melocotonero', '0.5 L/ha', '1 L/ha', 10, 'Tóxico', 'Evitar sobreaplicación', 1),
(7, 'Fertilitzant', 'Humic 25', 'Ácidos húmicos', '25%', 'Mejora del suelo', 'Todos', '1 L/ha', '2 L/ha', 0, 'No tóxico', '', 1),
(8, 'Fitosanitari', 'Fungicida C', 'Mancozeb', '80%', 'Control de hongos', 'Melocotonero', '150 g/hl', '300 g/hl', 14, 'Irritante', '', 1),
(9, 'Fertilitzant', 'AminoGrow', 'Aminoácidos', '10%', 'Mejor vigor', 'Todos', '2 L/ha', '4 L/ha', 0, 'No tóxico', '', 1),
(10, 'Fitosanitari', 'Insecticida D', 'Lambda-cihalotrina', '2.5%', 'Control de orugas', 'Olivar', '0.3 L/ha', '0.5 L/ha', 7, 'Tóxico', 'Evitar contacto con abejas', 1),
(11, 'Fitosanitari', 'Fungicida D', 'Cimoxanilo', '5%', 'Antimildiu', 'Vid', '100 g/hl', '200 g/hl', 10, 'Irritante', 'Respetar plazos', 1),
(12, 'Fertilitzant', 'Potasa K40', 'K2O', '40%', 'Aporte de potasio', 'Todos', '20 kg/ha', '40 kg/ha', 0, 'No tóxico', '', 1),
(13, 'Fitosanitari', 'Insecticida E', 'Spinosad', '0.5%', 'Trips y mosca', 'Cítricos', '0.3 L/ha', '0.6 L/ha', 7, 'Tóxico', 'Evitar abejas', 1),
(14, 'Fertilitzant', 'Fosfito P30', 'Fosfito potásico', '30%', 'Inductor defensas', 'Todos', '2 L/ha', '4 L/ha', 0, 'No tóxico', '', 1),
(15, 'Fitosanitari', 'Herbicida Selectivo', 'Fluazifop-P', '12.5%', 'Gramíneas', 'Almendro, Olivo', '1 L/ha', '2 L/ha', 30, 'Peligroso', 'Evitar deriva', 0),
(16, 'Fertilitzant', 'Quelato Fe6', 'Hierro (EDDHA)', '6%', 'Clorosis férrica', 'Frutales', '4 kg/ha', '8 kg/ha', 0, 'No tóxico', '', 1),
(17, 'Fitosanitari', 'Acaricida C', 'Fenpiroximato', '5%', 'Ácaros', 'Frutales', '0.5 L/ha', '1 L/ha', 10, 'Tóxico', 'No mezclar con aceites', 1),
(18, 'Fertilitzant', 'BoroPlus', 'Boro', '11%', 'Cuajado flor', 'Almendro', '1 L/ha', '2 L/ha', 0, 'No tóxico', '', 1),
(19, 'Fitosanitari', 'Fungicida Sistémico', 'Tebuconazol', '25%', 'Oídio', 'Manzano', '0.3 L/ha', '0.5 L/ha', 14, 'Irritante', 'Evitar altas Tª', 1),
(20, 'Fertilitzant', 'CalMag', 'Ca+Mg', '10%', 'Equilibrio Ca/Mg', 'Todos', '2 L/ha', '4 L/ha', 0, 'No tóxico', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producte_lot`
--

CREATE TABLE `producte_lot` (
  `id_lot` int NOT NULL,
  `id_producte` int NOT NULL,
  `numero_lot` varchar(100) NOT NULL,
  `data_caducitat` date DEFAULT NULL,
  `id_magatzem` int DEFAULT NULL,
  `quantitat_disponible` decimal(12,3) NOT NULL DEFAULT '0.000',
  `unitat` enum('L','mL','kg','g') NOT NULL,
  `fabricant` varchar(255) DEFAULT NULL,
  `proveidor` varchar(255) DEFAULT NULL,
  `data_compra` date DEFAULT NULL,
  `preu_unitari` decimal(12,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `producte_lot`
--

INSERT INTO `producte_lot` (`id_lot`, `id_producte`, `numero_lot`, `data_caducitat`, `id_magatzem`, `quantitat_disponible`, `unitat`, `fabricant`, `proveidor`, `data_compra`, `preu_unitari`) VALUES
(1, 1, 'L001', '2026-01-10', 1, 50.000, 'L', 'AgroChem', 'Proveidor A', '2025-01-01', 12.5000),
(2, 2, 'L002', '2026-03-15', 2, 40.000, 'L', 'BioFarm', 'Proveidor B', '2025-02-01', 15.0000),
(3, 3, 'L003', '2027-05-20', 3, 100.000, 'kg', 'AgroNutrient', 'Proveidor C', '2025-03-01', 8.5000),
(4, 4, 'L004', '2026-12-31', 4, 75.000, 'L', 'HerbiMax', 'Proveidor D', '2025-03-20', 10.0000),
(5, 5, 'L005', '2027-04-10', 5, 90.000, 'kg', 'CalciCorp', 'Proveidor E', '2025-04-01', 9.7500),
(6, 6, 'L006', '2026-08-01', 6, 30.000, 'L', 'AgroChem', 'Proveidor F', '2025-04-10', 14.2000),
(7, 7, 'L007', '2027-01-15', 7, 120.000, 'L', 'HumicGrow', 'Proveidor G', '2025-05-01', 11.0000),
(8, 8, 'L008', '2026-09-09', 8, 55.000, 'kg', 'MancoFarm', 'Proveidor H', '2025-05-15', 10.3000),
(9, 9, 'L009', '2027-06-30', 9, 70.000, 'L', 'AminoPro', 'Proveidor I', '2025-06-01', 13.6000),
(10, 10, 'L010', '2026-07-25', 10, 45.000, 'L', 'Insecta', 'Proveidor J', '2025-06-10', 16.8000),
(11, 11, 'L011', '2027-12-20', 11, 80.000, 'L', 'Fabricant 11', 'Proveidor A', '2025-01-01', 16.0000),
(12, 12, 'L012', '2027-01-20', 12, 100.000, 'kg', 'Fabricant 12', 'Proveidor B', '2025-02-01', 17.5000),
(13, 13, 'L013', '2027-02-20', 13, 120.000, 'L', 'Fabricant 13', 'Proveidor C', '2025-03-01', 19.0000),
(14, 14, 'L014', '2027-03-20', 14, 140.000, 'kg', 'Fabricant 14', 'Proveidor D', '2025-04-01', 10.0000),
(15, 15, 'L015', '2027-04-20', 15, 60.000, 'L', 'Fabricant 15', 'Proveidor E', '2025-05-01', 11.5000),
(16, 16, 'L016', '2027-05-20', 16, 80.000, 'kg', 'Fabricant 16', 'Proveidor F', '2025-06-01', 13.0000),
(17, 17, 'L017', '2027-06-20', 17, 100.000, 'L', 'Fabricant 17', 'Proveidor G', '2025-07-01', 14.5000),
(18, 18, 'L018', '2027-07-20', 18, 120.000, 'kg', 'Fabricant 18', 'Proveidor H', '2025-08-01', 16.0000),
(19, 19, 'L019', '2027-08-20', 19, 140.000, 'L', 'Fabricant 19', 'Proveidor I', '2025-09-01', 17.5000),
(20, 20, 'L020', '2027-09-20', 20, 60.000, 'kg', 'Fabricant 20', 'Proveidor J', '2025-10-01', 19.0000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registre`
--

CREATE TABLE `registre` (
  `id_registre` int NOT NULL,
  `id_varietat` int NOT NULL,
  `id_plantacio` int DEFAULT NULL,
  `data_plantacio` date NOT NULL,
  `data_arrencada` date DEFAULT NULL,
  `rendiment` decimal(10,2) DEFAULT NULL,
  `problemes_fitosanitaris` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `registre`
--

INSERT INTO `registre` (`id_registre`, `id_varietat`, `id_plantacio`, `data_plantacio`, `data_arrencada`, `rendiment`, `problemes_fitosanitaris`) VALUES
(1, 1, 1, '2025-02-01', NULL, 2000.00, 'Mildiu en primavera'),
(2, 2, 2, '2024-03-01', NULL, 1500.00, 'Plaga de pulgón'),
(3, 3, 3, '2024-09-01', NULL, 1610.00, 'Sin problemas significativos'),
(4, 4, 4, '2024-10-01', NULL, 1680.00, 'Sin problemas significativos'),
(5, 5, 5, '2024-11-01', NULL, 1750.00, 'Sin problemas significativos'),
(6, 6, 6, '2024-12-01', NULL, 1820.00, 'Sin problemas significativos'),
(7, 7, 7, '2024-01-01', NULL, 1890.00, 'Sin problemas significativos'),
(8, 8, 8, '2024-02-01', NULL, 1960.00, 'Sin problemas significativos'),
(9, 9, 9, '2024-03-01', NULL, 2030.00, 'Sin problemas significativos'),
(10, 10, 10, '2024-04-01', NULL, 2100.00, 'Sin problemas significativos'),
(11, 11, 11, '2024-05-01', NULL, 2170.00, 'Sin problemas significativos'),
(12, 12, 12, '2024-06-01', NULL, 2240.00, 'Sin problemas significativos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id_sector` int NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `geometria` geometry NOT NULL SRID 4326,
  `geometria_kml` longtext NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id_sector`, `nom`, `superficie`, `geometria`, `geometria_kml`, `foto_url`, `created_at`) VALUES
(1, 'Sector A', 5000.00, 0xe610000001030000000100000005000000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f000000000000f03f000000000000f03f000000000000000000000000000000000000000000000000, '<kml></kml>', 'sectorA.jpg', '2025-09-30 07:57:48'),
(2, 'Sector B', 4000.00, 0xe610000001030000000100000005000000000000000000f03f000000000000f03f000000000000f03f0000000000000040000000000000004000000000000000400000000000000040000000000000f03f000000000000f03f000000000000f03f, '<kml></kml>', 'sectorB.jpg', '2025-09-30 07:57:48'),
(3, 'Sector B', 3800.00, 0xe6100000010300000001000000050000007b14ae47e1ba434048e17a14ae47e1bf7b14ae47e1ba4340f6285c8fc2f5e0bf5c8fc2f528bc4340f6285c8fc2f5e0bf5c8fc2f528bc434048e17a14ae47e1bf7b14ae47e1ba434048e17a14ae47e1bf, '<kml></kml>', 'sector3.jpg', '2025-10-14 08:53:14'),
(4, 'Sector C', 3900.00, 0xe6100000010300000001000000050000003d0ad7a370bd4340a4703d0ad7a3e0bf3d0ad7a370bd434052b81e85eb51e0bf1e85eb51b8be434052b81e85eb51e0bf1e85eb51b8be4340a4703d0ad7a3e0bf3d0ad7a370bd4340a4703d0ad7a3e0bf, '<kml></kml>', 'sector4.jpg', '2025-10-14 08:53:14'),
(5, 'Sector D', 4000.00, 0xe6100000010300000001000000050000000000000000c04340000000000000e0bf0000000000c043405c8fc2f5285cdfbfe17a14ae47c143405c8fc2f5285cdfbfe17a14ae47c14340000000000000e0bf0000000000c04340000000000000e0bf, '<kml></kml>', 'sector5.jpg', '2025-10-14 08:53:14'),
(6, 'Sector E', 4100.00, 0xe610000001030000000100000005000000c2f5285c8fc24340b81e85eb51b8debfc2f5285c8fc2434014ae47e17a14debfa3703d0ad7c3434014ae47e17a14debfa3703d0ad7c34340b81e85eb51b8debfc2f5285c8fc24340b81e85eb51b8debf, '<kml></kml>', 'sector6.jpg', '2025-10-14 08:53:14'),
(7, 'Sector F', 4200.00, 0xe61000000103000000010000000500000085eb51b81ec54340703d0ad7a370ddbf85eb51b81ec54340ccccccccccccdcbf6666666666c64340ccccccccccccdcbf6666666666c64340703d0ad7a370ddbf85eb51b81ec54340703d0ad7a370ddbf, '<kml></kml>', 'sector7.jpg', '2025-10-14 08:53:14'),
(8, 'Sector G', 4300.00, 0xe61000000103000000010000000500000047e17a14aec74340285c8fc2f528dcbf47e17a14aec7434084eb51b81e85dbbf285c8fc2f5c8434084eb51b81e85dbbf285c8fc2f5c84340285c8fc2f528dcbf47e17a14aec74340285c8fc2f528dcbf, '<kml></kml>', 'sector8.jpg', '2025-10-14 08:53:14'),
(9, 'Sector H', 4400.00, 0xe6100000010300000001000000050000000ad7a3703dca4340e17a14ae47e1dabf0ad7a3703dca43403d0ad7a3703ddabfeb51b81e85cb43403d0ad7a3703ddabfeb51b81e85cb4340e17a14ae47e1dabf0ad7a3703dca4340e17a14ae47e1dabf, '<kml></kml>', 'sector9.jpg', '2025-10-14 08:53:14'),
(10, 'Sector I', 4500.00, 0xe610000001030000000100000005000000cdcccccccccc4340999999999999d9bfcdcccccccccc4340f5285c8fc2f5d8bfae47e17a14ce4340f5285c8fc2f5d8bfae47e17a14ce4340999999999999d9bfcdcccccccccc4340999999999999d9bf, '<kml></kml>', 'sector10.jpg', '2025-10-14 08:53:14'),
(11, 'Sector J', 4600.00, 0xe6100000010300000001000000050000008fc2f5285ccf434052b81e85eb51d8bf8fc2f5285ccf4340ae47e17a14aed7bf703d0ad7a3d04340ae47e17a14aed7bf703d0ad7a3d0434052b81e85eb51d8bf8fc2f5285ccf434052b81e85eb51d8bf, '<kml></kml>', 'sector11.jpg', '2025-10-14 08:53:14'),
(12, 'Sector K', 4700.00, 0xe61000000103000000010000000500000052b81e85ebd143400ad7a3703d0ad7bf52b81e85ebd14340666666666666d6bf3333333333d34340666666666666d6bf3333333333d343400ad7a3703d0ad7bf52b81e85ebd143400ad7a3703d0ad7bf, '<kml></kml>', 'sector12.jpg', '2025-10-14 08:53:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector_parcela`
--

CREATE TABLE `sector_parcela` (
  `id_sector` int NOT NULL,
  `id_parcela` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `sector_parcela`
--

INSERT INTO `sector_parcela` (`id_sector`, `id_parcela`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector_sol`
--

CREATE TABLE `sector_sol` (
  `id_sector` int NOT NULL,
  `id_sol` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `sector_sol`
--

INSERT INTO `sector_sol` (`id_sector`, `id_sol`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector_varietat`
--

CREATE TABLE `sector_varietat` (
  `id_sector` int NOT NULL,
  `id_varietat` int NOT NULL,
  `id_data` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `sector_varietat`
--

INSERT INTO `sector_varietat` (`id_sector`, `id_varietat`, `id_data`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6),
(7, 7, 7),
(8, 8, 8),
(9, 9, 9),
(10, 10, 10),
(11, 11, 11),
(12, 12, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguiment`
--

CREATE TABLE `seguiment` (
  `id_seguiment` int NOT NULL,
  `id_sector` int NOT NULL,
  `data_registre` date NOT NULL,
  `estat_fenologic` varchar(255) DEFAULT NULL,
  `creixement_vegetatiu` text,
  `incidencies_detectades` text,
  `intervencions_realitzades` text,
  `estimacio_actualizada_collita` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `seguiment`
--

INSERT INTO `seguiment` (`id_seguiment`, `id_sector`, `data_registre`, `estat_fenologic`, `creixement_vegetatiu`, `incidencies_detectades`, `intervencions_realitzades`, `estimacio_actualizada_collita`) VALUES
(1, 1, '2025-03-01', 'Floración', 'Brotes vigorosos', 'Mildiu incipiente', 'Tratamiento preventivo', 1800.00),
(2, 2, '2024-06-10', 'Fructificación', 'Normal', 'Ataque de pulgón', 'Aplicación insecticida', 1600.00),
(3, 3, '2025-05-10', 'Floración', 'Normal', 'Sin incidencias', 'Revisión semanal', 1650.00),
(4, 4, '2025-06-10', 'Cuajado', 'Normal', 'Sin incidencias', 'Revisión semanal', 1700.00),
(5, 5, '2025-07-10', 'Fructificación', 'Normal', 'Sin incidencias', 'Revisión semanal', 1750.00),
(6, 6, '2025-08-10', 'Engorde', 'Normal', 'Sin incidencias', 'Revisión semanal', 1800.00),
(7, 7, '2025-09-10', 'Envero', 'Normal', 'Sin incidencias', 'Revisión semanal', 1850.00),
(8, 8, '2025-10-10', 'Maduración', 'Normal', 'Sin incidencias', 'Revisión semanal', 1900.00),
(9, 9, '2025-11-10', 'Postcosecha', 'Normal', 'Sin incidencias', 'Revisión semanal', 1950.00),
(10, 10, '2025-12-10', 'Reposo', 'Normal', 'Sin incidencias', 'Revisión semanal', 2000.00),
(11, 11, '2025-01-10', 'Brotación', 'Normal', 'Sin incidencias', 'Revisión semanal', 2050.00),
(12, 12, '2025-02-10', 'Vigorización', 'Normal', 'Sin incidencias', 'Revisión semanal', 2100.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sol`
--

CREATE TABLE `sol` (
  `id_sol` int NOT NULL,
  `tipus` varchar(100) NOT NULL,
  `ph` decimal(4,2) DEFAULT NULL,
  `materia_organica` decimal(5,2) DEFAULT NULL,
  `observacions` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `sol`
--

INSERT INTO `sol` (`id_sol`, `tipus`, `ph`, `materia_organica`, `observacions`) VALUES
(1, 'Arenoso', 6.50, 2.50, 'Bien drenado'),
(2, 'Arcilloso', 7.20, 3.10, 'Alta retención de agua'),
(3, 'Franco', 6.80, 2.80, 'Equilibrado'),
(4, 'Franco-arenoso', 6.20, 2.30, 'Buen drenaje'),
(5, 'Franco-arcilloso', 7.40, 3.50, 'Fértil, pesado'),
(6, 'Calizo', 8.10, 1.80, 'Alto CaCO3'),
(7, 'Limoso', 6.90, 3.20, 'Retiene humedad'),
(8, 'Turboso', 5.50, 8.00, 'Rico en MO'),
(9, 'Salino', 8.30, 1.20, 'Conductividad alta'),
(10, 'Pedregoso', 7.00, 1.00, 'Buen drenaje, poca retención'),
(11, 'Volcánico', 6.50, 4.50, 'Minerales disponibles'),
(12, 'Aluvial', 7.10, 2.90, 'Fértil y profundo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tractament`
--

CREATE TABLE `tractament` (
  `id_tractament` int NOT NULL,
  `id_fila` int NOT NULL,
  `data` date NOT NULL,
  `tipus` enum('Fitosanitari','Fertilitzant','Altre') DEFAULT NULL,
  `producte` varchar(255) DEFAULT NULL,
  `dosi_ml` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tractament`
--

INSERT INTO `tractament` (`id_tractament`, `id_fila`, `data`, `tipus`, `producte`, `dosi_ml`) VALUES
(1, 1, '2025-04-01', 'Fitosanitari', 'Fungicida X', '200 ml'),
(2, 2, '2025-05-15', 'Fertilitzant', 'NPK 20-20-20', '500 ml'),
(3, 3, '2025-07-15', 'Fitosanitari', 'Fungicida D', '200 ml'),
(4, 4, '2025-08-15', 'Fertilitzant', 'NPK 20-20-20', '500 ml'),
(5, 5, '2025-09-15', 'Altre', 'Lavado de hojas', '—'),
(6, 6, '2025-10-15', 'Fitosanitari', 'Fungicida D', '200 ml'),
(7, 7, '2025-11-15', 'Fertilitzant', 'NPK 20-20-20', '500 ml'),
(8, 8, '2025-12-15', 'Altre', 'Lavado de hojas', '—'),
(9, 9, '2025-01-15', 'Fitosanitari', 'Fungicida D', '200 ml'),
(10, 10, '2025-02-15', 'Fertilitzant', 'NPK 20-20-20', '500 ml'),
(11, 11, '2025-03-15', 'Altre', 'Lavado de hojas', '—'),
(12, 12, '2025-04-15', 'Fitosanitari', 'Fungicida D', '200 ml');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `varietat`
--

CREATE TABLE `varietat` (
  `id_varietat` int NOT NULL,
  `id_especie` int NOT NULL,
  `nom_cientific` varchar(255) NOT NULL,
  `nom_comu` varchar(255) NOT NULL,
  `caracteristiques_agronomiques` text,
  `cicle_vegetatiu` text,
  `requisits_pollinitzacio` text,
  `productivitat_mitjana` decimal(10,2) DEFAULT NULL,
  `qualitats_organoleptiques` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `varietat`
--

INSERT INTO `varietat` (`id_varietat`, `id_especie`, `nom_cientific`, `nom_comu`, `caracteristiques_agronomiques`, `cicle_vegetatiu`, `requisits_pollinitzacio`, `productivitat_mitjana`, `qualitats_organoleptiques`) VALUES
(1, 1, 'Prunus persica var. platycarpa', 'Paraguayo', 'Resistente al frío', 'Caduco', 'Necesita polinizador', 2500.50, 'Dulce y jugoso'),
(2, 2, 'Olea europaea var. arbequina', 'Arbequina', 'Alta productividad', 'Perennifolio', 'Autopolinizante', 1800.00, 'Aceite suave'),
(3, 3, 'Vitis vinifera var. tempranillo', 'Tempranillo', 'Buena calidad en climas mediterráneos', 'Perennifolio', 'Polinización entomófila', 9000.00, 'Aromas a frutos rojos'),
(4, 4, 'Citrus sinensis var. navelina', 'Navelina', 'Alta productividad', 'Perennifolio', 'Autopolinizante', 25000.00, 'Dulce, sin semillas'),
(5, 5, 'Citrus limon var. eureka', 'Eureka', 'Floración casi continua', 'Perennifolio', 'Autopolinizante', 18000.00, 'Aroma intenso'),
(6, 6, 'Pyrus communis var. conference', 'Conference', 'Buen cuajado', 'Caduco', 'Parcialmente autoincompatible', 15000.00, 'Textura mantequillosa'),
(7, 7, 'Malus domestica var. golden', 'Golden', 'Sensible a oídio', 'Caduco', 'Requiere polinizador', 30000.00, 'Dulce, crocante'),
(8, 8, 'Prunus dulcis var. marcona', 'Marcona', 'Muy apreciada por calidad', 'Caduco', 'Autoincompatible', 1200.00, 'Grano dulce'),
(9, 9, 'Prunus armeniaca var. bulida', 'Bulida', 'Precoz', 'Caduco', 'Autoincompatible', 10000.00, 'Sabor equilibrado'),
(10, 10, 'Prunus avium var. burlat', 'Burlat', 'Temprana', 'Caduco', 'Requiere polinizador', 8000.00, 'Dulce, firme'),
(11, 11, 'Ficus carica var. brown turkey', 'Brown Turkey', 'Tolerante a sequía', 'Caduco', 'Partenocárpico parcial', 6000.00, 'Dulce, meloso'),
(12, 12, 'Punica granatum var. mollar', 'Mollar', 'Buena adaptación', 'Caduco', 'Autopolinizante', 9000.00, 'Granos suaves');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aplicacio`
--
ALTER TABLE `aplicacio`
  ADD PRIMARY KEY (`id_aplicacio`),
  ADD KEY `fk_ap_pla` (`id_pla`),
  ADD KEY `fk_ap_oper` (`id_operari`),
  ADD KEY `fk_ap_equip` (`id_equip`);

--
-- Indices de la tabla `aplicacio_fila`
--
ALTER TABLE `aplicacio_fila`
  ADD PRIMARY KEY (`id_aplicacio`,`id_fila`),
  ADD KEY `fk_af_fila` (`id_fila`);

--
-- Indices de la tabla `aplicacio_producte`
--
ALTER TABLE `aplicacio_producte`
  ADD PRIMARY KEY (`id_aplicacio`,`id_producte`),
  ADD KEY `fk_appr_pr` (`id_producte`);

--
-- Indices de la tabla `clima`
--
ALTER TABLE `clima`
  ADD PRIMARY KEY (`id_clima`),
  ADD KEY `id_plantacio` (`id_plantacio`);

--
-- Indices de la tabla `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id_data`);

--
-- Indices de la tabla `equip`
--
ALTER TABLE `equip`
  ADD PRIMARY KEY (`id_equip`);

--
-- Indices de la tabla `especie`
--
ALTER TABLE `especie`
  ADD PRIMARY KEY (`id_especie`);

--
-- Indices de la tabla `estat_fenologic`
--
ALTER TABLE `estat_fenologic`
  ADD PRIMARY KEY (`id_estat`);

--
-- Indices de la tabla `fila`
--
ALTER TABLE `fila`
  ADD PRIMARY KEY (`id_fila`),
  ADD UNIQUE KEY `id_increment` (`id_increment`,`numero_fila`);

--
-- Indices de la tabla `infraestructura`
--
ALTER TABLE `infraestructura`
  ADD PRIMARY KEY (`id_infra`),
  ADD KEY `id_parcela` (`id_parcela`),
  ADD SPATIAL KEY `geometria` (`geometria`);

--
-- Indices de la tabla `infra_parcela`
--
ALTER TABLE `infra_parcela`
  ADD PRIMARY KEY (`id_infra`,`id_parcela`),
  ADD KEY `id_parcela` (`id_parcela`);

--
-- Indices de la tabla `magatzem`
--
ALTER TABLE `magatzem`
  ADD PRIMARY KEY (`id_magatzem`);

--
-- Indices de la tabla `moviment_estoc`
--
ALTER TABLE `moviment_estoc`
  ADD PRIMARY KEY (`id_mov`),
  ADD KEY `fk_me_lot` (`id_lot`),
  ADD KEY `fk_me_ap` (`id_aplicacio`);

--
-- Indices de la tabla `operari`
--
ALTER TABLE `operari`
  ADD PRIMARY KEY (`id_operari`);

--
-- Indices de la tabla `parcela`
--
ALTER TABLE `parcela`
  ADD PRIMARY KEY (`id_parcela`),
  ADD UNIQUE KEY `ref_cadastral` (`ref_cadastral`),
  ADD SPATIAL KEY `geometria` (`geometria`);

--
-- Indices de la tabla `plantacio`
--
ALTER TABLE `plantacio`
  ADD PRIMARY KEY (`id_plantacio`),
  ADD KEY `id_sector` (`id_sector`),
  ADD KEY `id_varietat` (`id_varietat`);

--
-- Indices de la tabla `pla_fila`
--
ALTER TABLE `pla_fila`
  ADD PRIMARY KEY (`id_pla`,`id_fila`),
  ADD KEY `fk_pf_fila` (`id_fila`);

--
-- Indices de la tabla `pla_producte`
--
ALTER TABLE `pla_producte`
  ADD PRIMARY KEY (`id_pla`,`id_producte`),
  ADD KEY `fk_pp_producte` (`id_producte`);

--
-- Indices de la tabla `pla_sector`
--
ALTER TABLE `pla_sector`
  ADD PRIMARY KEY (`id_pla`,`id_sector`),
  ADD KEY `fk_ps_sector` (`id_sector`);

--
-- Indices de la tabla `pla_tractament`
--
ALTER TABLE `pla_tractament`
  ADD PRIMARY KEY (`id_pla`),
  ADD KEY `fk_pla_estat_ini` (`id_estat_inici`),
  ADD KEY `fk_pla_estat_fi` (`id_estat_fi`),
  ADD KEY `fk_pla_varietat` (`id_varietat`);

--
-- Indices de la tabla `producte`
--
ALTER TABLE `producte`
  ADD PRIMARY KEY (`id_producte`);

--
-- Indices de la tabla `producte_lot`
--
ALTER TABLE `producte_lot`
  ADD PRIMARY KEY (`id_lot`),
  ADD UNIQUE KEY `uq_lot` (`id_producte`,`numero_lot`),
  ADD KEY `fk_pl_magatzem` (`id_magatzem`);

--
-- Indices de la tabla `registre`
--
ALTER TABLE `registre`
  ADD PRIMARY KEY (`id_registre`),
  ADD KEY `id_varietat` (`id_varietat`),
  ADD KEY `id_plantacio` (`id_plantacio`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id_sector`),
  ADD SPATIAL KEY `geometria` (`geometria`);

--
-- Indices de la tabla `sector_parcela`
--
ALTER TABLE `sector_parcela`
  ADD PRIMARY KEY (`id_sector`,`id_parcela`),
  ADD KEY `idx_sp_parcela` (`id_parcela`);

--
-- Indices de la tabla `sector_sol`
--
ALTER TABLE `sector_sol`
  ADD PRIMARY KEY (`id_sector`,`id_sol`),
  ADD KEY `id_sol` (`id_sol`);

--
-- Indices de la tabla `sector_varietat`
--
ALTER TABLE `sector_varietat`
  ADD PRIMARY KEY (`id_sector`,`id_varietat`,`id_data`),
  ADD KEY `id_varietat` (`id_varietat`),
  ADD KEY `id_data` (`id_data`);

--
-- Indices de la tabla `seguiment`
--
ALTER TABLE `seguiment`
  ADD PRIMARY KEY (`id_seguiment`),
  ADD KEY `id_sector` (`id_sector`);

--
-- Indices de la tabla `sol`
--
ALTER TABLE `sol`
  ADD PRIMARY KEY (`id_sol`);

--
-- Indices de la tabla `tractament`
--
ALTER TABLE `tractament`
  ADD PRIMARY KEY (`id_tractament`),
  ADD KEY `id_fila` (`id_fila`);

--
-- Indices de la tabla `varietat`
--
ALTER TABLE `varietat`
  ADD PRIMARY KEY (`id_varietat`),
  ADD KEY `id_especie` (`id_especie`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aplicacio`
--
ALTER TABLE `aplicacio`
  MODIFY `id_aplicacio` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `clima`
--
ALTER TABLE `clima`
  MODIFY `id_clima` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `data`
--
ALTER TABLE `data`
  MODIFY `id_data` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `equip`
--
ALTER TABLE `equip`
  MODIFY `id_equip` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `especie`
--
ALTER TABLE `especie`
  MODIFY `id_especie` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `estat_fenologic`
--
ALTER TABLE `estat_fenologic`
  MODIFY `id_estat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `fila`
--
ALTER TABLE `fila`
  MODIFY `id_fila` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `infraestructura`
--
ALTER TABLE `infraestructura`
  MODIFY `id_infra` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `magatzem`
--
ALTER TABLE `magatzem`
  MODIFY `id_magatzem` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `moviment_estoc`
--
ALTER TABLE `moviment_estoc`
  MODIFY `id_mov` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `operari`
--
ALTER TABLE `operari`
  MODIFY `id_operari` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `parcela`
--
ALTER TABLE `parcela`
  MODIFY `id_parcela` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `plantacio`
--
ALTER TABLE `plantacio`
  MODIFY `id_plantacio` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pla_tractament`
--
ALTER TABLE `pla_tractament`
  MODIFY `id_pla` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `producte`
--
ALTER TABLE `producte`
  MODIFY `id_producte` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `producte_lot`
--
ALTER TABLE `producte_lot`
  MODIFY `id_lot` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `registre`
--
ALTER TABLE `registre`
  MODIFY `id_registre` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id_sector` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `seguiment`
--
ALTER TABLE `seguiment`
  MODIFY `id_seguiment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `sol`
--
ALTER TABLE `sol`
  MODIFY `id_sol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tractament`
--
ALTER TABLE `tractament`
  MODIFY `id_tractament` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `varietat`
--
ALTER TABLE `varietat`
  MODIFY `id_varietat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aplicacio`
--
ALTER TABLE `aplicacio`
  ADD CONSTRAINT `fk_ap_equip` FOREIGN KEY (`id_equip`) REFERENCES `equip` (`id_equip`),
  ADD CONSTRAINT `fk_ap_oper` FOREIGN KEY (`id_operari`) REFERENCES `operari` (`id_operari`),
  ADD CONSTRAINT `fk_ap_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`);

--
-- Filtros para la tabla `aplicacio_fila`
--
ALTER TABLE `aplicacio_fila`
  ADD CONSTRAINT `fk_af_ap` FOREIGN KEY (`id_aplicacio`) REFERENCES `aplicacio` (`id_aplicacio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_af_fila` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`);

--
-- Filtros para la tabla `aplicacio_producte`
--
ALTER TABLE `aplicacio_producte`
  ADD CONSTRAINT `fk_appr_ap` FOREIGN KEY (`id_aplicacio`) REFERENCES `aplicacio` (`id_aplicacio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appr_pr` FOREIGN KEY (`id_producte`) REFERENCES `producte` (`id_producte`);

--
-- Filtros para la tabla `clima`
--
ALTER TABLE `clima`
  ADD CONSTRAINT `clima_ibfk_1` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`);

--
-- Filtros para la tabla `fila`
--
ALTER TABLE `fila`
  ADD CONSTRAINT `fila_ibfk_1` FOREIGN KEY (`id_increment`) REFERENCES `plantacio` (`id_plantacio`);

--
-- Filtros para la tabla `infraestructura`
--
ALTER TABLE `infraestructura`
  ADD CONSTRAINT `infraestructura_ibfk_1` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`);

--
-- Filtros para la tabla `infra_parcela`
--
ALTER TABLE `infra_parcela`
  ADD CONSTRAINT `infra_parcela_ibfk_1` FOREIGN KEY (`id_infra`) REFERENCES `infraestructura` (`id_infra`),
  ADD CONSTRAINT `infra_parcela_ibfk_2` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`);

--
-- Filtros para la tabla `moviment_estoc`
--
ALTER TABLE `moviment_estoc`
  ADD CONSTRAINT `fk_me_ap` FOREIGN KEY (`id_aplicacio`) REFERENCES `aplicacio` (`id_aplicacio`),
  ADD CONSTRAINT `fk_me_lot` FOREIGN KEY (`id_lot`) REFERENCES `producte_lot` (`id_lot`);

--
-- Filtros para la tabla `plantacio`
--
ALTER TABLE `plantacio`
  ADD CONSTRAINT `plantacio_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  ADD CONSTRAINT `plantacio_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`);

--
-- Filtros para la tabla `pla_fila`
--
ALTER TABLE `pla_fila`
  ADD CONSTRAINT `fk_pf_fila` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`),
  ADD CONSTRAINT `fk_pf_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pla_producte`
--
ALTER TABLE `pla_producte`
  ADD CONSTRAINT `fk_pp_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_producte` FOREIGN KEY (`id_producte`) REFERENCES `producte` (`id_producte`);

--
-- Filtros para la tabla `pla_sector`
--
ALTER TABLE `pla_sector`
  ADD CONSTRAINT `fk_ps_pla` FOREIGN KEY (`id_pla`) REFERENCES `pla_tractament` (`id_pla`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`);

--
-- Filtros para la tabla `pla_tractament`
--
ALTER TABLE `pla_tractament`
  ADD CONSTRAINT `fk_pla_estat_fi` FOREIGN KEY (`id_estat_fi`) REFERENCES `estat_fenologic` (`id_estat`),
  ADD CONSTRAINT `fk_pla_estat_ini` FOREIGN KEY (`id_estat_inici`) REFERENCES `estat_fenologic` (`id_estat`),
  ADD CONSTRAINT `fk_pla_varietat` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`);

--
-- Filtros para la tabla `producte_lot`
--
ALTER TABLE `producte_lot`
  ADD CONSTRAINT `fk_pl_magatzem` FOREIGN KEY (`id_magatzem`) REFERENCES `magatzem` (`id_magatzem`),
  ADD CONSTRAINT `fk_pl_producte` FOREIGN KEY (`id_producte`) REFERENCES `producte` (`id_producte`);

--
-- Filtros para la tabla `registre`
--
ALTER TABLE `registre`
  ADD CONSTRAINT `registre_ibfk_1` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
  ADD CONSTRAINT `registre_ibfk_2` FOREIGN KEY (`id_plantacio`) REFERENCES `plantacio` (`id_plantacio`);

--
-- Filtros para la tabla `sector_parcela`
--
ALTER TABLE `sector_parcela`
  ADD CONSTRAINT `sp_fk_parcela` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
  ADD CONSTRAINT `sp_fk_sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`);

--
-- Filtros para la tabla `sector_sol`
--
ALTER TABLE `sector_sol`
  ADD CONSTRAINT `sector_sol_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  ADD CONSTRAINT `sector_sol_ibfk_2` FOREIGN KEY (`id_sol`) REFERENCES `sol` (`id_sol`);

--
-- Filtros para la tabla `sector_varietat`
--
ALTER TABLE `sector_varietat`
  ADD CONSTRAINT `sector_varietat_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
  ADD CONSTRAINT `sector_varietat_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
  ADD CONSTRAINT `sector_varietat_ibfk_3` FOREIGN KEY (`id_data`) REFERENCES `data` (`id_data`);

--
-- Filtros para la tabla `seguiment`
--
ALTER TABLE `seguiment`
  ADD CONSTRAINT `seguiment_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`);

--
-- Filtros para la tabla `tractament`
--
ALTER TABLE `tractament`
  ADD CONSTRAINT `tractament_ibfk_1` FOREIGN KEY (`id_fila`) REFERENCES `fila` (`id_fila`);

--
-- Filtros para la tabla `varietat`
--
ALTER TABLE `varietat`
  ADD CONSTRAINT `varietat_ibfk_1` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
