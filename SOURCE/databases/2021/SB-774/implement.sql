--- STEP 1: Remove previous backup table
DROP TABLE IF EXISTS tngdb.ProvisioningUsersSB_774;

--- STEP 2: Create backup table
CREATE TABLE tngdb.ProvisioningUsersSB_774 LIKE tngdb.ProvisioningUsers;
INSERT tngdb.ProvisioningUsersSB_774 SELECT * FROM tngdb.ProvisioningUsers;

---- STEP 3: Create new table
CREATE TABLE IF NOT EXISTS `tngdb`.`ProvisioningUsersCountSummary` (
`ID` INT NOT NULL AUTO_INCREMENT,
`IdCount` INT NULL,
`UidCount` INT NULL,
`FileIDCount` INT NULL,
`Date` DATETIME NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID`));

---- STEP 4: Run
---- If the value is set to OFF, notify Dev/GHS to turn the settings to ON
---- SET GLOBAL event_scheduler = ON;
SHOW GLOBAL VARIABLES LIKE 'event%';

---- STEP 5: Run and kindly note the timestamp and align the time on STEP 7 (it should be 9PM MNL time)
SELECT CURRENT_TIMESTAMP;

---- STEP 6: Run
---- SHOW EVENTS FROM tngdb;
DROP EVENT IF EXISTS ProvisioningUsersEvent;

---- STEP 7: Execute event
delimiter |
CREATE EVENT IF NOT EXISTS ProvisioningUsersEvent
ON SCHEDULE EVERY 1 DAY
STARTS CONCAT(DATE(NOW()), ' 11:30:00')
DO BEGIN
	INSERT INTO `tngdb`.`ProvisioningUsersCountSummary` (`IdCount`, `UidCount`, `FileIDCount`, `Date`)
      SELECT
        count(`ID`) AS `IdCount`,
        count(distinct(`uID`)) AS `UidCount`,
        count(distinct(`FileID`)) AS `FileIDCount`,
        `created_at` AS `Date`
      FROM ProvisioningUsers
      WHERE created_at < DATE(NOW())
      GROUP BY created_at;

	SET FOREIGN_KEY_CHECKS=0;
	DELETE FROM tngdb.ProvisioningUsers WHERE created_at < SUBDATE(NOW(), 1);
	SET FOREIGN_KEY_CHECKS=1;
END |
delimiter ;

---- STEP 8: Delete backup table after successfully running all the scripts (Kindly get confirmation from the Devs)
DROP TABLE IF EXISTS `tngdb`.`ProvisioningUsersSB_774`;