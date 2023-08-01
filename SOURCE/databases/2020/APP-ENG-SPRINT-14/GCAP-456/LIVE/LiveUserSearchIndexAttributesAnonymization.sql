USE `tngdb`;

DELIMITER $$
USE `tngdb`$$
CREATE PROCEDURE `anonymize_user_attributes`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    
    /* Table UserSearchIndexAttributes variables */
    DECLARE uID_var INT(11);
    DECLARE ak_profile_private_messages_enabled_var TINYINT(4);
    DECLARE ak_profile_private_messages_notification_enabled_var TINYINT(4);
    DECLARE ak_go_user_access_var LONGTEXT;
    DECLARE ak_go_user_activated_date_var VARCHAR(255);
    DECLARE ak_go_user_activation_code_var VARCHAR(255);
    DECLARE ak_go_user_address1_var VARCHAR(255);
    DECLARE ak_go_user_address2_var VARCHAR(255);
    DECLARE ak_go_user_administrator_var VARCHAR(255);
    DECLARE ak_go_user_allow_marketing_contact_var VARCHAR(255);
    DECLARE ak_go_user_auth_token_var VARCHAR(255);
    DECLARE ak_go_user_country_var VARCHAR(255);
    DECLARE ak_go_user_deactivated_date_var VARCHAR(255);
    DECLARE ak_go_user_department_var VARCHAR(255);
    DECLARE ak_go_user_ent_appid_var VARCHAR(255);
    DECLARE ak_go_user_ent_auth_token_var VARCHAR(255);
    DECLARE ak_go_user_ent_integrator_var VARCHAR(255);
    DECLARE ak_go_user_ent_offer_var VARCHAR(255);
    DECLARE ak_go_user_ent_productid_var VARCHAR(255);
    DECLARE ak_go_user_ent_subscription_renew_var VARCHAR(255);
    DECLARE ak_go_user_ent_subscription_start_var VARCHAR(255);
    DECLARE ak_go_user_ent_uuids_var LONGTEXT;
    DECLARE ak_go_user_first_name_var TEXT;
    DECLARE ak_go_user_last_name_var TEXT;
    DECLARE ak_go_user_link_var VARCHAR(255);
    DECLARE ak_go_user_manually_activated_var VARCHAR(255);
    DECLARE ak_go_user_notes_var TEXT;
    DECLARE ak_go_user_phone_number_var VARCHAR(255);
    DECLARE ak_go_user_position_var VARCHAR(255);
    DECLARE ak_go_user_post_code_var VARCHAR(255);
    DECLARE ak_go_user_promotional_by_post_var VARCHAR(255);
    DECLARE ak_go_user_publisher_id_var VARCHAR(255);
    DECLARE ak_go_user_role_var VARCHAR(255);
    DECLARE ak_go_user_school_name_var VARCHAR(255);
    DECLARE ak_go_user_security_question_var TEXT;
    DECLARE ak_go_user_security_answer_var TEXT;
    DECLARE ak_go_user_session_id_var VARCHAR(255);
    DECLARE ak_go_user_state_var VARCHAR(255);
    DECLARE ak_go_user_subjects_taught_var TEXT;
    DECLARE ak_go_user_subscription_renew_var VARCHAR(255);
    DECLARE ak_go_user_substription_start_var VARCHAR(255);
    DECLARE ak_go_user_suburb_var VARCHAR(255);
    DECLARE ak_go_user_title_var VARCHAR(255);
    DECLARE ak_go_user_uuids_var LONGTEXT;
    DECLARE ak_uName_var TEXT;
    DECLARE ak_uEmail_var TEXT;
    DECLARE ak_uPassword_var TEXT;
    DECLARE ak_uPasswordConfirm_var TEXT;
    DECLARE ak_uSecurityQuestion_var TEXT;
    DECLARE ak_uSecurityAnswer_var TEXT;
    DECLARE ak_uFirstName_var VARCHAR(255);
    DECLARE ak_uLastName_var VARCHAR(255);
    DECLARE ak_uSchoolName_var TEXT;
    DECLARE ak_uPositionType_var TEXT;
    DECLARE ak_uSchoolPhoneNumber_var TEXT;
    DECLARE ak_uSchoolAddress_var TEXT;
    DECLARE ak_uSuburb_var TEXT;
    DECLARE ak_uPostcode_var TEXT;
    DECLARE ak_uCountry_var TEXT;
    DECLARE ak_uSubjectsTaught_var TEXT;
    DECLARE ak_uPMByRegularPost_var TINYINT(4);
    DECLARE orderID_var INT(11);
    DECLARE ak_uAccess_var TEXT;
    DECLARE ak_uActivatedDate_var DATETIME;
    DECLARE ak_uActivationCode_var TEXT;
    DECLARE ak_uAuthToken_var TEXT;
    DECLARE ak_uDeactivatedDate_var DATETIME;
    DECLARE ak_uEntAppID_var TEXT;
    DECLARE ak_uEntAuthToken_var TEXT;
    DECLARE ak_uEntIntegrator_var TINYINT(4);
    DECLARE ak_uEntOffer_var TEXT;
    DECLARE ak_uEntProductID_var TEXT;
    DECLARE ak_uEntSubscriptionRenew_var DATETIME;
    DECLARE ak_uEntSubscriptionStart_var TEXT;
    DECLARE ak_uEntUuID_var TEXT;
    DECLARE ak_uLink_var DECIMAL(14,4);
    DECLARE ak_uManuallyActivated_var TINYINT(4);
    DECLARE ak_uMAStaffID_var DECIMAL(14,4);
    DECLARE ak_uNotes_var TEXT;
    DECLARE ak_uPublisherID_var DECIMAL(14,4);
    DECLARE ak_uSessionID_var TEXT;
    DECLARE ak_uSubscriptionRenew_var DATETIME;
    DECLARE ak_uSubscriptionStart_var DATETIME;
    DECLARE ak_uUuID_var TEXT;
    DECLARE ak_FirstName_var TEXT;
    DECLARE ak_LastName_var TEXT;
    DECLARE ak_uStateUS_var TEXT;
    DECLARE ak_uStateCA_var TEXT;
    DECLARE ak_uStateNZ_var TEXT;
    DECLARE ak_uStateAU_var TEXT;
    DECLARE ak_uState_var TEXT;
    DECLARE ak_billing_first_name_var TEXT;
    DECLARE ak_billing_last_name_var TEXT;
    DECLARE ak_billing_address_address1_var VARCHAR(255);
    DECLARE ak_billing_address_address2_var VARCHAR(255);
    DECLARE ak_billing_address_city_var VARCHAR(255);
    DECLARE ak_billing_address_state_province_var VARCHAR(255);
    DECLARE ak_billing_address_country_var VARCHAR(255);
    DECLARE ak_billing_address_postal_code_var VARCHAR(255);
    DECLARE ak_billing_phone_var TEXT;
    DECLARE ak_shipping_first_name_var TEXT;
    DECLARE ak_shipping_last_name_var TEXT;
    DECLARE ak_shipping_address_address1_var VARCHAR(255);
    DECLARE ak_shipping_address_address2_var VARCHAR(255);
    DECLARE ak_shipping_address_city_var VARCHAR(255);
    DECLARE ak_shipping_address_state_province_var VARCHAR(255);
    DECLARE ak_shipping_address_country_var VARCHAR(255);
    DECLARE ak_shipping_address_postal_code_var VARCHAR(255);
    DECLARE ak_shipping_phone_var TEXT;
    
    /* Custom variables */
    DECLARE ID_loop_var VARCHAR(11);
    DECLARE users_attribute_cursor CURSOR FOR SELECT uID, ak_profile_private_messages_enabled, ak_profile_private_messages_notification_enabled, ak_go_user_access, ak_go_user_activated_date, ak_go_user_activation_code, ak_go_user_address1, ak_go_user_address2, ak_go_user_administrator, ak_go_user_allow_marketing_contact, ak_go_user_auth_token, ak_go_user_country, ak_go_user_deactivated_date, ak_go_user_department, ak_go_user_ent_appid, ak_go_user_ent_auth_token, ak_go_user_ent_integrator, ak_go_user_ent_offer, ak_go_user_ent_productid, ak_go_user_ent_subscription_renew, ak_go_user_ent_subscription_start, ak_go_user_ent_uuids, ak_go_user_first_name, ak_go_user_last_name, ak_go_user_link, ak_go_user_manually_activated, ak_go_user_notes, ak_go_user_phone_number, ak_go_user_position, ak_go_user_post_code, ak_go_user_promotional_by_post, ak_go_user_publisher_id, ak_go_user_role, ak_go_user_school_name, ak_go_user_security_question, ak_go_user_security_answer, ak_go_user_session_id, ak_go_user_state, ak_go_user_subjects_taught, ak_go_user_subscription_renew, ak_go_user_substription_start, ak_go_user_suburb, ak_go_user_title, ak_go_user_uuids, ak_uName, ak_uEmail, ak_uPassword, ak_uPasswordConfirm, ak_uSecurityQuestion, ak_uSecurityAnswer, ak_uFirstName, ak_uLastName, ak_uSchoolName, ak_uPositionType, ak_uSchoolPhoneNumber, ak_uSchoolAddress, ak_uSuburb, ak_uPostcode, ak_uCountry, ak_uSubjectsTaught, ak_uPMByRegularPost, orderID, ak_uAccess, ak_uActivatedDate, ak_uActivationCode, ak_uAuthToken, ak_uDeactivatedDate, ak_uEntAppID, ak_uEntAuthToken, ak_uEntIntegrator, ak_uEntOffer, ak_uEntProductID, ak_uEntSubscriptionRenew, ak_uEntSubscriptionStart, ak_uEntUuID, ak_uLink, ak_uManuallyActivated, ak_uMAStaffID, ak_uNotes, ak_uPublisherID, ak_uSessionID, ak_uSubscriptionRenew, ak_uSubscriptionStart, ak_uUuID, ak_FirstName, ak_LastName, ak_uStateUS, ak_uStateCA, ak_uStateNZ, ak_uStateAU, ak_uState, ak_billing_first_name, ak_billing_last_name, ak_billing_address_address1, ak_billing_address_address2, ak_billing_address_city, ak_billing_address_state_province, ak_billing_address_country, ak_billing_address_postal_code, ak_billing_phone, ak_shipping_first_name, ak_shipping_last_name, ak_shipping_address_address1, ak_shipping_address_address2, ak_shipping_address_city, ak_shipping_address_state_province, ak_shipping_address_country, ak_shipping_address_postal_code, ak_shipping_phone
    FROM UserSearchIndexAttributes WHERE uID IN (SELECT uID FROM UserIdTemporaryTable) ORDER BY uID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN users_attribute_cursor;
        users_attribute_loop: LOOP
            FETCH users_attribute_cursor INTO uID_var, ak_profile_private_messages_enabled_var, ak_profile_private_messages_notification_enabled_var, ak_go_user_access_var, ak_go_user_activated_date_var, ak_go_user_activation_code_var, ak_go_user_address1_var, ak_go_user_address2_var, ak_go_user_administrator_var, ak_go_user_allow_marketing_contact_var, ak_go_user_auth_token_var, ak_go_user_country_var, ak_go_user_deactivated_date_var, ak_go_user_department_var, ak_go_user_ent_appid_var, ak_go_user_ent_auth_token_var, ak_go_user_ent_integrator_var, ak_go_user_ent_offer_var, ak_go_user_ent_productid_var, ak_go_user_ent_subscription_renew_var, ak_go_user_ent_subscription_start_var, ak_go_user_ent_uuids_var, ak_go_user_first_name_var, ak_go_user_last_name_var, ak_go_user_link_var, ak_go_user_manually_activated_var, ak_go_user_notes_var, ak_go_user_phone_number_var, ak_go_user_position_var, ak_go_user_post_code_var, ak_go_user_promotional_by_post_var, ak_go_user_publisher_id_var, ak_go_user_role_var, ak_go_user_school_name_var, ak_go_user_security_question_var, ak_go_user_security_answer_var, ak_go_user_session_id_var, ak_go_user_state_var, ak_go_user_subjects_taught_var, ak_go_user_subscription_renew_var, ak_go_user_substription_start_var, ak_go_user_suburb_var, ak_go_user_title_var, ak_go_user_uuids_var, ak_uName_var, ak_uEmail_var, ak_uPassword_var, ak_uPasswordConfirm_var, ak_uSecurityQuestion_var, ak_uSecurityAnswer_var, ak_uFirstName_var, ak_uLastName_var, ak_uSchoolName_var, ak_uPositionType_var, ak_uSchoolPhoneNumber_var, ak_uSchoolAddress_var, ak_uSuburb_var, ak_uPostcode_var, ak_uCountry_var, ak_uSubjectsTaught_var, ak_uPMByRegularPost_var, orderID_var, ak_uAccess_var, ak_uActivatedDate_var, ak_uActivationCode_var, ak_uAuthToken_var, ak_uDeactivatedDate_var, ak_uEntAppID_var, ak_uEntAuthToken_var, ak_uEntIntegrator_var, ak_uEntOffer_var, ak_uEntProductID_var, ak_uEntSubscriptionRenew_var, ak_uEntSubscriptionStart_var, ak_uEntUuID_var, ak_uLink_var, ak_uManuallyActivated_var, ak_uMAStaffID_var, ak_uNotes_var, ak_uPublisherID_var, ak_uSessionID_var, ak_uSubscriptionRenew_var, ak_uSubscriptionStart_var, ak_uUuID_var, ak_FirstName_var, ak_LastName_var, ak_uStateUS_var, ak_uStateCA_var, ak_uStateNZ_var, ak_uStateAU_var, ak_uState_var, ak_billing_first_name_var, ak_billing_last_name_var, ak_billing_address_address1_var, ak_billing_address_address2_var, ak_billing_address_city_var, ak_billing_address_state_province_var, ak_billing_address_country_var, ak_billing_address_postal_code_var, ak_billing_phone_var, ak_shipping_first_name_var, ak_shipping_last_name_var, ak_shipping_address_address1_var, ak_shipping_address_address2_var, ak_shipping_address_city_var, ak_shipping_address_state_province_var, ak_shipping_address_country_var, ak_shipping_address_postal_code_var, ak_shipping_phone_var;

            IF done 
                THEN LEAVE users_attribute_loop; 
            END IF;
                
            /* Anonymize data here */
            SET ID_loop_var = CAST(uID_var as CHAR(100));
            IF (ak_go_user_address1_var IS NOT NULL OR ak_go_user_address1_var <> '') THEN SET ak_go_user_address1_var = CONCAT('anonymousAddress1_', ID_loop_var); END IF;
            IF (ak_go_user_address2_var IS NOT NULL OR ak_go_user_address2_var <> '') THEN SET ak_go_user_address2_var = CONCAT('anonymousAddress2_', ID_loop_var); END IF;
            IF (ak_go_user_first_name_var IS NOT NULL OR ak_go_user_first_name_var <> '' OR ak_go_user_first_name_var IS NULL) THEN SET ak_go_user_first_name_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_go_user_last_name_var IS NOT NULL OR ak_go_user_last_name_var <> '') THEN SET ak_go_user_last_name_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_go_user_phone_number_var IS NOT NULL OR ak_go_user_phone_number_var <> '') THEN SET ak_go_user_phone_number_var = null; END IF;
            IF (ak_go_user_position_var IS NOT NULL OR ak_go_user_position_var <> '') THEN SET ak_go_user_position_var = CONCAT('anonymousPosition_', ID_loop_var); END IF;
            IF (ak_go_user_security_question_var IS NOT NULL OR ak_go_user_security_question_var <> '') THEN SET ak_go_user_security_question_var = null; END IF;
            IF (ak_go_user_security_answer_var IS NOT NULL OR ak_go_user_security_answer_var <> '') THEN SET ak_go_user_security_answer_var = null; END IF;
            IF (ak_uName_var IS NOT NULL OR ak_uName_var <> '') THEN SET ak_uName_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_uEmail_var IS NOT NULL OR ak_uEmail_var <> '') THEN SET ak_uEmail_var = CONCAT(ID_loop_var, '@anonymous.com'); END IF;
            IF (ak_uSecurityQuestion_var IS NOT NULL OR ak_uSecurityQuestion_var <> '') THEN SET ak_uSecurityQuestion_var = null; END IF;
            IF (ak_uSecurityAnswer_var IS NOT NULL OR ak_uSecurityAnswer_var <> '') THEN SET ak_uSecurityAnswer_var = null; END IF;
            IF (ak_uFirstName_var IS NOT NULL OR ak_uFirstName_var <> '' OR ak_uFirstName_var IS NULL) THEN SET ak_uFirstName_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_uLastName_var IS NOT NULL OR ak_uLastName_var <> '') THEN SET ak_uLastName_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_uPositionType_var IS NOT NULL OR ak_uPositionType_var <> '') THEN SET ak_uPositionType_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_uSchoolPhoneNumber_var IS NOT NULL OR ak_uSchoolPhoneNumber_var <> '') THEN SET ak_uSchoolPhoneNumber_var = null; END IF;
            IF (ak_FirstName_var IS NOT NULL OR ak_FirstName_var <> '' OR ak_FirstName_var IS NULL) THEN SET ak_FirstName_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_LastName_var IS NOT NULL OR ak_LastName_var <> '') THEN SET ak_LastName_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_billing_first_name_var IS NOT NULL OR ak_billing_first_name_var <> '') THEN SET ak_billing_first_name_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_billing_last_name_var IS NOT NULL OR ak_billing_last_name_var <> '') THEN SET ak_billing_last_name_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_billing_address_address1_var IS NOT NULL OR ak_billing_address_address1_var <> '') THEN SET ak_billing_address_address1_var = CONCAT('anonymousBillAddress1_', ID_loop_var); END IF;
            IF (ak_billing_address_address2_var IS NOT NULL OR ak_billing_address_address2_var <> '') THEN SET ak_billing_address_address2_var = CONCAT('anonymousBillAddress2_', ID_loop_var); END IF;
            IF (ak_billing_phone_var IS NOT NULL OR ak_billing_phone_var <> '') THEN SET ak_billing_phone_var = null; END IF;
            IF (ak_shipping_first_name_var IS NOT NULL OR ak_shipping_first_name_var <> '') THEN SET ak_shipping_first_name_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_shipping_last_name_var IS NOT NULL OR ak_shipping_last_name_var <> '') THEN SET ak_shipping_last_name_var = CONCAT('Anonymous_', ID_loop_var); END IF;
            IF (ak_shipping_address_address1_var IS NOT NULL OR ak_shipping_address_address1_var <> '') THEN SET ak_shipping_address_address1_var = CONCAT('anonymousShipAddress1_', ID_loop_var); END IF;
            IF (ak_shipping_address_address2_var IS NOT NULL OR ak_shipping_address_address2_var <> '') THEN SET ak_shipping_address_address2_var = CONCAT('anonymousShipAddress2_', ID_loop_var); END IF;
            IF (ak_shipping_phone_var IS NOT NULL OR ak_shipping_phone_var <> '') THEN SET ak_shipping_phone_var = null; END IF;
            
            
            /* Update data to anonymize the original table */
            UPDATE `UserSearchIndexAttributes`
            SET `ak_go_user_address1` = ak_go_user_address1_var,
            `ak_go_user_address2` = ak_go_user_address2_var,
            `ak_go_user_first_name` = ak_go_user_first_name_var,
            `ak_go_user_last_name` = ak_go_user_last_name_var,
            `ak_go_user_phone_number` = ak_go_user_phone_number_var,
            `ak_go_user_position` = ak_go_user_position_var,
            `ak_go_user_security_question` = ak_go_user_security_question_var,
            `ak_go_user_security_answer` = ak_go_user_security_answer_var,
            `ak_uName` = ak_uName_var,
            `ak_uEmail` = ak_uEmail_var,
            `ak_uSecurityQuestion` = ak_uSecurityQuestion_var,
            `ak_uSecurityAnswer` = ak_uSecurityAnswer_var,
            `ak_uFirstName` = ak_uFirstName_var,
            `ak_uLastName` = ak_uLastName_var,
            `ak_uPositionType` = ak_uPositionType_var,
            `ak_uSchoolPhoneNumber` = ak_uSchoolPhoneNumber_var,
            `ak_FirstName` = ak_FirstName_var,
            `ak_LastName` = ak_LastName_var,
            `ak_billing_first_name` = ak_billing_first_name_var,
            `ak_billing_last_name` = ak_billing_last_name_var,
            `ak_billing_address_address1` = ak_billing_address_address1_var,
            `ak_billing_address_address2` = ak_billing_address_address2_var,
            `ak_billing_phone` = ak_billing_phone_var,
            `ak_shipping_first_name` = ak_shipping_first_name_var,
            `ak_shipping_last_name` = ak_shipping_last_name_var,
            `ak_shipping_address_address1` = ak_shipping_address_address1_var,
            `ak_shipping_address_address2` = ak_shipping_address_address2_var,
            `ak_shipping_phone` = ak_shipping_phone_var
            WHERE uID = uID_var;
            
        END LOOP;
    CLOSE users_attribute_cursor;
    
    SELECT COUNT(*) FROM UserSearchIndexAttributes LIMIT 1; /* To eliminate the warning 'zero rows fetched ...' */  

END$$

DELIMITER ;