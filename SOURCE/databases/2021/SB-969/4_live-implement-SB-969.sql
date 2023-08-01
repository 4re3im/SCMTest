--- STEP 1: Remove previous backup table
DROP TABLE IF EXISTS peas.activations_SB_969;

--- STEP 2: Create backup table for peas.activations
CREATE TABLE peas.activations_SB_969 LIKE peas.activations;

INSERT peas.activations_SB_969 SELECT * FROM peas.activations
WHERE user_id IN ([INSERT IDs from attached file liveUIDs.txt]);


--- STEP 3: Update activations
UPDATE peas.activations 
SET 
    ended_at = '2022-12-31 12:00:00',
    metadata = JSON_SET(metadata, "$.DateDeactivated", null)
WHERE
    user_id IN ([INSERT IDs from attached file liveUIDs.txt])
AND ended_at < '2022-12-31 12:00:00';
