-- Add columns for soft delete functionality to the 'agentes' table

ALTER TABLE `agentes`
ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL,
ADD `deleted_by` INT(11) UNSIGNED NULL DEFAULT NULL,
ADD INDEX `idx_deleted_at` (`deleted_at`),
ADD CONSTRAINT `fk_agente_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Description of changes:
--
-- `deleted_at` TIMESTAMP NULL DEFAULT NULL:
--   - This column will store the timestamp of when an agent was "soft-deleted".
--   - If it's NULL, the agent is considered active.
--   - If it has a timestamp, the agent is considered deleted.
--
-- `deleted_by` INT(11) UNSIGNED NULL DEFAULT NULL:
--   - This column will store the user ID of the admin/leader who performed the deletion.
--   - It's a foreign key that references the `id` in the `users` table (from Ion Auth).
--   - ON DELETE SET NULL: If the admin who deleted the agent is ever deleted themselves, the `deleted_by` field for the agent will be set to NULL instead of deleting the agent record.
--
-- `idx_deleted_at` INDEX:
--   - An index on the `deleted_at` column is added to speed up queries that filter for active records (WHERE deleted_at IS NULL).
--
-- After running this script, all existing agents will have NULL for `deleted_at` and `deleted_by`,
-- correctly marking them as active.
--
-- PLEASE EXECUTE THIS SCRIPT ON YOUR DATABASE.
