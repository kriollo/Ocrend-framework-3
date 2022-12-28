-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.3.15-MariaDB - mariadb.org binary distribution
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
DROP DATABASE IF EXISTS `newproyect`;
CREATE DATABASE IF NOT EXISTS `newproyect` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `newproyect`;

-- Volcando estructura para función newproyect.digitoverificador
DROP FUNCTION IF EXISTS `digitoverificador`;
DELIMITER //
CREATE FUNCTION `digitoverificador`(`rut` INT UNSIGNED) RETURNS varchar(1) CHARSET utf8 COLLATE utf8_unicode_ci
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
DROP FUNCTION IF EXISTS `mes_palabras`;
DELIMITER //
CREATE FUNCTION `mes_palabras`(fecha DATETIME) RETURNS varchar(20) CHARSET utf8 COLLATE utf8_unicode_ci
BEGIN

	DECLARE mes VARCHAR(20);
	SET lc_time_names = 'es_ES';
	SET mes = MONTHNAME(fecha);

RETURN mes;

END//
DELIMITER ;

-- Volcando estructura para tabla newproyect.tbladm_menu
DROP TABLE IF EXISTS `tbladm_menu`;
CREATE TABLE IF NOT EXISTS `tbladm_menu` (
  `id_menu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posi` int(10) unsigned NOT NULL,
  `seccion` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `descripcion` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `glyphicon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id_menu`,`posi`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladm_menu: 1 rows
DELETE FROM `tbladm_menu`;
/*!40000 ALTER TABLE `tbladm_menu` DISABLE KEYS */;
INSERT INTO `tbladm_menu` (`id_menu`, `posi`, `seccion`, `descripcion`, `glyphicon`, `estado`) VALUES
	(99, 9, 'ADMINISTRACION', 'USUARIOS', 'fas fa-users', 1);
/*!40000 ALTER TABLE `tbladm_menu` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.tbladm_perfiles
DROP TABLE IF EXISTS `tbladm_perfiles`;
CREATE TABLE IF NOT EXISTS `tbladm_perfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `id_menu` int(10) NOT NULL DEFAULT 0,
  `id_submenu` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladm_perfiles: 0 rows
DELETE FROM `tbladm_perfiles`;
/*!40000 ALTER TABLE `tbladm_perfiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbladm_perfiles` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.tbladm_perfilesuser
DROP TABLE IF EXISTS `tbladm_perfilesuser`;
CREATE TABLE IF NOT EXISTS `tbladm_perfilesuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_menu` int(10) unsigned NOT NULL,
  `id_submenu` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_user_id_menu_id_submenu` (`id_user`,`id_menu`,`id_submenu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladm_perfilesuser: 0 rows
DELETE FROM `tbladm_perfilesuser`;
/*!40000 ALTER TABLE `tbladm_perfilesuser` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbladm_perfilesuser` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.tbladm_submenu
DROP TABLE IF EXISTS `tbladm_submenu`;
CREATE TABLE IF NOT EXISTS `tbladm_submenu` (
  `id_menu` int(10) unsigned NOT NULL,
  `id_submenu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PosS` int(10) unsigned NOT NULL,
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `estado` smallint(5) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_menu`,`id_submenu`,`PosS`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla newproyect.tbladm_submenu: 7 rows
DELETE FROM `tbladm_submenu`;
/*!40000 ALTER TABLE `tbladm_submenu` DISABLE KEYS */;
INSERT INTO `tbladm_submenu` (`id_menu`, `id_submenu`, `PosS`, `url`, `descripcion`, `estado`) VALUES
	(99, 1, 1, 'users', 'Principal', 1),
	(99, 2, 2, 'users/usuarios', 'Usuarios', 1),
	(99, 3, 3, 'users/perfiles', 'Gestion de Pefiles', 1);
/*!40000 ALTER TABLE `tbladm_submenu` ENABLE KEYS */;

-- Volcando estructura para tabla newproyect.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `tmp_pass` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `perfil` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `rol` smallint(1) NOT NULL DEFAULT 0,
  `estado` smallint(1) NOT NULL DEFAULT 1,
  `foto` smallint(1) NOT NULL DEFAULT 0,
  `name_foto` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pagina_inicio` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `online_fecha` int(20) NOT NULL DEFAULT 0,
  `fecha_pass` date NOT NULL,
  `tipo_user` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sistema',
  PRIMARY KEY (`id_user`) USING BTREE,
  KEY `tipo_user_rut_cliente` (`tipo_user`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla newproyect.users: 1 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id_user`, `name`, `email`, `pass`, `tmp_pass`, `token`, `perfil`, `rol`, `estado`, `foto`, `name_foto`, `pagina_inicio`, `online_fecha`, `fecha_pass`, `tipo_user`) VALUES
	(1, 'ADMINISTRADOR', 'admin@wys.cl', '$2a$10$1b15acc9218708fe45010OXHRhxB61vvFnLu/g0dG5jBC7ywONy6u', '', '', 'DEFINIDO', 1, 1, 1, '1.jpg', 'portal', 0, '2023-02-25', 'sistema');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
