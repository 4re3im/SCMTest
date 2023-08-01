<?php 
/**
 * Model for Provisioning 
 * @author Ariel Tabag <atabag@cambridge.org>
 * ANZGO-3764 tagged by jbernardez 20181011
 * tagged for deletion, not sure if we are still using cup_go package or the models inside this
 */
class Provision extends Model{

    var $staff_id; var $db;
    var $file_location = "";
    var $provision_code_tbl = "Provision_Codes";
    var $provision_files_tbl = "Provision_Files";
    
    public function __construct()
    {
        $u = new User();
        $this->staff_id = $u->getUserID();
        $this->db = Loader::db();
    }
    
    public function ProcessExcel($user_only='')
    {

        $file_id = $this->uploadProvisionFile();

        if ($file_id) {
            $_SESSION['file_id'] = $file_id;
            $values = $this->extractExcel($file_id, $this->file_location);
            
            if (!is_array($values)) {
                $this->insertProvision($values);
                $this->processUser($file_id, $user_only);              

                if (!$user_only) {
                    $this->processAccessCode($file_id);
                }

                $provisions = $this->getAllProvision($file_id, $user_only);
                
                return $provisions;
            }
            
            return $values;
        }
        
        return false;
    }
    
    public function processApiRequest($email, $isbn)
    {
        if (!$user = $this->getUserByEmail($email)) {
            $Oauth->insertAuthLogs($key,'addProductToUser','user not found',$isbn,$email);
            return "user not found";
        }
        
        if (!$subscription =  $this->getSubscriptionByISBN($isbn)) {
            $Oauth->insertAuthLogs($key,'addProductToUser','product not found',$isbn,$email);
            return "product not found";
        }
        
        $user_subscription = $this->insertUserSubscription(
            $user['uID'],
            $subscription['S_ID'],
            $subscription['SA_ID'],
            'api'
        );
        
        if ($user_subscription) {
            $Oauth->insertAuthLogs($key,'addProductToUser','success',$isbn,$email);
            return "success";
        }
    }
            
    public function processApiDeActivateRequest($email, $isbn, $key)
    {
        $Oauth = new Oauth();
        
        if (!$user = $this->getUserByEmail($email)) {
            $Oauth->insertAuthLogs($key,'deactivateProductOnUser','user not found',$isbn,$email);
            return "user not found";
        }
        
        if (!$subscription =  $this->getSubscriptionByISBN($isbn)) {
            $Oauth->insertAuthLogs($key,'deactivateProductOnUser','product not found',$isbn,$email);
            return "product not found";
        }
        
        $user_subscription = $this->deActivateUserSubscription(
            $user['uID'],
            $subscription['S_ID'],
            $subscription['SA_ID']
        );
        
        if ($user_subscription) {
            $Oauth->insertAuthLogs($key,'deactivateProductOnUser','success',$isbn,$email);
            return "success";
        }
    }
    
    public function addUserSubscription($user_id, $s_id, $sa_id)
    {
        $user_ids = explode('|', $user_id);
        $duplicate_arr = array();
        
        if (is_array) {
            foreach($user_ids as $uid) {
                $this->insertUserSubscription($uid, $s_id, $sa_id);
            }
        } else {
            $this->insertUserSubscription($uid, $s_id, $sa_id);
        }
        return array('provisions' => $this->getAllProvision(
            $_SESSION['file_id']),
            'user_ids' => $user_ids,
            'dupes'=>$duplicate_arr
        );
    }

    /**
     * Store provision data to table from excel
     */
    public function insertProvision($values)
    {
        //insert data to Go.Provision_Codes table
        $sql = "INSERT INTO $this->provision_code_tbl VALUES $values";
        return ($this->db->Execute($sql)) ? true : false;
    }
    
