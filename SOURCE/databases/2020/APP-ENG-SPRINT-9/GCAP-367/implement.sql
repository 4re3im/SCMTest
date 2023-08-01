--- STEP 1
--- Create backup table for tngdb.UserSearchIndexAttributes
CREATE TABLE tngdb.UserSearchIndexAttributes_20190404 LIKE tngdb.UserSearchIndexAttributes;
INSERT tngdb.UserSearchIndexAttributes_20190404 SELECT * FROM tngdb.UserSearchIndexAttributes;

--- STEP 2
--- Execute SQL query below to update institute data of students and teachers
UPDATE UserSearchIndexAttributes usia_1
INNER JOIN (
	SELECT u.uID
	FROM Users u
	LEFT JOIN UserSearchIndexAttributes usia ON u.uID = usia.uID
	JOIN UserGroups ug ON u.uID = ug.uID
	JOIN Groups g ON ug.gID = g.gID
	WHERE g.gName = 'Student'
	OR (
		g.gName = 'Teacher'
		AND u.uID NOT IN (
			SELECT u.uID FROM Users u
			JOIN UserGroups ug ON u.uID = ug.uID
			JOIN Groups g ON ug.gID = g.gID
			WHERE g.gName = 'Student'
		)
	)
) as users ON users.uID = usia_1.uID
SET 
usia_1.ak_uSchoolName = NULL,
usia_1.ak_uSchoolAddress = NULL, 
usia_1.ak_uPostcode = NULL;

--- STEP 3 (Optional)
--- Run the SQL query below to check if all the data are updated succesfully, it should return 1 row only with null values.
SELECT DISTINCT usia.ak_uSchoolName, usia.ak_uSchoolAddress, usia.ak_uPostcode
FROM Users u
LEFT JOIN UserSearchIndexAttributes usia ON u.uID = usia.uID
JOIN UserGroups ug ON u.uID = ug.uID
JOIN Groups g ON ug.gID = g.gID
WHERE g.gName = 'Student'
OR (g.gName = 'Teacher'
AND u.uID NOT IN (
	SELECT u.uID FROM Users u
	JOIN UserGroups ug ON u.uID = ug.uID
	JOIN Groups g ON ug.gID = g.gID
	WHERE g.gName = 'Student'
));