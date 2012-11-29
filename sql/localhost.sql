-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 29-11-2012 a las 16:24:32
-- Versión del servidor: 5.5.24
-- Versión de PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `gestcines3`
--
CREATE DATABASE `gestcines3` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `gestcines3`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `usuario` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `pass` varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(1) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'A',
  `puntos` int(11) NOT NULL DEFAULT '0',
  `regentrada` int(11) NOT NULL DEFAULT '0',
  `regpalomitas` int(11) NOT NULL DEFAULT '0',
  `premios` int(11) NOT NULL DEFAULT '0',
  `dni` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `anopremio` int(4) NOT NULL DEFAULT '0',
  `salas` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario`, `pass`, `estado`, `puntos`, `regentrada`, `regpalomitas`, `premios`, `dni`, `fecha`, `anopremio`, `salas`) VALUES
('lito', '827ccb0eea8a706c4c34a16891f84e7b', 'A', 10, 0, 0, 0, '23234345l', '1982-11-25', 0, 5),
('lorena', '827ccb0eea8a706c4c34a16891f84e7b', 'A', 80, 0, 0, 15, '212314234', '1992-11-11', 2012, 5),
('manuel', '827ccb0eea8a706c4c34a16891f84e7b', 'A', 30, 0, 0, 100, '20678987m', '1967-11-01', 2012, 5),
('pepe', '827ccb0eea8a706c4c34a16891f84e7b', 'A', 0, 1, 1, 14, '12234345m', '1967-11-02', 2012, 5);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
