--- STEP 1: Query all the duplicated access codes on hub.permissions table; Select All duplicate proofs
SELECT
    p.entitlement_id entID,
    p.id p_id,
    p.proof,
    p.limit,
    p.is_active is_active,
    p.expired_at expired_at,
    p.released_at released_at,
    p.created_at created_at,
    b.name batch_name,
    JSON_EXTRACT(e.metadata, '$.HmID') HmID,
    JSON_EXTRACT(e.metadata, '$.Type') entitlementType,
    JSON_EXTRACT(pr.metadata, '$.ISBN_13') ISBN_13,
    JSON_EXTRACT(pr.metadata, '$.CMS_Name') CMS_Name,
    (SELECT COUNT(*) FROM hub.activations WHERE permission_id = p.id) activationCount
FROM
hub.permissions p
JOIN hub.batches b ON p.batch_id = b.id
JOIN hub.entitlements e ON b.entitlement_id = e.id
JOIN hub.products pr ON e.product_id = pr.id
WHERE p.proof IN (
    SELECT p2.proof from (
        SELECT p1.proof, COUNT(*) c FROM hub.permissions p1 WHERE p1.proof IS NOT NULL GROUP BY p1.proof HAVING c > 1
    ) as p2
)
ORDER BY p.proof ASC;

--- STEP 2: Export to csv file with the following format:
-- Staging - staging_duplicated_codes.csv
-- Live - live_duplicated_codes.csv

--- STEP 3: Create backup table for hub.permissions
CREATE TABLE hub.permissions_SB3 LIKE hub.permissions;
INSERT hub.permissions_SB3 SELECT * FROM hub.permissions;

--- STEP 4: Dev will give a go signal to run Step 5

--- STEP 5: Get the following file attached on the change ticket
-- Staging - staging_permission_duplicates.csv
-- Live    - live_permission_duplicates.csv

--- STEP 6: Using the IDs on the file from Step 5, replace $permissionID then multiply hub.permission limit field to 2
UPDATE hub.permissions perm SET perm.limit = perm.limit * 2 WHERE id IN ($permissionID);

--- STEP 7: Get the following file attached on the change ticket
-- Staging - staging_codes_toDelete.csv
-- Live    - live_codes_toDelete.csv

--- STEP 8: Get the IDs from the file on Step 7, replace the $permissionID then, run delete duplicated access codes
DELETE FROM hub.permissions WHERE id IN ($permissionID);

--- STEP 9:
ALTER TABLE hub.permissions ADD UNIQUE (proof);