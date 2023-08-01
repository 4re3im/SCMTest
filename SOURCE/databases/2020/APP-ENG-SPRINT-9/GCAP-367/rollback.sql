--- STEP 1
--- Rollback the backup table for tngdb.UserSearchIndexAttributes
--- Rename the table tngdb.UserSearchIndexAttributes_201904 to tngdb.UserSearchIndexAttributes
RENAME TABLE `tngdb.UserSearchIndexAttributes` TO `tngdb.UserSearchIndexAttributes_bak_201904`;
RENAME TABLE `tngdb.UserSearchIndexAttributes_201904` TO `tngdb.UserSearchIndexAttributes`;