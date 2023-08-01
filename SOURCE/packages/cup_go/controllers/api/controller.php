<?php
/**
 * Provision Controller
 * @author Ariel Tabag <atabag@cambridge.org>
 * Last Edited : October 22, 2014
 */

//http://www.androidhive.info/2014/01/how-to-create-rest-api-for-android-app-using-php-slim-and-mysql-day-23/
//https://github.com/bshaffer/oauth2-server-php/tree/v0.9
//http://svn.cup.cam.ac.uk/repos/OnlineContent/anz-c5/go/branches/ANZGO-1705

defined('C5_EXECUTE') or die(_('Access Denied.'));

class ApiController extends Controller 
{   
    // declare global variable
    protected $_params = array();
    protected $pkgHandle = 'cup_go';
    protected $app = '';
    protected $dsn = '';
    protected $username = '';
    protected $password = '';
    
    public function on_start() 
    {
        parent::on_start();
        // controls the actual web service
        Loader::library('Slim/Slim', $this->pkgHandle);
        Loader::library('OAuth2/Autoloader', $this->pkgHandle);
        
        OAuth2_Autoloader::register();
        \Slim\Slim::registerAutoloader(); 
        
        $this->app = new \Slim\Slim();
        // db credentials
        $this->dsn = "mysql:dbname=".DB_DATABASE.";host=".DB_SERVER;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
    }
    
    // to temporarily handle concrete 5 url request
    public function view($email='', $isbn='') 
    {
        $app = \Slim\Slim::getInstance();

        /*
        * insert new user subscription
        * example: curl -i --data "email=atabag@cambridge.org&isbn=36" http://localhost:8080/go_c5/index.php/api
        */
        $app->post('/api', function() use ($app) {
            $Provision = new Provision();
            $Oauth = new Oauth();
            $allPostVars = $app->request()->post();
            $email = $allPostVars['email'];
            $isbn = $allPostVars['isbn'];
            $key = $allPostVars['key'];
            
            // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
            $storage = new OAuth2_Storage_Pdo(array('dsn' => $this->dsn, 'username' => $this->username, 'password' => $this->password));

            // create your server again
            $server = new OAuth2_Server($storage);

            // Handle a request for an OAuth2.0 Access Token and send the response to the client
            if (!$server->verifyResourceRequest(OAuth2_Request::createFromGlobals(), new OAuth2_Response())) {
                
                //$server->getResponse()->send();
                //send email
                $Oauth->insertAuthLogs($key,'addProductToUser','invalid WS Key',$isbn,$email);
                 
                die;
            }

            // $Oauth->insertAuthLogs($key,'addProductToUser','success',$isbn,$email);
            echo json_encode(array($Provision->processApiRequest($email, $isbn, $key)));
        }); 
        
        /*
         * delete user subscription
         * example: curl -i --data "email=atabag@cambridge.org&isbn=36" http://localhost:8080/go_c5/index.php/api/deactivateProduct
         */
        $app->put('/api/:email/:isbn', function($email, $isbn) use ($app) {
            $Provision = new Provision(); 
            $Oauth = new Oauth();
            $allPostVars = $app->request()->post();
            $key = $allPostVars['key'];
            
            // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
            $storage = new OAuth2_Storage_Pdo(array('dsn' => $this->dsn, 'username' => $this->username, 'password' => $this->password));
            // create your server again
            $server = new OAuth2_Server($storage);

            // Handle a request for an OAuth2.0 Access Token and send the response to the client
            if (!$server->verifyResourceRequest(OAuth2_Request::createFromGlobals(), new OAuth2_Response())) {
                // $server->getResponse()->send();
                // send email
                $Oauth->insertAuthLogs($key,'deactivateProductOnUser','invalid WS Key',$isbn,$email);
                
                die;
            }
            
            // $Oauth->insertAuthLogs($key,'deactivateProductOnUser','success',$isbn,$email);
            echo json_encode($Provision->processApiDeActivateRequest($email, $isbn, $key));
        });
                
        $app->run();
        exit;
    }
    
    public function token()
    {
        $app = \Slim\Slim::getInstance();
        
        /**
         * Generate one time token
         * curl -u testclient:testpass http://localhost:8080/go_c5/index.php/api/token -d 'grant_type=client_credentials'
         */
        $app->post('/api/token', function() use ($app) {
            // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
            $storage = new OAuth2_Storage_Pdo(array('dsn' => $this->dsn, 'username' => $this->username, 'password' => $this->password));
            // Pass a storage object or array of storage objects to the OAuth2 server class
            $server = new OAuth2_Server($storage);
            // Add the "Client Credentials" grant type (it is the simplest of the grant types)
            $server->addGrantType(new OAuth2_GrantType_ClientCredentials($storage));
            // Handle a request for an OAuth2.0 Access Token and send the response to the client
            $server->handleTokenRequest(OAuth2_Request::createFromGlobals(), new OAuth2_Response())->send();
        });

        $app->run();
        exit;
    }
    
    // http://localhost:8080/go_c5/index.php/api/linkExternalUser
    // http://tng.cambridge.edu.au/index.php/api/linkExternalUser
    // authorizationToken=13f30ae7-9ebf-33d8-a920-1fcfdd0d557d&userId=373907&externalId=12&tokenExpiryDate=1442215792000&brandCodes=["HOTMATHS","EMACS"]
    public function linkExternalUser()
    {
        $app = \Slim\Slim::getInstance();
        $app->post('/api/linkExternalUser/', function() use ($app) {
            $ExternalUser = new CupGoExternalUser(); 
            $params = $app->request()->post();
            
            echo json_encode($ExternalUser->processExternalUser($params));
        });
        
        $app->run();
        exit;
    }

    // SB-9 added by jbernardez 20191106
    public function removeExternalUser($authorizationToken, $brandCode)
    {
        $app = \Slim\Slim::getInstance();
        $app->delete(
            '/api/removeExternalUser/:authorizationToken/:brandCode',
            function($authorizationToken, $brandCode) use ($app) {
            $externalUser = new CupGoExternalUser(); 
            
            echo json_encode($externalUser->removeExternalUser($authorizationToken, $brandCode));
        });
        
        $app->run();
        exit;
    }
    
    // http://localhost:8080/go_c5/index.php/api/linkEpubTestHub
    // http://tng.cambridge.edu.au/index.php/api/linkEpubTestHub
    public function linkEpubTestHub($sesskey)
    {
        $app = \Slim\Slim::getInstance();
        $app->get('/api/linkEpubTestHub/:sesskey', function($sesskey) use ($app) {
            $EpubTestHubUser = new EpubTesthubUser();
            echo json_encode($EpubTestHubUser->checkUser($sesskey));
        });
         
        $app->run();
        exit;
    }
    
    // http://localhost:8080/go_c5/index.php/api/loginExternalUser/user/password
    public function loginExternalUser2()
    {
        $app = \Slim\Slim::getInstance();
        $app->post('/api/loginExternalUser2', function() use ($app) {
            $allPostVars = $app->request()->post();
            $username = $allPostVars['username'];
            $password = $allPostVars['password'];
            
            $u = new User($username, $password);
            if ($u->uIsActive) { 
                $result = $u; 
            } else { 
                $result = array('error'=>'Invalid user name and/or password! Please try again.'); 
            }

            echo json_encode($result);
        });
         
        $app->run();
        exit;
    }
}
?>