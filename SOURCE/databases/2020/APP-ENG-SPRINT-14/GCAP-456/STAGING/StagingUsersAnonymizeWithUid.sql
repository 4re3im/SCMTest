USE `tngdb`;

DELIMITER $$
USE `tngdb`$$
CREATE PROCEDURE `anonymize_users`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    
    /* Table Users variables */ 
    DECLARE uID_var INT(10);
    DECLARE uName_var VARCHAR(255);
    DECLARE uEmail_var VARCHAR(255);
    DECLARE uPassword_var VARCHAR(255);
    DECLARE uIsActive_var VARCHAR(1);
    DECLARE uIsValidated_var TINYINT(4);
    DECLARE uIsFullRecord_var TINYINT(1);
    DECLARE uDateAdded_var DATETIME;
    DECLARE uHasAvatar_var TINYINT(1);
    DECLARE uLastOnline_var INT(10);
    DECLARE uLastLogin_var INT(10);
    DECLARE uLastIP_var BIGINT(10);
    DECLARE uPreviousLogin_var INT(10);
    DECLARE uNumLogins_var INT(10);
    DECLARE uTimeZone_var VARCHAR(255);
    DECLARE uDefaultLanguage_var VARCHAR(32);
    DECLARE oldPasswordTested_var TINYINT(1);
    
    /* Custom variables */
    DECLARE ID_loop_var VARCHAR(11);
    DECLARE users_cursor CURSOR FOR SELECT uID, uName, uEmail, uPassword, uIsActive, uIsValidated, uIsFullRecord, uDateAdded, uHasAvatar, uLastOnline, uLastLogin, uLastIP, uPreviousLogin, uNumLogins, uTimeZone, uDefaultLanguage, oldPasswordTested FROM Users
    WHERE uID IN (SELECT uID FROM UserIdTemporaryTable) ORDER BY uID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN users_cursor;
        users_loop: LOOP
            FETCH users_cursor INTO uID_var, uName_var, uEmail_var, uPassword_var, uIsActive_var, uIsValidated_var, uIsFullRecord_var, uDateAdded_var, uHasAvatar_var, uLastOnline_var, uLastLogin_var, uLastIP_var, uPreviousLogin_var, uNumLogins_var, uTimeZone_var, uDefaultLanguage_var, oldPasswordTested_var;
            
            IF done 
                THEN LEAVE users_loop; 
            END IF;
                
            /* Anonymize data here */
            SET ID_loop_var = CAST(uID_var as CHAR(100));
            IF (uName_var IS NOT NULL OR uName_var <> '') THEN SET uName_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (uEmail_var IS NOT NULL OR uEmail_var <> '') THEN SET uEmail_var = CONCAT(ID_loop_var, '@anonymous.com'); END IF;
            SET uLastIP_var = 0;
            
            /* Update data to anonymize the original table */
            UPDATE `Users` 
            SET 
				`uName` = uName_var, 
                `uEmail` = uEmail_var,
                `uIsActive` = 0,
                `uIsValidated` = 1
			WHERE uID = uID_var;
            
        END LOOP;
    CLOSE users_cursor;
    
    SELECT COUNT(*) FROM Users LIMIT 1; /* To eliminate the warning 'zero rows fetched ..' */

END$$

DELIMITER ;
