--- STEP 1
--- Rollback the backup table for tngdb.UserValidationHashes
--- Rename the table tngdb.UserValidationHashes_GCAP462 to tngdb.UserValidationHashes
RENAME TABLE `tngdb.UserValidationHashes` TO `tngdb.UserValidationHashes_bak_GCAP462`;
RENAME TABLE `tngdb.UserValidationHashes_GCAP462` TO `tngdb.UserValidationHashes`;