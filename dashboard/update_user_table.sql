-- Add status column to users table
ALTER TABLE users ADD COLUMN status ENUM('Pending Approval', 'Approved', 'Blocked') NOT NULL DEFAULT 'Pending Approval';

-- Update existing users to Approved status (optional - remove if you want existing users to be pending)
UPDATE users SET status = 'Approved' WHERE status = 'Pending Approval';

-- Add index for faster status lookups
ALTER TABLE users ADD INDEX idx_status (status); 