ALTER TABLE `tngdb`.`CupGoTabs` 
ADD COLUMN `Cogbooks` VARCHAR(1) NULL DEFAULT 'N' AFTER `KnowledgeCheck`;

INSERT INTO `tngdb`.`CupGoTabGroup` (`ID`, `group_name`, `created_at`) VALUES (11, 'Coursebooks',now());