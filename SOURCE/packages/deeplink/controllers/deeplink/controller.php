<?php defined('C5_EXECUTE') or die("Access Denied.");

define('PROD_DOMAIN', 'https://hotmaths.cambridge.edu.au');
define('TEST_DOMAIN', 'https://testportal.edjin.com');

class DeeplinkController extends Controller 
{
    const DEEPLINK_HASH = 'hash';
    const BRAND_CODE    = 'brandCode';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));

        Loader::library('HotMaths/api');
    }

    public function view($deeplinkHash = null, $brandCode = null) 
    {
        if (is_null($deeplinkHash) || is_null($brandCode)) {
            $this->redirect('go/myresources');
        }

        $user = new User();
        $hmDb = new HotMathsModel();
        $userId = $user->getUserID();

        $domain = TEST_DOMAIN;
        if (defined('PRODUCTION_MODE') && PRODUCTION_MODE) {
            $domain = PROD_DOMAIN;
        }

        if ($user->isRegistered()) {
            $hotmathsUser = $hmDb->getTngHotmathsUser($userId, $brandCode);
            $hotmathsUser['authorizationToken'];

            $redirectURL = $domain . '/cambridgeLogin?access_token=' . $hotmathsUser['authorizationToken'] . 
                '&linkHash=' . $deeplinkHash;

            header('Location: ' . $redirectURL);
        } else {
            $_SESSION[static::DEEPLINK_HASH] = $deeplinkHash;
            $_SESSION[static::BRAND_CODE] = $brandCode;
            $this->redirect('go/login');
        }
    }
}