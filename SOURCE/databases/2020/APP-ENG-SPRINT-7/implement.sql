ALTER TABLE tngdb.ProvisioningUsers MODIFY COLUMN `uID` varchar(200);
ALTER TABLE tngdb.Hotmaths MODIFY COLUMN `UserID` varchar(200);

ALTER TABLE
  `tngdb`.`ProvisioningUsers`
ADD
  COLUMN `Password` VARCHAR(255) NULL
AFTER
  `in_gigya`,
ADD
  COLUMN `School` VARCHAR(100) NULL
AFTER
  `Password`,
ADD
  COLUMN `State` VARCHAR(100) NULL
AFTER
  `School`,
ADD
  COLUMN `PostCode` VARCHAR(20) NULL
AFTER
  `State`,
ADD
  COLUMN `HMSchoolID` VARCHAR(15) NULL
AFTER
  `PostCode`,
ADD
  COLUMN `ClassNameMaths` VARCHAR(100) NULL
AFTER
  `HMSchoolID`,
ADD
  COLUMN `ClassNameHumanities` VARCHAR(100) NULL
AFTER
  `ClassNameMaths`,
ADD
  COLUMN `ClassNameICEEM` VARCHAR(100) NULL
AFTER
  `ClassNameHumanities`;