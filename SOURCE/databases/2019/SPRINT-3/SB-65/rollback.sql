-- Rollback
UPDATE hub.permissions p
INNER JOIN hub.permissions_manual_release mp
SET p.limit = mp.limit, p.released_at = mp.released_at
WHERE p.batch_id IN (3978,3979,4195,4011,3966, 4026,3934,4025,3970,3971 ,4077,3944,3952)