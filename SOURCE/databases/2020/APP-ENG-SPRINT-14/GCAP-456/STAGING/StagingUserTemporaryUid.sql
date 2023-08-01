--- Step 1: Creation of Temporary table for Array of uID

CREATE TABLE UserIdTemporaryTable (
    uId int,
    INDEX (uId)
);

--- Step 2: Store the file and get the path by right clicking the file > Properties
LOAD DATA LOCAL INFILE 'C:\\path\\to\\file\\uIDlist.csv' INTO TABLE UserIdTemporaryTable;

--- Step 3: Check count, result should be: 161,230 (Live only)
SELECT count(*) FROM UserIdTemporaryTable;

--- Step 4: Get result and export then send to devs
SELECT * FROM Users WHERE uID IN (SELECT uID FROM UserIdTemporaryTable);
