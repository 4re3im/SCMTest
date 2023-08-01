-- Checker for Users, send results to devs
SELECT * FROM Users WHERE uName LIKE '%Anonymous%';
SELECT * FROM Users WHERE uID IN (SELECT uID FROM UserIdTemporaryTable);
SELECT * FROM Users WHERE uID NOT IN (SELECT uID FROM UserIdTemporaryTable) AND uName like '%Anonymous%';


-- Checker for UserSearchIndexAttributes, send results to devs
SELECT * FROM UserSearchIndexAttributes WHERE ak_FirstName LIKE '%Anonymous%' OR ak_go_user_first_name LIKE '%Anonymous%';
SELECT * FROM UserSearchIndexAttributes WHERE uID IN (SELECT uID FROM UserIdTemporaryTable);
SELECT * FROM UserSearchIndexAttributes WHERE uID NOT IN (SELECT uID FROM UserIdTemporaryTable) AND (ak_FirstName like '%Anonymous%' OR ak_go_user_first_name LIKE '%Anonymous%');
