--- Rollback plan
--- Replace the records on peas.activations table based from the backup table peas.activations_BTS_2021
REPLACE INTO
    peas.activations(id,permission_id,user_id,ended_at,activated_at,metadata,created_at,updated_at)
SELECT * FROM peas.activations_BTS_2021;