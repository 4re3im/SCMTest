--- STEP 1
--- Create backup table for tngdb.UserValidationHashes
CREATE TABLE tngdb.UserValidationHashes_GCAP462 LIKE tngdb.UserValidationHashes;
INSERT tngdb.UserValidationHashes_GCAP462 SELECT * FROM tngdb.UserValidationHashes;

--- STEP 2
DELETE uvh FROM tngdb.UserValidationHashes uvh
INNER JOIN tngdb.Users u ON uvh.uID = u.uID
INNER JOIN tngdb.UserGroups ug ON u.uID = ug.uID
WHERE ug.gID IN (4, 5)
AND uvh.type = 2;