    /**
     * insert user subscription
     */
    public function insertUserSubscription($user_id, $s_id, $sa_id)
    {
        $check_sql = "SELECT * FROM CupGoUserSubscription WHERE (UserID = ? AND SA_ID = ?) AND PurchaseType = 'PROVISION'";
        $result = $this->db->GetAll($check_sql, array($user_id,$sa_id));

        if (count($result) <= 0) {
            $sql  = "INSERT INTO CupGoUserSubscription(UserID,SA_ID,S_ID,StartDate,EndDate,Duration,Active,PurchaseType) ";
            $sql .= "(SELECT ?,ID,S_ID,StartDate,EndDate,Duration,'Y','PROVISION' FROM CupGoSubscriptionAvailability WHERE ID = ?)";
            $this->db->Execute($sql, array($user_id, $sa_id));
            $u_sub_id = $this->db->Insert_ID();

            $sql  = "INSERT INTO CupGoTabAccess(UserID,TabID,S_ID,SA_ID,Active,US_ID) ";
            // ANZGO-3764 modified by jbernardez 20181011
            $sql .= "(SELECT ?,TabID,S_ID,?,'Y',? from CupGoSubscriptionTabs where S_ID=?)";

            $this->db->Execute($sql, array($user_id, $sa_id, $u_sub_id, $s_id));
            $this->insertProvisionByUserSubscription($user_id, $u_sub_id);

            return true;
        } else {
            return FALSE;
        }
    }
     
    /**
     * Store provision from rpoduct
     */
    public function insertProvisionByUserSubscription($user_id, $us_id)
    {
        $pf_id = $_SESSION['file_id'];
        
        //insert data to Go.Provision_Codes table
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "INSERT INTO $this->provision_code_tbl(PFID, UserID, USID) VALUES(?,?,?)";

        if ($this->db->Execute($sql, array($pf_id, $user_id, $us_id))) {
            return true;
        } else {
            return false;
        }
    }    

    /**
     * insert user subscription
     */
    public function insertUserSubscriptionByAccessCode($user_id, $s_id, $sa_id,$access_code)
    {
        $sql  = "INSERT INTO CupGoUserSubscription(UserID,SA_ID,S_ID,StartDate,EndDate,Duration,Active,AccessCode,PurchaseType) ";
        // ANZGO-3764 modified by jbernardez 20181011
        $sql .= "(SELECT ?,ID,S_ID,StartDate,EndDate,Duration,'Y',?, 'PROVISION' FROM CupGoSubscriptionAvailability WHERE ID=?)";
        $this->db->Execute($sql, array($user_id, $access_code, $sa_id));

        $us_id = $this->db->Insert_ID();

        $sql  = "INSERT INTO CupGoTabAccess(UserID,TabID,S_ID,SA_ID,Active,US_ID) ";
        // ANZGO-3764 modified by jbernardez 20181011
        $sql .= "(SELECT ?,TabID,S_ID,?,'Y',? FROM CupGoSubscriptionTabs WHERE S_ID=?)";
        $this->db->Execute($sql, array($user_id, $sa_id, $us_id, $s_id));

        $sql  = "UPDATE CupGoAccessCodes SET Usable='N' WHERE AccessCode=?";
        $this->db->Execute($sql, array($access_code));

        return $us_id;
    }
    
    /**
     * user subscription availability search
     **/
    public function getAllProductByname($search)
    {
        $sql  = "SELECT CONCAT( IFNULL(s.CMS_Name,''), ' : ', IFNULL(s.Name,''), ' : ', ";
        $sql .= "IFNULL(sa.Description,''), ' / ', IFNULL(cast(s.ISBN_13 as char(13)),''), ' (', ";
        $sql .= "IFNULL((SELECT CASE WHEN(sa.Type='duration' AND sa.Duration=0) then 'perpetual' ";
        $sql .= "WHEN(sa.Type='duration' AND sa.duration>0) THEN CONCAT(cast(sa.Duration as char(10)), ' days') ";
        $sql .= "WHEN(sa.Type='start-end') THEN CONCAT(cast(sa.StartDate as char(25)), ' to ', CAST(sa.EndDate as char(25))) ";
        $sql .= "ELSE 'school year' end),''), IFNULL((SELECT CASE WHEN(sa.Demo='Y') THEN ' / Demo' end),''), ')') AS 'search_result', ";
        $sql .= "sa.ID as 'sa_id', s.ID as 's_id', CONCAT(s.ID,'_',sa.ID) as search_id from CupGoSubscriptionAvailability sa, CupGoSubscription s ";
        // ANZGO-3764 modified by jbernardez 20181011
        $sql .= "WHERE sa.S_ID = s.id and sa.Active='Y' AND (s.CMS_Name LIKE ? OR s.ISBN_13 LIKE ?) ORDER BY 1 DESC";

        $products = $this->db->getAll($sql, array("%$search%", "%$search%"));

        return $products;
    }
    
