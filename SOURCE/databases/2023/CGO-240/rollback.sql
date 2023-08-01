ALTER TABLE `tngdb`.`CupGoTabs` 
DROP COLUMN `Cogbooks`;

DELETE FROM `tngdb`.`CupGoTabGroup` WHERE (`group_name` = 'Coursebooks');