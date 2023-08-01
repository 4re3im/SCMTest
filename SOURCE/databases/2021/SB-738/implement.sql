-- Please backup `tngdb`.`GigyaResetPasswordUsers` and `tngdb`.`BulkDeleteUsers`

ALTER TABLE `tngdb`.`GigyaResetPasswordUsers`
CHANGE COLUMN `Email` `Email` TEXT(150) NULL DEFAULT NULL ;

ALTER TABLE `tngdb`.`BulkDeleteUsers`
CHANGE COLUMN `Email` `Email` TEXT(150) NULL DEFAULT NULL ;