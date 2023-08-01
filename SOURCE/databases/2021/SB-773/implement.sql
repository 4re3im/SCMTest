--- Run: Alter table
ALTER TABLE tngdb.ProvisioningUsers
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;