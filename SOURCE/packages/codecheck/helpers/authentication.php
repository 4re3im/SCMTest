<?php

/**
 * API Authentication
 * @author Original: John Renzo Sonico
 * @author Modified: Shane Camus
 * November 29, 2017
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));
define('AUTHENTICATION_INDEX', 1);

class CodeCheckAuthenticationHelper
{

    public static function authenticate()
    {
        $header = getallheaders();
        $authorization = isset($header['Authorization']) ? $header['Authorization'] : null;
        $authorizationArray = explode(' ', $authorization);

        if (!$authorization || (!strpos($authorization, 'Bearer') && count($authorizationArray) < 2)) {
            static::raiseForbidden();
        } else {
            $auth = $authorizationArray[AUTHENTICATION_INDEX];
            $key = base64_encode(CODE_CHECK_SECRET_KEY.":".CODE_CHECK_PUBLIC_KEY);

            if (strcmp($auth, $key)) {
                static::raiseForbidden();
            }

        }
    }

    public static function raiseForbidden()
    {
        $response['message'] = 'Invalid credentials';
        $response['status'] = '403 Forbidden';
        http_response_code(403);
        echo json_encode($response);
        exit;
    }
}
