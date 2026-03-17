-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 15-03-2026 a las 12:58:40
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_inventario`
--

DROP TABLE IF EXISTS `categorias_inventario`;
CREATE TABLE IF NOT EXISTS `categorias_inventario` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(50) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id_categoria`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `choferes`
--

DROP TABLE IF EXISTS `choferes`;
CREATE TABLE IF NOT EXISTS `choferes` (
  `ID_chofer` int NOT NULL AUTO_INCREMENT,
  `RIF_cedula` varchar(30) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_chofer`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `choferes`
--

INSERT INTO `choferes` (`ID_chofer`, `RIF_cedula`, `nombre`, `telefono`, `fecha_registro`) VALUES
(7, 'V31011420', 'Agnae Páez', '04249103505', '2026-03-15 12:49:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `ID_cliente` int NOT NULL AUTO_INCREMENT,
  `RIF_cedula` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  PRIMARY KEY (`ID_cliente`),
  UNIQUE KEY `RIF_cedula` (`RIF_cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`ID_cliente`, `RIF_cedula`, `nombre`, `telefono`, `fecha_registro`) VALUES
(12, 'V31011420', 'Agnae Paez', '04249103505', '2026-03-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fletes`
--

DROP TABLE IF EXISTS `fletes`;
CREATE TABLE IF NOT EXISTS `fletes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_chofer` int NOT NULL,
  `id_vehiculo` int NOT NULL,
  `origen` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `valor` int NOT NULL,
  `cancelado` tinyint NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `fletes`
--

INSERT INTO `fletes` (`id`, `id_cliente`, `id_chofer`, `id_vehiculo`, `origen`, `destino`, `estado`, `valor`, `cancelado`, `fecha`) VALUES
(10, 12, 7, 5, 'Charallave (Cristóbal Rojas)', 'Libertador (Ccs)', 'En Ruta', 1000, 0, '2026-03-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `cantidad` int NOT NULL DEFAULT '0',
  `precio_unidad` decimal(10,2) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cantidad_actual` int DEFAULT '0',
  `stock_minimo` int DEFAULT '5',
  PRIMARY KEY (`id_producto`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_producto`, `codigo`, `nombre`, `descripcion`, `cantidad`, `precio_unidad`, `fecha_actualizacion`, `cantidad_actual`, `stock_minimo`) VALUES
(6, '001', 'Camion', 'carga', 72, 10.00, '2026-03-15 12:53:37', 0, 5),
(7, '001a', 'camion', 'qwerty', 34, 235.00, '2026-03-12 20:35:43', 0, 5),
(8, '002', 'camion2', 'camion de carga', 4, 1000.00, '2026-03-13 16:28:10', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Nombre` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Apellido` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Contraseña` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token_recuperacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `fecha_token` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `mail` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Email`, `Nombre`, `Apellido`, `Contraseña`, `token_recuperacion`, `token_expiracion`, `fecha_token`) VALUES
(7, 'neybri@gmail.com', NULL, NULL, '$2y$10$UUkSwxvP.J4u7u/EGKsVp.A', NULL, NULL, NULL),
(8, 'Luisgalindez@gmail.com', NULL, NULL, '$2y$10$obPxscL8gNYl1h7YdI50uOD', NULL, NULL, NULL),
(14, 'neybriramos@gmail.com', NULL, NULL, '$2y$10$zbPgtnoWf.M6c6icmH8OoeqxHjlA0IE69p20GXh1UA1r/4.uc25ES', '234976', '2026-03-01 00:02:11', NULL),
(15, 'paezagnae@gmail.net', 'Agnae ', 'Paez', '$2y$10$0uyqupSn068tvSmXHtIci.U0yP4gA8pbzt0bwYcR/9aIyXjnQvwwu', NULL, NULL, NULL),
(16, 'a-j-v-r@hotmail.com', 'Ana', 'Velásquez', '$2y$10$OD.2Yyg3eg4u72h2q5jcUuQ0poTb3SjJ3BstXeXysPbTZGsmRc2a2', NULL, NULL, NULL),
(17, 'paezagnae@gmail.com', 'Agnae', 'Paez', '$2y$10$3GcG7bV2URf4YkHF9ZgLNeKnW8IHUkdT4l72r9swfp0gaviFQH0uS', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id_vehiculo` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `cliente_id` int NOT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `placa` (`placa`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `placa`, `marca`, `modelo`, `fecha_registro`, `cliente_id`) VALUES
(5, 'AGX05T', 'Chevrolet', 'NPR', '2026-03-15', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
