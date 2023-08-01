--- Rollback plan
--- Replace the records on hub_uat.activations table based from the backup table hub_uat.activations_Sprint_8_2020
REPLACE INTO
    hub_uat.activations(id,permission_id,user_id,ended_at,activated_at,metadata,created_at,updated_at)
SELECT * FROM hub_uat.activations_Sprint_8_2020;