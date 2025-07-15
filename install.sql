-- --------------------------------------------------------
-- Abadia Sales & Affiliation App - Full Installation SQL
-- --------------------------------------------------------
-- This file contains the complete schema for the application.
-- Execute this script on an empty database to install all tables.
-- Order of execution is critical due to foreign key constraints.
-- --------------------------------------------------------

--
-- Part 1: Ion Auth Schema (Users and Groups)
--
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `users_groups`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'Gerente General', 'General Manager - Top Level'),
(3, 'Gerente de Zona', 'Zone Manager'),
(4, 'Supervisor', 'Team Supervisor'),
(5, 'Agente', 'Sales Agent'),
(6, 'Cliente', 'Standard Client/Member');

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_email` (`email`),
  UNIQUE KEY `uc_username` (`username`),
  UNIQUE KEY `uc_activation_selector` (`activation_selector`),
  UNIQUE KEY `uc_forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `uc_remember_selector` (`remember_selector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `active`, `first_name`, `last_name`) VALUES
(1,'127.0.0.1','administrator','$2y$10$J.MenaBAIFV13XMVLCJq2.83rLz2zKMGg26sgfY9Z5oLBMy6EvL1O','admin@admin.com', 1, 'Admin','istrator');

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`, `group_id`),
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES (1,1,1), (2,1,2);

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Part 2: Agentes Schema
--
DROP TABLE IF EXISTS `agentes`;
DROP TABLE IF EXISTS `parroquias`;
DROP TABLE IF EXISTS `municipios`;
DROP TABLE IF EXISTS `ciudades`;
DROP TABLE IF EXISTS `estados`;
DROP TABLE IF EXISTS `cargos`;

CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL AUTO_INCREMENT, `cargo` varchar(100) NOT NULL, PRIMARY KEY (`id_cargo`), UNIQUE KEY `uq_cargo_nombre` (`cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cargos` (`id_cargo`, `cargo`) VALUES (1, 'Agente de Ventas'), (2, 'Supervisor'), (3, 'Gerente de Zona');

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL AUTO_INCREMENT, `estado` varchar(100) NOT NULL, PRIMARY KEY (`id_estado`), UNIQUE KEY `uq_estado_nombre` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `estados` (`id_estado`, `estado`) VALUES (1, 'Amazonas'),(2, 'Anzoátegui'),(3, 'Apure'),(4, 'Aragua'),(5, 'Barinas'),(6, 'Bolívar'),(7, 'Carabobo'),(8, 'Cojedes'),(9, 'Delta Amacuro'),(10, 'Falcón'),(11, 'Guárico'),(12, 'Lara'),(13, 'Mérida'),(14, 'Miranda'),(15, 'Monagas'),(16, 'Nueva Esparta'),(17, 'Portuguesa'),(18, 'Sucre'),(19, 'Táchira'),(20, 'Trujillo'),(21, 'La Guaira'),(22, 'Yaracuy'),(23, 'Zulia'),(24, 'Distrito Capital'),(25, 'Dependencias Federales');

CREATE TABLE `ciudades` (
  `id_ciudad` int(11) NOT NULL AUTO_INCREMENT, `id_estado` int(11) NOT NULL, `ciudad` varchar(100) NOT NULL, PRIMARY KEY (`id_ciudad`), UNIQUE KEY `uq_ciudad_estado` (`id_estado`, `ciudad`), CONSTRAINT `fk_ciudad_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ciudades` (`id_estado`, `ciudad`) VALUES (24, 'Caracas'),(14, 'Los Teques'),(14, 'Guarenas'),(14, 'Guatire'),(14, 'Charallave');

CREATE TABLE `municipios` (
  `id_municipio` int(11) NOT NULL AUTO_INCREMENT, `id_estado` int(11) NOT NULL, `municipio` varchar(100) NOT NULL, PRIMARY KEY (`id_municipio`), UNIQUE KEY `uq_municipio_estado` (`id_estado`, `municipio`), CONSTRAINT `fk_municipio_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `municipios` (`id_estado`, `municipio`) VALUES (24, 'Libertador'),(14, 'Guaicaipuro'),(14, 'Plaza'),(14, 'Zamora');

CREATE TABLE `parroquias` (
  `id_parroquia` int(11) NOT NULL AUTO_INCREMENT, `id_municipio` int(11) NOT NULL, `parroquia` varchar(100) NOT NULL, PRIMARY KEY (`id_parroquia`), UNIQUE KEY `uq_parroquia_municipio` (`id_municipio`, `parroquia`), CONSTRAINT `fk_parroquia_municipio` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `agentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT, `nombres` varchar(100) NOT NULL, `apellidos` varchar(100) NOT NULL, `cedula` varchar(20) NOT NULL, `rif` varchar(20) NOT NULL, `sexo` enum('Masculino','Femenino') NOT NULL, `telefono_celular` varchar(20) NOT NULL, `telefono_local` varchar(20) DEFAULT NULL, `correo_electronico` varchar(100) NOT NULL, `direccion_habitacion` text NOT NULL, `id_estado` int(11) NOT NULL, `id_ciudad` int(11) NOT NULL, `id_municipio` int(11) NOT NULL, `id_parroquia` int(11) NOT NULL, `fecha_ingreso` date NOT NULL, `id_cargo` int(11) NOT NULL, `foto_perfil` varchar(255) DEFAULT NULL, `user_id` int(11) unsigned DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `deleted_at` TIMESTAMP NULL DEFAULT NULL, `deleted_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_cedula` (`cedula`), UNIQUE KEY `uq_rif` (`rif`), UNIQUE KEY `uq_correo_electronico` (`correo_electronico`),
  CONSTRAINT `fk_agente_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_municipio` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_parroquia` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquias` (`id_parroquia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_ion_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Part 3: Afiliaciones Schema
--
DROP TABLE IF EXISTS `afiliacion_familiares`;
DROP TABLE IF EXISTS `afiliaciones`;
DROP TABLE IF EXISTS `personas`;

CREATE TABLE `personas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `nombres` varchar(100) NOT NULL, `apellidos` varchar(100) NOT NULL, `cedula` varchar(20) NOT NULL, `birthdate` date NOT NULL, `foto_perfil` varchar(255) DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uq_cedula_personas` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `afiliaciones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `contract_number` varchar(50) NOT NULL, `fecha` date NOT NULL, `asesor_id` int(11) NOT NULL, `titular_id` int(11) unsigned NOT NULL, `plan_type` varchar(50) NOT NULL, `plan_amount` decimal(10,2) NOT NULL, `cuotas` int(11) NOT NULL, `payment_type` varchar(50) NOT NULL, `bank_name` varchar(100) DEFAULT NULL, `account_number` varchar(50) DEFAULT NULL, `account_type` varchar(50) DEFAULT NULL, `observaciones` text DEFAULT NULL, `created_by` int(11) unsigned NOT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_contract_number` (`contract_number`),
  CONSTRAINT `fk_afiliacion_asesor` FOREIGN KEY (`asesor_id`) REFERENCES `agentes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_afiliacion_titular` FOREIGN KEY (`titular_id`) REFERENCES `personas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_afiliacion_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `afiliacion_familiares` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `afiliacion_id` int(11) unsigned NOT NULL, `persona_id` int(11) unsigned NOT NULL, `parentesco` varchar(50) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `uq_afiliacion_persona` (`afiliacion_id`, `persona_id`),
  CONSTRAINT `fk_familiar_afiliacion` FOREIGN KEY (`afiliacion_id`) REFERENCES `afiliaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_familiar_persona` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
