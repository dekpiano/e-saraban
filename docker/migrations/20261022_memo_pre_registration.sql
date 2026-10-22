-- e-Saraban: allow internal memos to be submitted to Saraban before an official number is issued.
-- Run against database skjacth_saraban. Existing issued document numbers are preserved.
ALTER TABLE documents
  MODIFY COLUMN doc_no VARCHAR(100) NULL COMMENT 'เลขที่หนังสือ (รอเจ้าหน้าที่สารบรรณออกเลขได้)',
  MODIFY COLUMN secret_level ENUM('normal', 'confidential', 'secret', 'top_secret') DEFAULT 'normal' COMMENT 'ชั้นความลับ',
  MODIFY COLUMN status ENUM('draft', 'pending_saraban', 'routing', 'completed', 'returned', 'canceled') DEFAULT 'routing';
