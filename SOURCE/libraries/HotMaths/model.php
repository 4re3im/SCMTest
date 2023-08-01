<?php

/**
 * HOTMATHS API MODEL v2.0
 * ANZGO-3914 Added by Shane Camus 11/12/18
 */

class NewHotMathsModel
{
    const DATETIME_FORMAT = 'Y-m-d h:i:s';
    private $db;

    /**
     * NewHotMathsModel constructor.
     */
    public function __construct()
    {
        $this->db = Loader::db();
    }

    /**
     * @param $environment
     * @return mixed
     */
    public function getAccessToken($environment)
    {
        $sql = 'SELECT access_token FROM Hotmaths_API WHERE env = ?';
        $result = $this->db->GetRow($sql, array($environment));

        return $result['access_token'];
    }

    /**
     * @param $userID
     * @param $authToken
     * @param $hmUserID
     * @param $tokenExpiryDate
     * @param $brandCode
     * @param $schoolYear
     * @param $subscriberType
     * @return mixed
     */
    public function storeAuthorizationTokenPerUser(
        $userID,
        $authToken,
        $hmUserID,
        $tokenExpiryDate,
        $brandCode,
        $schoolYear = null,
        $subscriberType = null
    )
    {
        $sql = 'INSERT INTO Hotmaths (UserID, authorizationToken, externalId, tokenExpiryDate, brandCodes, ';
        $sql .= 'schoolYear, subscriberType, dateCreated) VALUES (?,?,?,?,?,?,?,?)';

        $param = array(
            $userID,
            $authToken,
            $hmUserID,
            $tokenExpiryDate,
            $brandCode,
            $schoolYear,
            $subscriberType,
            date(static::DATETIME_FORMAT)
        );

        $this->db->Execute($sql, $param);

        return $this->db->Insert_ID('Hotmaths');
    }
}
