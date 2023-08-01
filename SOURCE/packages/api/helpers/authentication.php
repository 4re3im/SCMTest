<?php

class AuthenticationHelper
{
    const VALID_BEARER_ELEMENT_COUNT = 2;

    private $isAllowed = false;

    /**
     * Authenticates basic bearer token
     * @param $header
     * @param $password
     * @return bool
     */
    public function authenticateBearerToken($header, $password)
    {
        $authorization = explode(' ', trim($header));
        if (!strpos($header, 'Bearer') && count($authorization) !== static::VALID_BEARER_ELEMENT_COUNT) {
            return $this->isAllowed;
        }

        if (base64_encode($password) === array_pop($authorization)) {
            $this->isAllowed = true;
        }

        return $this->isAllowed;
    }

    /**
     * Checks if request is authorized
     * @return bool
     */
    public function isAllowed()
    {
        return $this->isAllowed;
    }
}