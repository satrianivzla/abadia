-- Add columns to support agent hierarchy (Supervisor -> Agente)
-- and for zone management (Gerente de Zona)

ALTER TABLE `agentes`
ADD `supervisor_id` INT(11) NULL DEFAULT NULL AFTER `user_id`,
ADD `zona_id` INT(11) NULL DEFAULT NULL AFTER `supervisor_id`,
ADD INDEX `idx_supervisor_id` (`supervisor_id`),
ADD INDEX `idx_zona_id` (`zona_id`),
ADD CONSTRAINT `fk_agente_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `agentes`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Description of changes:
--
-- `supervisor_id` INT(11) NULL DEFAULT NULL:
--   - This column will store the `id` of another agent who is the supervisor of this agent.
--   - It's a self-referencing foreign key.
--   - If NULL, the agent has no direct supervisor (they might be a supervisor themselves, or a top-level agent).
--   - ON DELETE SET NULL: If a supervisor agent record is deleted, any agents they managed will have their `supervisor_id` set to NULL, rather than being deleted.
--
-- `zona_id` INT(11) NULL DEFAULT NULL:
--   - A placeholder for a future 'zonas' table to link agents to a geographical or administrative zone, managed by a 'Gerente de Zona'.
--
-- PLEASE EXECUTE THIS SCRIPT ON YOUR DATABASE.
