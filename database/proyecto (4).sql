-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 26, 2026 at 04:51 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proyecto`
--

-- --------------------------------------------------------

--
-- Table structure for table `choferes`
--

DROP TABLE IF EXISTS `choferes`;
CREATE TABLE IF NOT EXISTS `choferes` (
  `ID_chofer` int NOT NULL AUTO_INCREMENT,
  `RIF_cedula` varchar(30) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  PRIMARY KEY (`ID_chofer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `ID_cliente` int NOT NULL AUTO_INCREMENT,
  `RIF_cedula` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ID_cliente`),
  UNIQUE KEY `RIF_cedula` (`RIF_cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fletes`
--

DROP TABLE IF EXISTS `fletes`;
CREATE TABLE IF NOT EXISTS `fletes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `origen` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `valor` int NOT NULL,
  `cancelado` tinyint NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fletes`
--

INSERT INTO `fletes` (`id`, `id_cliente`, `origen`, `destino`, `estado`, `valor`, `cancelado`, `fecha`) VALUES
(1, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 1, '0000-00-00'),
(2, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(3, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(4, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(5, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00'),
(6, 0, 'Los Teques', 'Caracas ', 'Completado', 0, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `codigo` int NOT NULL,
  `nombre` int NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `precio_unidad` decimal(10,2) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Contraseña` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `mail` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Email`, `Contraseña`) VALUES
(6, 'paezagnae@gmail.com', '$2y$10$0AqhLhWYEN2X3YR6fvErw.7'),
(7, 'neybri@gmail.com', '$2y$10$UUkSwxvP.J4u7u/EGKsVp.A'),
(8, 'Luisgalindez@gmail.com', '$2y$10$obPxscL8gNYl1h7YdI50uOD');

-- --------------------------------------------------------

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id_vehiculo` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marca` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_id` int NOT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `placa` (`placa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
