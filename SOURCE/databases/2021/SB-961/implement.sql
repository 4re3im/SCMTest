---Change data type for updated_at
ALTER TABLE tngdb.Hotmaths_API
MODIFY COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

--- Run: UPDATE table
UPDATE tngdb.Hotmaths_API
SET access_token = '105b3055-b440-4d57-9e41-07bf6dd16692', refresh_token = 'c1ab81b2-6364-47d4-ba5e-dde5a03f22fd'
WHERE ID = 2 AND env = 'production';