    public function getAccessCodes($file_id)
    {
        $PHPFunction = new PHPFunction();
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "SELECT ProAccessCode FROM $this->provision_code_tbl WHERE PFID=? AND AccessCodeUsable='Y'";

        if (!function_exists('array_column')) {
            $columns = $PHPFunction->array_column($this->db->GetAll($sql, array($file_id)), 'AccessCode');
        } else {
            $columns = array_column($this->db->GetAll($sql, array($file_id)), 'AccessCode');
        }

        return "('" . implode("','", $columns) . "')";
    }
    
    /**
     * get all processed accesscodes
     */
    public function getAllProvision($file_id, $user_only='')
    {
        if ($user_only=='checked') {
            $sql  = "SELECT *, '' as ProductName, '' as USProductName ";
            $sql .= "FROM Provision_Files p LEFT JOIN Provision_Codes pc ON p.ID=PFID ";
            // ANZGO-3764 modified by jbernardez 20181011
            $sql .= "WHERE PFID=? AND ProAccessCode IS NOT NULL";

            $provisions = $this->db->getAll($sql, array($file_id));
        } else {
            $inner_sql = "SELECT CMS_Name FROM CupGoAccessCodes a ";
            $inner_sql .= "LEFT JOIN CupGoSubscriptionAvailability sa ON a.SA_ID=sa.ID ";
            $inner_sql .= "LEFT JOIN CupGoSubscription s ON sa.S_ID=s.ID WHERE a.AccessCode=ProAccessCode ";
            $inner_sql2 = "SELECT GROUP_CONCAT((CMS_Name) SEPARATOR '|') FROM CupGoUserSubscription us ";
            $inner_sql2 .= "LEFT JOIN Provision_Codes p ON us.ID=USID LEFT JOIN CupGoSubscription s ";
            // ANZGO-3764 modified by jbernardez 20181011
            $inner_sql2 .= "ON us.S_ID=s.ID WHERE ProAccessCode IS NULL AND us.UserID=pc.UserID AND PFID=?";
            $sql  = "SELECT *, (?) as ProductName, (?) as USProductName ";
            $sql .= "FROM Provision_Files p LEFT JOIN Provision_Codes pc ON p.ID=PFID ";
            $sql .= "WHERE PFID=? AND ProAccessCode IS NOT NULL";

            $provisions = $this->db->getAll($sql, array($file_id, $inner_sql, $inner_sql2, $file_id));
        }
        
        return $provisions;
    }
    
    public function getUser($id)
    {
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "SELECT ak_uFirstname, ak_uLastname, uName, uPassword, uDateAdded FROM Users JOIN UserSearchIndexAttributes WHERE uID=?";

        return $this->db->getRow($sql, array($id));
    }
    
    
    public function updateProvision($p_id, $us_id)
    {
        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "UPDATE $this->provision_code_tbl SET USID=? WHERE ID=?";

        return $this->db->Execute($sql, array($us_id, $p_id));
    }
    
    // Unused
    public function verifyAccount($params)
    {
        $user_id = $params['user_id'];
        $password = $this->encryptPassword($params['password']);
        $allow_marketing_contact = $params['allow_marketing_contact'];

        // ANZGO-3764 modified by jbernardez 20181011
        $sql = "UPDATE User SET Password=?, AllowMarketingContact=?, Active='Y', ActivatedDate=now() WHERE ID=?";

        return $this->db->Execute($sql, array($password, $allow_marketing_contact, $user_id));
    }


