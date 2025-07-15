-- Add columns to support dashboard functionality

-- Add a user_id to the personas table to link a person to their system login
ALTER TABLE `personas`
ADD `user_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `foto_perfil`,
ADD INDEX `idx_user_id` (`user_id`),
ADD CONSTRAINT `fk_persona_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add a status to the afiliaciones table for tracking and stats
ALTER TABLE `afiliaciones`
ADD `status` VARCHAR(50) NOT NULL DEFAULT 'Pendiente' AFTER `observaciones`,
ADD INDEX `idx_status` (`status`);

-- Description of changes:
--
-- `personas.user_id`:
--   - This will link a person record (titular or familiar) to an actual system user in the `users` table.
--   - This is crucial for the Client Dashboard, so a logged-in user can see the affiliation contract where they are the titular.
--
-- `afiliaciones.status`:
--   - This will track the status of an affiliation (e.g., 'Pendiente', 'Activo', 'Rechazado').
--   - It defaults to 'Pendiente'.
--   - This is crucial for the Agent Dashboard stats.
--
-- PLEASE EXECUTE THIS SCRIPT ON YOUR DATABASE.
