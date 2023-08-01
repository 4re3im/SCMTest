--- Rollback plan
--- Replace the records on hub.activations table based from the backup table hub.activations_Sprint_8_2020
REPLACE INTO
    hub.activations(id,permission_id,user_id,ended_at,activated_at,metadata,created_at,updated_at)
SELECT * FROM hub.activations_Sprint_8_2020;