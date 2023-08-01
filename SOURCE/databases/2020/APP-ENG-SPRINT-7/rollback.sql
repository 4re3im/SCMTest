ALTER TABLE tngdb.ProvisioningUsers MODIFY COLUMN `uID` INT(11);
ALTER TABLE tngdb.Hotmaths MODIFY COLUMN `UserID` INT(11);


ALTER TABLE `tngdb`.`ProvisioningUsers`
DROP COLUMN `Password`,
DROP COLUMN `School`,
DROP COLUMN `State`,
DROP COLUMN `PostCode`,
DROP COLUMN `HMSchoolID`,
DROP COLUMN `ClassNameMaths`,
DROP COLUMN `ClassNameHumanities`,
DROP COLUMN `ClassNameICEEM`;