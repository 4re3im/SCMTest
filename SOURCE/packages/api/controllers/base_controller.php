<?php

/**
 * ANZGO-3233 Added by John Renzo S. Sunico
 * Class BaseController
 * Contains essential and basic methods in
 * handling API request.
 */

abstract class BaseController extends Controller
{
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    const CONTENT_JSON = 'application/json';

    const RESPONSE_SUCCESS = 'success';
    const RESPONSE_MESSAGE = 'message';
    const RESPONSE_DATA = 'data';
    const RESPONSE_ERRORS = 'errors';

    private $crud = array(
        self::HTTP_METHOD_POST => 'create',
        self::HTTP_METHOD_GET => 'read',
        self::HTTP_METHOD_PUT => 'update',
        self::HTTP_METHOD_DELETE => 'delete'
    );

    public $authenticate = true;
    public $pkgHandle = 'api';

    /**
     * Override $authenticate in your child class
     * and set it to false if you want to make
     * your API public. Also parses JSON to $_POST
     * if request content type is JSON.
     */
    public function on_start()
    {
        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle('json_theme', $this->pkgHandle));

        if ($this->authenticate) {
            $headers = getallheaders();
            $authorization = $headers['Authorization'];

            $authenticator = Loader::helper('authentication', $this->pkgHandle);
            $authenticator->authenticateBearerToken($authorization, API_SECRET_KEY);

            if (!$authenticator->isAllowed()) {
                $this->redirect('/api/default/forbidden');
            }
        }

        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if ($method !== static::HTTP_METHOD_GET && $this->isJSON()) {
            $this->parseBodyAsPost();
        }
    }

    /**
     * Identifies request method and calls that method.
     * This follows RESTAPI Interface (CRUD)
     * @param null $id
     */
    public function view($id = null)
    {
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $functionName = $this->crud[$method];

        if (method_exists($this, $functionName)) {
            $this->$functionName($id);
        }
    }

    /**
     * Gets request body and decode it
     * Typical for request with JSON content type.
     */
    public function parseBodyAsPost()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Checks if request is in JSON format.
     * @return bool
     */
    public function isJSON()
    {
        $headers = getallheaders();
        $contentType = $headers['Content-Type'];

        return $contentType === static::CONTENT_JSON;
    }

    /**
     * Sets API response
     * @param $success
     * @param $message
     * @param null $data
     */
    public function setAPIResponse($success, $message, $data = null)
    {
        if (is_object($message)) {
            $message = (string)$message;
        }

        $this->set('result', array(
                static::RESPONSE_SUCCESS => $success,
                static::RESPONSE_MESSAGE => $message,
                static::RESPONSE_DATA => $data
            )
        );
    }

    /**
     * Set API Error response
     * @param $message
     * @param array $errorList
     */
    public function setErrorAPIResponse($message, $errorList = array())
    {
        if (is_object($message)) {
            $message = (string)$message;
        }

        $this->set('result', array(
                static::RESPONSE_SUCCESS => false,
                static::RESPONSE_MESSAGE => $message,
                static::RESPONSE_ERRORS => $errorList
            )
        );
    }
}
