--- STEP 1
--- Rollback the backup table for tngdb.UserValidationHashes
--- Rename the table tngdb.UserValidationHashes_201904 to tngdb.UserValidationHashes
RENAME TABLE `tngdb.UserValidationHashes` TO `tngdb.UserValidationHashes_bak_201904`;
RENAME TABLE `tngdb.UserValidationHashes_201904` TO `tngdb.UserValidationHashes`;