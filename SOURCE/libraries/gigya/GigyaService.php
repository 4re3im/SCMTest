<?php

/**
 * Class GigyaService
 * Contains common function used in handling Gigya
 */

Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');

class GigyaService extends BaseGigya
{
    const METHOD_EXCHANGE_UID_SIGNATURE = 'accounts.exchangeUIDSignature';

    /**
     * Verifies user uid, signature and timestamp from R-a-a-s
     *
     * @param $uId
     * @param $uIdSignature
     * @param $uIdTimestamp
     * @return bool|array
     */
    public function verifyUser($uId, $uIdSignature, $uIdTimestamp)
    {
        $exchangedSignResponse = $this->exchangeUidSignature(
            $uId,
            $uIdSignature,
            $uIdTimestamp
        );

        if (!$exchangedSignResponse) {
            return false;
        }

        $exchangedUID = $exchangedSignResponse->getString('UID');
        $exchangedSignature = $exchangedSignResponse
            ->getString('UIDSignature');
        $exchangedTimestamp = $exchangedSignResponse
            ->getInt('signatureTimestamp');


        return SigUtils::validateUserSignature(
            $exchangedUID,
            $exchangedTimestamp,
            GIGYA_SECRET_KEY,
            $exchangedSignature
        );
    }

    /**
     * Use this if you only have application keys
     * and do not have partner secret key.
     *
     * @param $uId string
     * @param $uIdSignature string
     * @param $uIdTimestamp int
     * @return bool|GSObject
     */
    public function exchangeUidSignature(
        $uId,
        $uIdSignature,
        $uIdTimestamp
    ) {
        $request = new GSRequest(
            GIGYA_API_KEY,
            GIGYA_SECRET_KEY,
            static::METHOD_EXCHANGE_UID_SIGNATURE,
            null,
            true,
            GIGYA_USER_KEY
        );
        $request->setParam('UID', $uId);
        $request->setParam('UIDSignature', $uIdSignature);
        $request->setParam('signatureTimestamp', $uIdTimestamp);
        $request->setAPIDomain(GIGYA_DATA_CENTER_DOMAIN);

        return $this->handleResponse($request->send());
    }
}
