--- STEP 1: Remove previous backup table
DROP TABLE IF EXISTS hub_uat.activations_Sprint_8_2020;

--- STEP 2: Create backup table for hub_uat.activations
CREATE TABLE hub_uat.activations_Sprint_8_2020 LIKE hub_uat.activations;

INSERT hub_uat.activations_Sprint_8_2020 SELECT * FROM hub_uat.activations
WHERE user_id IN ('3917230',
'3917229',
'3917228',
'3917227',
'3917226',
'beecc45ed2f6436f8402825da72ae51c',
'5a5f70a968a34105b21aa14388a4203a',
'b922d0c4086542ba9c8abe3dc50106a3',
'f4579b66d2b3400dac6f858565356e70',
'c824f5d7f40e4575b03a09cdfac09024'
);


--- STEP 3: Update activations
UPDATE hub_uat.activations 
SET 
    ended_at = '2021-12-31 12:00:00',
    metadata = JSON_SET(metadata, "$.DateDeactivated", null)
WHERE
    user_id IN ('3917230',
'3917229',
'3917228',
'3917227',
'3917226',
'beecc45ed2f6436f8402825da72ae51c',
'5a5f70a968a34105b21aa14388a4203a',
'b922d0c4086542ba9c8abe3dc50106a3',
'f4579b66d2b3400dac6f858565356e70',
'c824f5d7f40e4575b03a09cdfac09024'
)
AND ended_at < '2021-12-31 12:00:00';
