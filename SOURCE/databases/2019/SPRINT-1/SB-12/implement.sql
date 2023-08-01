--- STEP 1: Create backup table for hub.permissions
CREATE TABLE hub.permissions_Sprint1_2019 LIKE hub.permissions;
INSERT hub.permissions_Sprint1_2019 SELECT * FROM hub.permissions;

--- STEP 2: Get all permission IDs then insert the IDs ($batchIds) placed in change ticket
--- STAGING: staging_batch_ids.csv
--- LIVE: live_batch_ids.csv
SELECT perm.id AS permID FROM hub.permissions perm
INNER JOIN hub.batches batch ON perm.batch_id = batch.id WHERE perm.proof IS NOT NULL AND
perm.limit = 1 AND batch.id IN ([$batchIds]);

--- STEP 3: Export the file from the SELECT query on STEP 2.

--- STEP 4: Run the ff: command and supply the IDs ($permId) from the exported file on STEP 3.
UPDATE hub.permissions AS perm SET perm.limit = 3 WHERE id IN ([$permId]);