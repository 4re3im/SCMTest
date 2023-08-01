ALTER TABLE `tngdb`.`ProvisioningFiles`
  ADD COLUMN `IsProvisionedInGigya` INT NULL DEFAULT 0
  AFTER `StaffID`;
