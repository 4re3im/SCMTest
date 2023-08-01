CREATE EVENT ReleaseDurationTypePermissions
ON SCHEDULE EVERY 1 DAY
STARTS CONCAT(DATE(NOW()), ' 13:00:00')
DO
UPDATE hub.permissions
             SET 
                released_at = NOW()
             WHERE
        EXISTS (
            SELECT
                1 
            FROM
                (SELECT
                    p.id,
                    p.limit,
                    (SELECT
                        COUNT(activations.id) 
                    FROM
                        hub.activations 
                    WHERE
                        activations.permission_id = p.id) activations,
                    JSON_EXTRACT(e.metadata,
                    '$.Type') 
                FROM
                    hub.permissions p 
                INNER JOIN
                    hub.activations a 
                        ON p.id = a.permission_id 
                INNER JOIN
                    hub.entitlements e 
                        ON p.entitlement_id = e.id 
                INNER JOIN
                    hub.products pr 
                        ON e.product_id = pr.id 
                INNER JOIN
                    hub.platforms pl 
                        ON pr.platform_id = pl.id 
                WHERE
                    DATE_FORMAT(a.ended_at, '%Y-%m-%d') <= DATE_ADD(CURDATE(), INTERVAL -1 DAY) 
                    AND p.proof IS NOT NULL 
                    AND p.released_at IS NULL 
                    AND LOWER(JSON_UNQUOTE(JSON_EXTRACT(e.metadata, "$.Type"))) = 'duration' 
                    AND pl.id = 1 
                GROUP BY
                    p.id 
                HAVING
                    activations < p.limit 
                ORDER BY
                    NULL) AS p1 
                WHERE
                    (
                        p1.id = permissions.id
                    ))