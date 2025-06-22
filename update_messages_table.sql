-- Add message_type column if it doesn't exist
ALTER TABLE messages ADD COLUMN IF NOT EXISTS message_type VARCHAR(20) DEFAULT 'text';

-- Update existing messages to have 'text' type
UPDATE messages SET message_type = 'text' WHERE message_type IS NULL; 