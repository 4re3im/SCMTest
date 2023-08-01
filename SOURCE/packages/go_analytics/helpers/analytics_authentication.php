<?php

/**
 * ANZGO-3430 Reusable API Authentication for Analytics
 * @author Renzo & Shane
 * June 29, 2017
 */

defined('C5_EXECUTE') or die(_('Access Denied.'));

define('AUTHENTICATION_INDEX', 1);

class AnalyticsAuthenticationHelper
{

    public static function authenticate()
    {
        $header = getallheaders();
        $authorization = isset($header['Authorization']) ? $header['Authorization'] : null;
        $authorizationList = explode(' ', $authorization);

        if (!$authorization || (!strpos($authorization, 'Bearer') && count($authorizationList) < 2)) {
            static::raiseForbidden();
        } else {
            $auth = $authorizationList[AUTHENTICATION_INDEX];
            $key = base64_encode(ANALYTICS_SECRET_KEY . ":" . ANALYTICS_PUBLIC_KEY);

            if (strcmp($auth, $key)) {
                static::raiseForbidden();
            }

        }
    }

    public static function postRequestOnly()
    {
        $method = strtolower(
            filter_input(
                INPUT_SERVER,
                'REQUEST_METHOD',
                FILTER_SANITIZE_STRING
            )
        );

        if ($method != 'post') {
            static::raiseMethodNotAllowed();
        }
    }

    public static function raiseMethodNotAllowed()
    {
        $view = View::getInstance();
        $view->controller->redirect('analytics/defaults/methodNotAllowed');
    }

    public static function raiseForbidden()
    {
        $view = View::getInstance();
        $view->controller->redirect('analytics/defaults/forbidden');
    }
}
