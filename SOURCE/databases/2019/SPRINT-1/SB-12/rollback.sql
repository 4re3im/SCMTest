--- STEP 1: In case of rollback, reload the data from the backup table to hub.permissions_Sprint1_2019
TRUNCATE TABLE hub.permissions;
INSERT hub.permissions; SELECT * FROM hub.permissions_Sprint1_2019;