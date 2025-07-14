-- Drop tables in reverse order of creation due to foreign keys
DROP TABLE IF EXISTS `afiliacion_familiares`;
DROP TABLE IF EXISTS `afiliaciones`;
DROP TABLE IF EXISTS `personas`;

-- Table structure for table `personas`
-- This table will store all individuals, both titulares and familiares, to avoid data duplication.
CREATE TABLE `personas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `birthdate` date NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cedula` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table structure for table `afiliaciones`
-- This table stores the main contract information.
CREATE TABLE `afiliaciones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contract_number` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `asesor_id` int(11) NOT NULL,
  `titular_id` int(11) unsigned NOT NULL,
  `plan_type` varchar(50) NOT NULL,
  `plan_amount` decimal(10,2) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_by` int(11) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_contract_number` (`contract_number`),
  KEY `fk_afiliacion_asesor` (`asesor_id`),
  KEY `fk_afiliacion_titular` (`titular_id`),
  KEY `fk_afiliacion_creator` (`created_by`),
  CONSTRAINT `fk_afiliacion_asesor` FOREIGN KEY (`asesor_id`) REFERENCES `agentes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_afiliacion_titular` FOREIGN KEY (`titular_id`) REFERENCES `personas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_afiliacion_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table structure for table `afiliacion_familiares`
-- This is a pivot table linking an affiliation contract to multiple family members (who are also in the 'personas' table).
CREATE TABLE `afiliacion_familiares` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `afiliacion_id` int(11) unsigned NOT NULL,
  `persona_id` int(11) unsigned NOT NULL,
  `parentesco` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_afiliacion_persona` (`afiliacion_id`, `persona_id`),
  KEY `fk_familiar_afiliacion` (`afiliacion_id`),
  KEY `fk_familiar_persona` (`persona_id`),
  CONSTRAINT `fk_familiar_afiliacion` FOREIGN KEY (`afiliacion_id`) REFERENCES `afiliaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_familiar_persona` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Note:
-- The `personas` table uses `cedula` as a unique key. The application logic will need to handle cases where a person (e.g., a familiar) might already exist in the table from a previous affiliation.
-- This is often handled with a "find or create" approach.
-- Foreign keys are set to RESTRICT on the main `afiliaciones` table to prevent accidental deletion of agents or users who have contracts associated with them.
-- Foreign keys are set to CASCADE on the pivot table `afiliacion_familiares` so that if an affiliation or a person is deleted, the link is automatically removed.
--
-- PLEASE EXECUTE THIS SCRIPT ON YOUR DATABASE.
