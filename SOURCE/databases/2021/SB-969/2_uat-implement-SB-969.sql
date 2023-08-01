--- STEP 1: Remove previous backup table
DROP TABLE IF EXISTS peas.activations_SB_969;

--- STEP 2: Create backup table for peas.activations
CREATE TABLE peas.activations_SB_969 LIKE peas.activations;

INSERT peas.activations_SB_969 SELECT * FROM peas.activations
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
UPDATE peas.activations 
SET 
    ended_at = '2022-12-31 12:00:00',
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
AND ended_at < '2022-12-31 12:00:00';
