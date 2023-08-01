--- STEP 1: In case of rollback, reload the data from the backup table: hub.permissions_SB3
TRUNCATE TABLE hub.permissions;
INSERT hub.permissions SELECT * FROM hub.permissions_SB3;