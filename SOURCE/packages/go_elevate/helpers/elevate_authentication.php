<?php
/**
 * Reusable API Authentication for Elevate
 * @author John Renzo Sunico
 * June 13, 2017
 */

defined('C5_EXECUTE') or die(_('Access Denied.'));
define('AUTHENTICATION_INDEX', 1);

Loader::helper('elevate_encryption', 'go_elevate');
Loader::model('elevate_validation_hashes', 'go_elevate');


class ElevateAuthenticationHelper
{

    public static function authenticate()
    {
        $headers = getallheaders();
        $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        $authorization_array = explode(' ', $authorization);
        $response = array();

        if (!$authorization || (!strpos($authorization, 'Bearer') && count($authorization_array) < 2)) {
            self::raise_forbidden();
        } else {
            $auth = $authorization_array[AUTHENTICATION_INDEX];
            $auth = ElevateEncryptionHelper::decrypt($auth);
            parse_str($auth, $auth);

            if (!array_key_exists('GOUID', $auth) && !array_key_exists('token', $auth)) {
                self::raise_forbidden();
            }

            $userID = ElevateValidationHashes::getByUserID($auth['GOUID'], $auth['token']);
            if (!$userID) {
                self::raise_forbidden();
            }

            return $userID;
        }
    }

    public static function raise_bad_request()
    {
        $response = array();
        $response['message'] = 'Request is not correct or deformed';
        $response['status'] = '400 Bad Request';
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    public static function raise_forbidden()
    {
        $response['message'] = 'Invalid credentials';
        $response['status'] = '403 Forbidden';
        http_response_code(403);
        echo json_encode($response);
        exit;
    }

    public static function raise_method_not_allowed()
    {
        http_response_code(405);
        $response['message'] = "Method not allowed in this resource";
        $response['status'] = '405 Method not allowed';
        echo json_encode($response);
        exit;
    }
}