    /**
     * Encrypt password
     */
    private function encryptPassword($password)
    {
        $returnpassword = $password."ZX3D56";
        $returnpassword = md5($returnpassword);

        return $returnpassword;
    }    
    
    /**
     * get the content of the excel file
    */
    private function extractExcel($file_id, $file)
    {
        $array_rows_lookup = array(
            'null',
            'email',
            'password',
            'first name',
            'last name',
            'access code',
            'school',
            'state',
            'post code',
            'role'
        );

        $ExcelReader =  new ExcelReader($file, false);
        $row_count = $ExcelReader->rowcount();
        $string = ''; $warning_message = ''; 
        
        for ($row = 2; $row <= $row_count; $row++) {
            $empty_rows = '';
            
            if ($ExcelReader->val($row,'a')=='' && $ExcelReader->val($row,'c')=='') {
                continue;
            }
            
            for ($col = 1; $col <= $ExcelReader->colcount(); $col++) {
                $val = $ExcelReader->val($row,$col);
                
                if ($val!='') {
                    $col_val = $ExcelReader->val($row,$col);
                    
                    if (in_array($col, array(3,4,6))) {
                        $col_val = addslashes($col_val);
                    }
                    
                    $arr[$row][$col] = trim($col == 2 ? $this->encryptPassword($col_val) : $col_val);
                    $valid_row = $row;
                } else {
                    $empty_rows .= $empty_rows != '' ? ',' : '';
                    $empty_rows .= $array_rows_lookup[$col];
                }
            }
            
            if ($empty_rows) {
                $warning_message .= "<li>$empty_rows in row $row of " . $ExcelReader->val($row,'c') ."&nbsp;";
                $warning_message .= $ExcelReader->val($row,'d') ." (" . $ExcelReader->val($row,'a') . ") </li>";
            } elseif ($warning_message=='') {
                if ($row !=2) {
                    $string .= ',';
                }

                // ANZGO-1559
                // check if user it student or teacher
                // then push value to array
                $userTypeID = 1;
                if (strtolower($arr[$valid_row][9]) == 'student') {
                    $userTypeID = 1;
                } elseif (strtolower($arr[$valid_row][9]) == 'teacher') {
                    $userTypeID = 2;
                }

                array_push($arr[$valid_row], $userTypeID);
                // END ANZGO-1559
                
                if ($userResult = $this->getUserID($arr[$valid_row][1])) {
                    $string .= "('NULL', 'NULL'," . $userResult['UserID'] . ", $file_id, '".implode("', '", $arr[$valid_row])."', 'N', 'N')";
                } else {
                    $string .= "('NULL', 'NULL', 'NULL', $file_id, '".implode("', '", $arr[$valid_row])."', 'N', 'N')";
                }
            }
        }

        if ($warning_message) {
            return array('warning' => "The following are required, 
                                        please check your file and upload again<br><br><ul>$warning_message</ul>");
        }
        
        return $string;
    }
    
    private function getClientIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                return $_SERVER["HTTP_X_FORWARDED_FOR"];
            }

