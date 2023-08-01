<?php

/**
 * ANZGO-3951 , Added by John Renzo S. Sunico, 1/10/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../base_controller.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../restApi_interface.php";

class APISubscriptionUserController extends BaseController implements RestAPI
{
    const UNAVAILABLE_URI = '/api/default/unavailable';

    public function __construct()
    {
        Loader::model('access_code', 'api');

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

    public function checkAccessCode()
    {
        $code = strtoupper($this->get('access_code'));
        $accessCode = new AccessCode();
        $accessCode->loadByAccessCode($code);
        $this->set('result', $accessCode->getSubscriptionInfo());
    }
}
