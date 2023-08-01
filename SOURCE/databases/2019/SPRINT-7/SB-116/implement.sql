-- Step 1: Create Backup Table not yet created
CREATE TABLE IF NOT EXISTS tngdb.Users_SB116 LIKE tngdb.Users;

-- Step 2: Insert the users table to users_sb116 for backup
INSERT tngdb.Users_SB116 SELECT * FROM tngdb.Users;

-- Step 3: Update uIsVerified to 1 and uIsActive of all users created from 2018-07-01 onwards
UPDATE Users SET uIsValidated = 1, uIsActive = 1 WHERE uDateAdded > '2018-07-01 00:00:00';