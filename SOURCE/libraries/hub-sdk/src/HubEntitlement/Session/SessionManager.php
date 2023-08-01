<?php

/**
 * Session Manager
 * Handles sessions
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Session;


class SessionManager
{
    const SESSION_JWT_KEY = 'HUB_JWT_TOKEN';
    const SESSION_JWT_REFRESH_TOKEN = 'HUB_JWT_REFRESH_TOKEN';
    const KEY_JWT_TOKEN = 'JWT';
    const KEY_JWT_EXPIRY = 'EXPIRY';
    const TWO_HOURS = (60 * 60 * 2);

    /**
     * Get session value
     * @param $key
     * @param string $default
     * @return bool|mixed
     */
    public static function get($key, $default = '')
    {
        $value = isset($_SESSION[$key]) ? $_SESSION[$key] : '';
        return $value ? $value : $default;
    }

    /**
     * Set session values
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Updates token information in Session
     * @param $jwtToken
     * @param $refreshToken
     */
    public static function setJwt($jwtToken, $refreshToken = false)
    {
        static::set(static::SESSION_JWT_KEY, [
            static::KEY_JWT_TOKEN => $jwtToken,
            static::KEY_JWT_EXPIRY => time() + static::TWO_HOURS
        ]);

        if ($refreshToken) {
            static::set(static::SESSION_JWT_REFRESH_TOKEN, $refreshToken);
        }
    }
}
