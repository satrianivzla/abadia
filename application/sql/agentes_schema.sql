-- Drop tables in reverse order of creation due to foreign keys
DROP TABLE IF EXISTS `agentes`;
DROP TABLE IF EXISTS `parroquias`;
DROP TABLE IF EXISTS `municipios`;
DROP TABLE IF EXISTS `ciudades`;
DROP TABLE IF EXISTS `estados`;
DROP TABLE IF EXISTS `cargos`;

-- Table structure for table `cargos`
CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL AUTO_INCREMENT,
  `cargo` varchar(100) NOT NULL,
  PRIMARY KEY (`id_cargo`),
  UNIQUE KEY `uq_cargo_nombre` (`cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `cargos`
INSERT INTO `cargos` (`id_cargo`, `cargo`) VALUES
(1, 'Agente de Ventas'),
(2, 'Supervisor'),
(3, 'Gerente de Zona');

-- Table structure for table `estados`
CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(100) NOT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `uq_estado_nombre` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `estados` (from provided HTML)
INSERT INTO `estados` (`id_estado`, `estado`) VALUES
(1, 'Amazonas'),
(2, 'Anzoátegui'),
(3, 'Apure'),
(4, 'Aragua'),
(5, 'Barinas'),
(6, 'Bolívar'),
(7, 'Carabobo'),
(8, 'Cojedes'),
(9, 'Delta Amacuro'),
(10, 'Falcón'),
(11, 'Guárico'),
(12, 'Lara'),
(13, 'Mérida'),
(14, 'Miranda'),
(15, 'Monagas'),
(16, 'Nueva Esparta'),
(17, 'Portuguesa'),
(18, 'Sucre'),
(19, 'Táchira'),
(20, 'Trujillo'),
(21, 'La Guaira'), -- Formerly Vargas
(22, 'Yaracuy'),
(23, 'Zulia'),
(24, 'Distrito Capital'),
(25, 'Dependencias Federales');

-- Table structure for table `ciudades`
CREATE TABLE `ciudades` (
  `id_ciudad` int(11) NOT NULL AUTO_INCREMENT,
  `id_estado` int(11) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ciudad`),
  UNIQUE KEY `uq_ciudad_estado` (`id_estado`, `ciudad`),
  KEY `fk_ciudad_estado` (`id_estado`),
  CONSTRAINT `fk_ciudad_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `ciudades` (Sample - you'll need to populate this extensively)
-- Example for Distrito Capital (24)
INSERT INTO `ciudades` (`id_estado`, `ciudad`) VALUES
(24, 'Caracas');

-- Example for Miranda (14)
INSERT INTO `ciudades` (`id_estado`, `ciudad`) VALUES
(14, 'Los Teques'),
(14, 'Guarenas'),
(14, 'Guatire'),
(14, 'Charallave');

-- Table structure for table `municipios`
CREATE TABLE `municipios` (
  `id_municipio` int(11) NOT NULL AUTO_INCREMENT,
  `id_estado` int(11) NOT NULL, -- FK to estado, as per user's JS logic for initial population
  `municipio` varchar(100) NOT NULL,
  PRIMARY KEY (`id_municipio`),
  UNIQUE KEY `uq_municipio_estado` (`id_estado`, `municipio`),
  KEY `fk_municipio_estado` (`id_estado`),
  CONSTRAINT `fk_municipio_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `municipios` (Sample - you'll need to populate this extensively)
-- Example for Distrito Capital (24), Municipio Libertador
INSERT INTO `municipios` (`id_estado`, `municipio`) VALUES
(24, 'Libertador');

-- Example for Miranda (14)
INSERT INTO `municipios` (`id_estado`, `municipio`) VALUES
(14, 'Guaicaipuro'), -- Los Teques
(14, 'Plaza'),       -- Guarenas
(14, 'Zamora');      -- Guatire

-- Table structure for table `parroquias`
CREATE TABLE `parroquias` (
  `id_parroquia` int(11) NOT NULL AUTO_INCREMENT,
  `id_municipio` int(11) NOT NULL,
  `parroquia` varchar(100) NOT NULL,
  PRIMARY KEY (`id_parroquia`),
  UNIQUE KEY `uq_parroquia_municipio` (`id_municipio`, `parroquia`),
  KEY `fk_parroquia_municipio` (`id_municipio`),
  CONSTRAINT `fk_parroquia_municipio` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `parroquias` (Sample - you'll need to populate this extensively)
-- Example for Distrito Capital (24), Municipio Libertador
INSERT INTO `parroquias` (`id_municipio`, `parroquia`) VALUES
((SELECT id_municipio FROM municipios WHERE id_estado = 24 AND municipio = 'Libertador'), 'Altagracia'),
((SELECT id_municipio FROM municipios WHERE id_estado = 24 AND municipio = 'Libertador'), 'Catedral'),
((SELECT id_municipio FROM municipios WHERE id_estado = 24 AND municipio = 'Libertador'), 'El Recreo');


-- Table structure for table `agentes`
CREATE TABLE `agentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `rif` varchar(20) NOT NULL,
  `sexo` enum('Masculino','Femenino') NOT NULL,
  `telefono_celular` varchar(20) NOT NULL,
  `telefono_local` varchar(20) DEFAULT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `direccion_habitacion` text NOT NULL,
  `id_estado` int(11) NOT NULL,
  `id_ciudad` int(11) NOT NULL,
  `id_municipio` int(11) NOT NULL,
  `id_parroquia` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL, -- For linking to Ion Auth user (optional)
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cedula` (`cedula`),
  UNIQUE KEY `uq_rif` (`rif`),
  UNIQUE KEY `uq_correo_electronico` (`correo_electronico`),
  KEY `fk_agente_estado` (`id_estado`),
  KEY `fk_agente_ciudad` (`id_ciudad`),
  KEY `fk_agente_municipio` (`id_municipio`),
  KEY `fk_agente_parroquia` (`id_parroquia`),
  KEY `fk_agente_cargo` (`id_cargo`),
  KEY `fk_agente_ion_user` (`user_id`),
  CONSTRAINT `fk_agente_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_municipio` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_parroquia` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquias` (`id_parroquia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_agente_ion_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Note on `agentes` table:
-- `user_id` is optional. If an agent is also an Ion Auth user, this can link them.
-- Foreign key constraints for estado, ciudad, municipio, parroquia are set to RESTRICT on delete.
-- Consider if CASCADE is more appropriate for your use case (e.g., if deleting an estado should delete all its agentes).
-- For now, RESTRICT is safer to prevent accidental data loss.

-- Note on dependent dropdowns (ciudades, municipios, parroquias):
-- The schema for `municipios` includes `id_estado` as a direct foreign key.
-- This is to support the JavaScript logic where municipios are fetched directly after an estado is selected.
-- While a more normalized structure might have municipios only linked to ciudades,
-- this structure aligns with the provided AJAX call `agentes/get_municipios` taking `estado_id`.
-- You will need to ensure your data population and application logic correctly handle these relationships.
-- The `ciudades` table is also linked to `id_estado`.
-- The `parroquias` table is linked to `id_municipio`.

-- You will need to populate `ciudades`, `municipios`, and `parroquias` with comprehensive data for Venezuela.
-- The sample data provided is very minimal.
-- The subqueries in INSERT INTO parroquias are just examples and might need adjustment based on actual IDs after municipio inserts.
-- It's generally better to insert municipios first, get their IDs, then insert parroquias referencing those IDs.
