-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.31 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.0.0.6468
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para newproyect
CREATE DATABASE IF NOT EXISTS `newproyect` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `newproyect`;

-- Volcando estructura para función newproyect.digitoverificador
DELIMITER //
CREATE FUNCTION `digitoverificador`(`rut` INT UNSIGNED) RETURNS varchar(1) CHARSET utf8mb3 COLLATE utf8mb3_unicode_ci
BEGIN

DECLARE dv VARCHAR(1);
DECLARE Digito INT;
DECLARE Contador INT;
DECLARE Multiplo INT;
DECLARE Acumulador INT;

SET Contador = 2;
SET Acumulador = 0;
SET Multiplo = 0;

WHILE(rut!=0) DO
SET Multiplo = (rut%10) * Contador;
SET Acumulador = Acumulador + Multiplo;
SET rut = FLOOR(rut / 10);
SET Contador = Contador + 1;
if(Contador = 8) THEN
SET Contador = 2;
END IF;
END WHILE;

SET Digito = 11 - (Acumulador%11);

SET dv = LTRIM(RTRIM(CAST(Digito as CHAR(2))));

IF(Digito = 10) THEN
SET dv = 'K';
END IF;

IF(Digito = 11) THEN
SET dv = '0';
END IF;

RETURN dv;

END//
DELIMITER ;

-- Volcando estructura para función newproyect.mes_palabras
DELIMITER //
CREATE FUNCTION `mes_palabras`(fecha DATETIME) RETURNS varchar(20) CHARSET utf8mb3 COLLATE utf8mb3_unicode_ci
BEGIN

	DECLARE mes VARCHAR(20);
	SET lc_time_names = 'es_ES';
	SET mes = MONTHNAME(fecha);

RETURN mes;

END//
DELIMITER ;

