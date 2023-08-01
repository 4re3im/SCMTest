-- For rollback purposes

-- Step 1: Rename affected/anonymized tables
RENAME TABLE Users TO Users_anonymized, UserSearchIndexAttributes TO UserSearchIndexAttributes_anonymized;

-- Step 2: Rename backup tables to original
RENAME TABLE Users_GCAP456 TO Users, UserSearchIndexAttributes_GCAP456 TO UserSearchIndexAttributes;

-- Step 3: Delete the affected renamed tables from step 1
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE Users_anonymized;
DROP TABLE UserSearchIndexAttributes_anonymized;
SET FOREIGN_KEY_CHECKS = 1;