            if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                return $_SERVER["HTTP_CLIENT_IP"];
            }

            return $_SERVER["REMOTE_ADDR"];
        }

        if (getenv('HTTP_X_FORWARDED_FOR')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        }

        if (getenv('HTTP_CLIENT_IP')) {
            return getenv('HTTP_CLIENT_IP');
        }

        return getenv('REMOTE_ADDR');
    }
    
    /*
     * Verify if user exists
     */
    private function processUser($file_id, $user_only='')
    {
        $sql = "UPDATE $this->provision_code_tbl SET UserExists='Y' WHERE EmailAddress IN (SELECT uEmail FROM Users) AND PFID=$file_id";
        
        if ($this->db->Execute($sql)) {
            
            $active = 'N';
            $activated_date='NULL';
            $manually_activated = 'NULL';
            $manually_activated_staff_id = 'NULL';
            $notes = 'NULL';
            
            if ($user_only) {
                $active = 'Y';
                $activated_date = 'now()';
                $manually_activated = 'Y';
                $manually_activated_staff_id = $this->staff_id;
                $notes = 'Provisioning';
            }
            
            $ids = $this->createC5Users($file_id);
            
            foreach ($ids as $key => $value) {
                // ANZGO-3764 modified by jbernardez 20181011
                $sql1 = "UPDATE $this->provision_code_tbl SET UserID=$key WHERE PFID = ? AND EmailAddress = ?";
                $this->db->Execute($sql1, array($file_id, $value));
            }

            return true;
        }

        return false;
    }
    
    /*
     * Verify Access Codes
     */
    private function processAccessCode($file_id)
    {
        // ANZGO-3764 modified by jbernardez 20181011
        $sql  = "UPDATE $this->provision_code_tbl SET AccessCodeUsable='Y' WHERE PFID = ? AND ";
        $sql .= "ProAccessCode IN (SELECT AccessCode FROM CupGoAccessCodes WHERE UserID IS NULL AND Usable='Y')";

        if ($this->db->Execute($sql, array($file_id))) {
            $ip = $this->getClientIP();
            $accesscodes = $this->getAccessCodes($file_id);
            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            //update access codes that available
            //// ANZGO-3764 modified by jbernardez 20181011
            $sql  = "UPDATE CupGoAccessCodes a SET DateActivated=now(), UserAgent=?, ";
            $sql .= "IPAddress=?, Active='Y', UsageCount=UsageCount+1, StaffID=?, ";
            $sql .= "UserID=(SELECT Users.uID FROM Users LEFT JOIN $this->provision_code_tbl pc ON Users.uEmail=EmailAddress ";
            $sql .= "WHERE ProAccessCode=AccessCode AND PFID=?) WHERE AccessCode IN ?";

            $this->db->Execute($sql, array($user_agent, $ip, $this->staff_id, $file_id, implode("','", $accesscodes)));

            //select all codes for User Subscription
            $sql  = "SELECT pc.ID as P_ID, pc.UserID, S_ID, SA_ID, ProAccessCode as AccessCode FROM $this->provision_code_tbl ";
            $sql .= "as pc LEFT JOIN CupGoAccessCodes as a ON a.AccessCode=ProAccessCode LEFT JOIN CupGoSubscriptionAvailability ";
            $sql .= "as sa ON a.SA_ID=sa.ID WHERE PFID=? AND AccesscodeUsable='Y'";

            $provisions = $this->db->getAll($sql, array($file_id));

            foreach ($provisions as $provision) {
                $us_id = $this->insertUserSubscriptionByAccessCode($provision['UserID'], $provision['S_ID'], $provision['SA_ID'], $provision['AccessCode']);
                $this->updateProvision($provision['P_ID'], $us_id);
            }
            
            return true;
        }
        
        return false;
    }
    
    public function sendEmail($file_id)
    {
        $msg  = "Thank you for registering with Cambridge GO.\n\n";
        $msg .= "Your account has been created.\n\n";
        $msg .= "To activate your account, open the following link:\n\n";
        $msg .= BASE_URL.DIR_REL ."/provision/verification/";
        
        $msg2 = "If you are unable to click the link, simply copy and paste it into your web browser.\n\n";
        $msg2 .= "The Cambridge GO team.\n\n";
        $msg2 .= "**************************************************\n\n";
        $msg2 .= "This is an automated email generated by the\n\n";
        $msg2 .= "Education website for Cambridge University Press\n\n";
        $msg2 .= "Australia and New Zealand. Please do not reply to this email\n\n";
        $msg2 .= "If you have any questions, contact Customer\n\n";
        $msg2 .= "Service via email enquiries@cambridge.edu.au or\n\n";
        $msg2 .= "phone +61 (0)3 8671 1400.\n\n";
        $msg2 .= "(FreePhone within Australia 1800 005 210,\n\n";
        $msg2 .= "or if within New Zealand 0800 023 520)\n\n";
        $msg2 .= "**************************************************\n\n";

        $subject = "Cambridge GO Account Confirmation";

        $from = "go@cambridge.ed u.au";
        //$from = "success@simulator.amazonses.com";

        $mh = Loader::helper('mail');

        // ANZGO-3764 modified by jbernardez 20181011
        $emails = $this->db->getAll("SELECT UserID, EmailAddress, FirstName, LastName FROM 
                                    $this->provision_code_tbl WHERE PFID=? AND UserExists='N'", array($file_id));

        foreach ($emails as $email) {
            $mh->setSubject($subject);
            $salutation = "Hi " . $email['FirstName'] . "\n\n";
            $msg_id = str_replace('/', '-',EncryptionHelper::encrypt($email['UserID']))."\n\n";
            $mh->setBody($salutation.$msg.$msg_id.$msg2);
            $mh->to($email['EmailAddress']);
            $mh->from($from, "Cambridge GO Website");
            $mh->sendMail();
        }
        
        return;
    }
    
    private function uploadProvisionFile()
    {
        //upload file
        Loader::library("file/importer");
        $fi = new FileImporter();
        $path_to_file = $_FILES[0]['tmp_name'];
        $filename = $_FILES[0]['name'];
        $newFile = $fi->import($path_to_file, $filename);

        $f = $newFile->getFile();
        $this->file_location = $f->getPath();

        // ANZGO-3764 modified by jbernardez 20181011
        //insert record of the file
        $sql  = "INSERT INTO $this->provision_files_tbl (StaffID, File) VALUES (?,?)";

        if ($this->db->Execute($sql, array($this->staff_id, $this->file_location))) {
            return $this->db->Insert_ID();
        }

        return false;
    }
    
    private function createC5Users($file_id)
    {
        $uID_arr = array();
        
        // Get User details from Provision Codes table
        $sql = "SELECT * FROM Provision_Codes WHERE UserExists = 'N' and PFID = ?";
        $result = $this->db->GetAll($sql, array($file_id));
                
        foreach ($result as $r) {
            // Setup needed details
            $groupType = ($r['UserTypeID'] == 1 ? 'Student' : 'Teacher');
            $pr_contact_attribs = array(
                'uFirstName' => $r['FirstName'],
                'uLastName' => $r['LastName'],
                'uSchoolName' => $r['School'],
                'uState' => $r['State'],
                'uPostcode' => $r['Postcode'],
                'uPositionType' => $groupType,
                'uPositionTitle' => $r['Role']
            );

            $c5_user = array(
                'uName' => $r['EmailAddress'],
                'uEmail' => $r['EmailAddress'],
                'uPassword' => $r['Password'],
                'uPasswordConfirm' => $r['Password']
            );

            // Register user
            $ui = UserInfo::register($c5_user);

            // Get group type
            $g = Group::getByName($groupType);

            // Instantiate User object from UserInfo
            $u = $ui->getUserObject();

            // Add user to group
            $u->enterGroup($g);

            // New UserInfo Object using $u
            $nUi = UserInfo::getByID($u->getUserID());
            
            $uID_arr[$u->getUserID()] = $r['EmailAddress'];
           
            // Set user attributes
            $contact_attribs = AttributeSet::getByHandle('uContactDetails');

            foreach ($contact_attribs->getAttributeKeys() as $ca) {
                if (in_array($ca->getAttributeKeyHandle(), array_keys($pr_contact_attribs))) {
                    $nUi->setAttribute($ca->getAttributeKeyHandle(), $pr_contact_attribs[$ca->getAttributeKeyHandle()]);
                }
            }

            if ($groupType == 'Teacher') {
                $teacher_attribs = AttributeSet::getByHandle('uTeacherContactDetails');
                foreach ($teacher_attribs->getAttributeKeys() as $ta) {
                    if (in_array($ta->getAttributeKeyHandle(), array_keys($pr_contact_attribs))) {
                        $nUi->setAttribute($ta->getAttributeKeyHandle(), $pr_contact_attribs[$ta->getAttributeKeyHandle()]);
                    }
                }
            }
            
        }
        
        return $uID_arr;
    }
    
    public function getUserID($email)
    {
        $sql = "SELECT UserID FROM Provision_Codes WHERE EmailAddress = ?";
        $result = $this->db->GetRow($sql, array($email));
        return $result;
    }
}