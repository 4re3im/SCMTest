-- 1. Create ProvisioningUsers backup
CREATE TABLE tngdb.ProvisioningUsers_GCAP479 LIKE tngdb.ProvisioningUsers;
INSERT tngdb.ProvisioningUsers_GCAP479 SELECT * FROM tngdb.ProvisioningUsers;

-- 2. Add in_gigya column
ALTER TABLE `tngdb`.`ProvisioningUsers`
ADD COLUMN `in_gigya` TINYINT(1) NULL DEFAULT NULL AFTER `completed`;
