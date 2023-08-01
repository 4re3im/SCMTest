---Change data type for updated_at
ALTER TABLE tngdb.Hotmaths_API
MODIFY COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

--- Run: UPDATE table
UPDATE tngdb.Hotmaths_API
SET access_token = 'cc64a5b1-23c8-45fe-b278-b60fc12b1d07', refresh_token = 'cbb95b47-e62b-4198-b08c-7e53582fe946'
WHERE ID = 1 AND env = 'testing';