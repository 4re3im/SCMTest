ALTER TABLE `tngdb`.`GigyaResetPasswordUsers`
CHANGE COLUMN `Email` `Email` VARCHAR(45) NULL DEFAULT NULL ;

ALTER TABLE `tngdb`.`BulkDeleteUsers`
CHANGE COLUMN `Email` `Email` VARCHAR(45) NULL DEFAULT NULL ;