<?php
/**
 * Created by PhpStorm.
 * User: jsunico
 * Date: 10/12/2018
 * Time: 1:36 PM
 */

Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');

class GigyaSocialize extends BaseGigya
{
    const METHOD_GET_USER_INFO = 'socialize.getUserInfo';

    public function getUserInfo($uId)
    {
        $request = new GSRequest(
            GIGYA_API_KEY,
            GIGYA_SECRET_KEY,
            static::METHOD_GET_USER_INFO,
            null,
            true,
            GIGYA_USER_KEY
        );
        $request->setParam('UID', $uId);
        $request->setAPIDomain(GIGYA_DATA_CENTER_DOMAIN);

        return $this->handleResponse($request->send());
    }
}
