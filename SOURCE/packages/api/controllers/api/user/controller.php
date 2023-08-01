<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../base_controller.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../restApi_interface.php";

Loader::library('gigya/GigyaAccount');

class APIUserController extends Controller
{
    const UNAVAILABLE_URI = '/api/default/unavailable';

    public function __construct()
    {
        header('Content-Type: application/json');

        $request = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if (strpos($request, 'checkAccessCode') !== false) {
            $this->authenticate = false;
        }

        parent::__construct();
    }

    public function create($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function read($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function update($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function delete($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function infoUpdate()
    {
        header('Content-Type: application/json');

        // This should be replaced with your actual Partner Secret, or if signing using a User Key, the secret for that key.
        // $SIGNINGKEYSECRET = GIGYA_SECRET_KEY;
         
        // PHP converts the header into all UPPERCASE, converts hyphens("-") into underscores("_") and prepends "HTTP_"
        // or "HTTPS_" to the front, based on the protocol used.
        // Keep this in mind if converting this code to work in a language other than PHP. 
        // The actual header passed by Gigya is: "X-Gigya-Sig-Hmac-Sha1".
        $msgHash = $_SERVER['HTTP_X_GIGYA_SIG_HMAC_SHA1']; // How PHP sees the X-Gigya-Sig-Hmac-Sha1 header
         
        // Get the JSON payload sent by Gigya.
        $messageJSON = file_get_contents('php://input');
         
        // Decode the JSON payload into an associative array.
        $jsonDecoded = json_decode($messageJSON, true);

        // Check if the hash matches. If it doesn't, it could mean that the data was tampered
        // with in flight. If so, do not send 2XX SUCCESS - let Gigya re-send the notification.
        if ($this->hashesMatch($this->createMessageHash(GIGYA_SECRET_KEY, $messageJSON), $msgHash)) {
          
            // Loop through the events portion of the notification.
            for ($x = 0; $x < sizeof($jsonDecoded['events']); $x++ ) {
            
                $eventType = $jsonDecoded['events'][$x]['type'];
                $uID = $jsonDecoded['events'][$x]['data']['uid'];
             
                /***************************************************************
                ** This is where we would normally do something with this info.
                ** For the sake of this example though, we'll just output
                ** the info to the screen.
                ***************************************************************/

                if ($eventType === 'accountUpdated') {
                    $gigyaAccount = new GigyaAccount($uID);
                    $account = $gigyaAccount->getAccountInfo();
                    
                    $result = $this->updateUser(
                        $account->getSystemID(),
                        $account->getFirstName(),
                        $account->getLastName(),
                        $account->getSchoolName()
                    );
                }
            }

            if ($result) {
                $record = $account->getSystemID() . ': ' . $account->getFirstName() . ' ' . $account->getLastName();
                CupGoLogs::trackUser('Gigya User Update', $record);
                echo json_encode('User updated');
                exit;
            } else {
                echo json_encode('There was an error on your user update');
                exit;
            }
        } else {
            echo json_encode('There was an error on your user update');
            exit;
        }
    }

    private function updateUser($id, $firstName, $lastName, $school)
    {
        $ui = UserInfo::getByID($id);

        if (is_null($ui)) {
            return false;
        }

        $ui->setAttribute('uFirstName', $firstName);
        $ui->setAttribute('uLastName', $lastName);
        $ui->setAttribute('uSchoolName', $school);

        return true;
    }

    // Builds and returns expected hash
    private function createMessageHash($secret, $message)
    {
        return base64_encode(hash_hmac('sha1', $message, base64_decode($secret), true));
    }
 
 
    // Compares the two parameters (in this case the hashes) and returns TRUE if they match
    // and FALSE if they don't.  
    private function hashesMatch($expected, $received)
    {
        return $expected === $received;
    }
}
