--- STEP 1: Remove previous backup table
DROP TABLE IF EXISTS hub.activations_Sprint_8_2020;

--- STEP 2: Create backup table for hub.activations
CREATE TABLE hub.activations_Sprint_8_2020 LIKE hub.activations;

INSERT hub.activations_Sprint_8_2020 SELECT * FROM hub.activations
WHERE user_id IN ('2903010','2903007','2903009','2903008')'';


--- STEP 3: Update activations
UPDATE hub.activations 
SET 
    ended_at = '2021-12-31 12:00:00',
    metadata = JSON_SET(metadata, "$.DateDeactivated", null)
WHERE
    user_id IN ('2903010','2903007','2903009','2903008')
AND ended_at < '2021-12-31 12:00:00';
