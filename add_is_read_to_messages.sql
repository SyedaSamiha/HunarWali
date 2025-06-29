-- Add is_read column to messages table if it doesn't exist
ALTER TABLE messages ADD COLUMN IF NOT EXISTS is_read TINYINT(1) DEFAULT 0;

-- Update existing messages to be marked as read
UPDATE messages SET is_read = 0 WHERE is_read IS NULL;