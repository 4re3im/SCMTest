<?php

/**
 * Code Health Check API Controller ANZGO-3563, November, 29 2017
 * @author scamus@cambridge.org
 */

class CodeCheckApiController extends Controller
{
    private $pkgHandle = 'codecheck';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("codecheck_theme"));
        Loader::library('Activation/library');
        Loader::library('Activation/hub_activation');
        Loader::helper('authentication', $this->pkgHandle);
    }

    public function getStatusMessage()
    {
        header('Content-Type: application/json');

        $method = strtolower(
            filter_input(
                INPUT_SERVER,
                'REQUEST_METHOD',
                FILTER_SANITIZE_STRING
            )
        );

        if ($method != 'post') {
            http_response_code(405);
            $response['message'] = 'Method not allowed in this resource';
            $response['status'] = '405 Method not allowed';
            echo json_encode($response);
            exit;
        }

        CodeCheckAuthenticationHelper::authenticate();

        $data = $this->post();
        $data['isForCodeCheck'] = true;

        // ANZGO-3760 Modified by mtanada 20180719 PEAS Integration
        // HUB-148 Modified by John Renzo S. Sunico, 08/30/2018
        $activation = new HubActivation($data);
        $result = $activation->checkCodeHealth();

        echo json_encode($result);
        exit;
    }
}
