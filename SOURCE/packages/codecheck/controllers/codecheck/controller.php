<?php

/**
 * Code Health Check Controller ANZGO-3490, September 06, 2017
 * @author jsunico@cambridge.org
 */

class CodeCheckController extends Controller
{
    private $pkgHandle = 'codecheck';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("codecheck_theme"));
        Loader::model('access_code', $this->pkgHandle);
        Loader::library('Activation/hub_activation');
        Loader::library('Activation/library');
    }

    // HUB-19 Modified by John Renzo S. Sunico, 05/23/2018
    public function verifyAccessCode()
    {
        header('Content-Type: application/json');
        $accessCode = $this->post('access_code', []);
        $parameters = [
            'accessCode' => implode('-', $accessCode),
            'isForCodeCheck' => true
        ];

        // HUB-148 Modified by John Renzo S. Sunico, 08/30/2018
        $activation = new HubActivation($parameters);
        $response = $activation->checkCodeHealth();

        echo json_encode($response);
        exit;
    }
}
