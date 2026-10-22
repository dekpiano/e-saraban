-- e-Saraban: allow memo routing to real personnel records without requiring an e-Saraban account.
-- Run once against database skjacth_saraban.
SET @fk_name = (
  SELECT CONSTRAINT_NAME
  FROM information_schema.KEY_COLUMN_USAGE
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'document_routes'
    AND COLUMN_NAME = 'user_id'
    AND REFERENCED_TABLE_NAME = 'users'
  LIMIT 1
);
SET @drop_fk_sql = IF(@fk_name IS NULL, 'SELECT 1', CONCAT('ALTER TABLE document_routes DROP FOREIGN KEY `', @fk_name, '`'));
PREPARE drop_fk_stmt FROM @drop_fk_sql;
EXECUTE drop_fk_stmt;
DEALLOCATE PREPARE drop_fk_stmt;

ALTER TABLE document_routes
  MODIFY COLUMN user_id INT NULL COMMENT 'e-Saraban user ID when linked; nullable for personnel-only approvers',
  ADD COLUMN pers_id VARCHAR(50) NULL COMMENT 'Personnel ID from skjacth_personnel.tb_personnel' AFTER user_id,
  ADD COLUMN approver_name VARCHAR(255) NULL COMMENT 'Snapshot of approver name from personnel' AFTER pers_id,
  ADD COLUMN personnel_position VARCHAR(150) NULL COMMENT 'Snapshot of personnel position' AFTER approver_name,
  ADD COLUMN personnel_learning VARCHAR(100) NULL COMMENT 'Personnel learning group ID' AFTER personnel_position,
  ADD INDEX idx_document_routes_pers_id (pers_id);

-- Recreate the user foreign key with SET NULL so removing a local account does not erase routing history.
ALTER TABLE document_routes
  ADD CONSTRAINT fk_document_routes_user_nullable
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
