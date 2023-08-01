-- Step 1: Create Backup Table not yet created
CREATE TABLE IF NOT EXISTS hub.permissions_manual_release LIKE hub.permissions;

-- Step 2: Insert the permissions to permissions_manual_release for backup
INSERT hub.permissions_manual_release
	SELECT * FROM hub.permissions
	WHERE hub.permissions.batch_id IN (3978,3979,4195,4011,3966, 4026,3934,4025,3970,3971 ,4077,3944,3952);

-- Step 3: Update released at to NOW if activated between '2017-08-01 00:00:00' and '2018-07-31 23:59:59'
UPDATE hub.permissions permission
INNER JOIN (
	SELECT p.id
	FROM hub.permissions p
	WHERE
		p.batch_id IN (3978,3979,4195,4011,3966, 4026,3934,4025,3970,3971 ,4077,3944,3952) AND
	p.released_at IS NULL AND
		EXISTS(
			SELECT id
			FROM hub.activations a
			WHERE a.permission_id = p.id AND
			a.activated_at BETWEEN '2017-08-01 00:00:00' AND '2018-07-31 23:59:59'
		)
) jp ON permission.id = jp.id
SET permission.released_at = NOW();