-- Volcando estructura para tabla newproyect.tbladmmenu
CREATE TABLE IF NOT EXISTS `tbladmmenu` (
  `id_menu` int unsigned NOT NULL AUTO_INCREMENT,
  `posi` int unsigned NOT NULL,
  `seccion` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `descripcion` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `glyphicon` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` tinyint DEFAULT '1',
  PRIMARY KEY (`id_menu`,`posi`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladmmenu: 2 rows
DELETE FROM `tbladmmenu`;
/*!40000 ALTER TABLE `tbladmmenu` DISABLE KEYS */;
INSERT INTO `tbladmmenu` (`id_menu`, `posi`, `seccion`, `descripcion`, `glyphicon`, `estado`) VALUES
	(99, 9, 'ADMINISTRACION', 'USUARIOS', 'fas fa-users', 1),
	(1, 1, 'PRUEBA', 'PRUEBA', 'fas fa-save', 1);
/*!40000 ALTER TABLE `tbladmmenu` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.tbladmperfiles
CREATE TABLE IF NOT EXISTS `tbladmperfiles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(35) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_menu` int NOT NULL DEFAULT '0',
  `id_submenu` int NOT NULL DEFAULT '0',
  `url` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladmperfiles: 4 rows
DELETE FROM `tbladmperfiles`;
/*!40000 ALTER TABLE `tbladmperfiles` DISABLE KEYS */;
INSERT INTO `tbladmperfiles` (`id`, `nombre`, `id_menu`, `id_submenu`, `url`) VALUES
	(93, 'PRO', 99, 1, 'users/perfiles'),
	(97, 'MEDIO', 99, 2, 'users/perfiles'),
	(96, 'MEDIO', 1, 1, 'users/perfiles'),
	(57, 'BASICO', 1, 1, 'portal'),
	(92, 'PRO', 1, 1, 'users/perfiles'),
	(94, 'PRO', 99, 2, 'users/perfiles'),
	(95, 'PRO', 99, 3, 'users/perfiles');
/*!40000 ALTER TABLE `tbladmperfiles` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.tbladmperfilesuser
CREATE TABLE IF NOT EXISTS `tbladmperfilesuser` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL DEFAULT '0',
  `id_menu` int unsigned NOT NULL,
  `id_submenu` int unsigned NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_user_id_menu_id_submenu` (`id_user`,`id_menu`,`id_submenu`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladmperfilesuser: 7 rows
DELETE FROM `tbladmperfilesuser`;
/*!40000 ALTER TABLE `tbladmperfilesuser` DISABLE KEYS */;
INSERT INTO `tbladmperfilesuser` (`id`, `id_user`, `id_menu`, `id_submenu`) VALUES
	(64, 4, 1, 1),
	(52, 3, 99, 2),
	(63, 6, 1, 1),
	(62, 4, 99, 2),
	(61, 6, 99, 2),
	(50, 3, 1, 1),
	(51, 3, 99, 1),
	(54, 5, 1, 1),
	(53, 3, 99, 3);
/*!40000 ALTER TABLE `tbladmperfilesuser` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.tbladmsubmenu
CREATE TABLE IF NOT EXISTS `tbladmsubmenu` (
  `id_menu` int unsigned NOT NULL,
  `id_submenu` int unsigned NOT NULL AUTO_INCREMENT,
  `PosS` int unsigned NOT NULL,
  `url` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` smallint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_menu`,`id_submenu`,`PosS`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladmsubmenu: 4 rows
DELETE FROM `tbladmsubmenu`;
/*!40000 ALTER TABLE `tbladmsubmenu` DISABLE KEYS */;
INSERT INTO `tbladmsubmenu` (`id_menu`, `id_submenu`, `PosS`, `url`, `descripcion`, `estado`) VALUES
	(99, 1, 1, 'users', 'Principal', 1),
	(99, 2, 2, 'users/usuarios', 'Usuarios', 1),
	(99, 3, 3, 'users/perfiles', 'Gestion de Pefiles', 1),
	(1, 1, 1, 'prueba', 'Principal', 1);
/*!40000 ALTER TABLE `tbladmsubmenu` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pass` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tmp_pass` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `token` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `perfil` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `rol` tinyint(1) NOT NULL DEFAULT '0',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `foto` tinyint(1) NOT NULL DEFAULT '0',
  `name_foto` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pagina_inicio` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `online_fecha` int NOT NULL DEFAULT '0',
  `fecha_pass` date NOT NULL,
  `tipo_user` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'sistema',
  PRIMARY KEY (`id`),
  KEY `tipo_user_rut_cliente` (`tipo_user`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla newproyect.users: 5 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `id_user`, `name`, `email`, `pass`, `tmp_pass`, `token`, `perfil`, `rol`, `estado`, `foto`, `name_foto`, `pagina_inicio`, `online_fecha`, `fecha_pass`, `tipo_user`) VALUES
	(1, 1, 'ADMINISTRADOR', 'admin@wys.cl', '$2a$10$bfd53db74cdae1cdd0471OSBOVacafd9PtPSKcKZp/nwaJooTLLP.', '', '', 'DEFINIDO', 1, 1, 1, '1.jpg', 'portal', 1675189018, '2023-03-31', 'sistema'),
	(3, 3, 'Jorge Jara', 'newproyect@wys.cl', '$2a$10$8616adde6472ed58e5725eghAVuVG7iTNZBsfCAkuqrn.Khs..2aS', '', '', 'DEFINIDO', 1, 1, 0, NULL, 'portal', 0, '2023-03-31', 'sistema'),
	(4, 4, 'prueba1', 'prueba@wys.cl', '$2a$10$1e450f04bb98b61a0a741uyjQ.wqxa5k1apZDWg.pH96EZVll4fuO', '', '', 'MEDIO', 1, 1, 0, NULL, 'users/perfiles', 0, '2023-01-30', 'sistema'),
	(5, 5, 'prueba2', 'prueba2@wys.cl', '$2a$10$2b310b0c836faa240e2d2uT7VSUK9Ezz/OPEFZayBv6V7S7ceIVRi', '', '', 'BASICO', 0, 1, 0, NULL, 'portal', 0, '2023-03-31', 'sistema'),
	(6, 6, 'prueba3', 'prueba3@wys.cl', '$2a$10$f8a2292b5b5e82b1b7c8du1hSCrLOqahbEJRlxVZaBMr55ZTk2hEW', NULL, NULL, 'MEDIO', 0, 1, 0, NULL, 'users/usuarios', 0, '2023-01-27', 'sistema');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
