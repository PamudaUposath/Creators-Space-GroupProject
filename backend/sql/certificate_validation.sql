-- Enhanced progress tracking for certificate validation
-- Add columns to track actual watching behavior vs video skipping

ALTER TABLE lesson_progress ADD COLUMN IF NOT EXISTS actual_watch_time DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Actual time spent watching (not skipping)';
ALTER TABLE lesson_progress ADD COLUMN IF NOT EXISTS watch_sessions INT DEFAULT 0 COMMENT 'Number of separate watch sessions';
ALTER TABLE lesson_progress ADD COLUMN IF NOT EXISTS skipped_duration DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total time skipped by seeking';
ALTER TABLE lesson_progress ADD COLUMN IF NOT EXISTS is_eligible_for_certificate BOOLEAN DEFAULT FALSE COMMENT 'True if user watched video properly without excessive skipping';
ALTER TABLE lesson_progress ADD COLUMN IF NOT EXISTS last_position_change DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Last recorded position change';
ALTER TABLE lesson_progress ADD COLUMN IF NOT EXISTS seek_violations INT DEFAULT 0 COMMENT 'Number of seek/skip violations';

-- Add certificate eligibility tracking to enrollments
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS certificate_eligible BOOLEAN DEFAULT FALSE COMMENT 'Eligible for course certificate';
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS lessons_completed_properly INT DEFAULT 0 COMMENT 'Number of lessons watched without skipping';
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS total_lessons_required INT DEFAULT 0 COMMENT 'Total lessons required for certificate';

-- Update existing progress records to set default values
UPDATE lesson_progress SET actual_watch_time = last_watched_time WHERE actual_watch_time = 0;
UPDATE lesson_progress SET is_eligible_for_certificate = TRUE WHERE completion_percentage >= 95 AND actual_watch_time >= (total_duration * 0.9);