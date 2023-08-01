--- STEP 1: In case of rollback, reload the data from the backup table: tngdb.Users_SB116
TRUNCATE TABLE tngdb.Users;
INSERT tngdb.Users SELECT * FROM tngdb.Users_SB116;
