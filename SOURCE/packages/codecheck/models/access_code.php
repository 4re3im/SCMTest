<?php
/**
 * ANZGO-3490 Added by John Renzo S. Sunico, Sept. 04, 2017
 */

class AccessCode
{
    /**
     * ANZGO-3490 Added by John Renzo S. Sunico, Sept. 06, 2017
     * @param $access_code should be separated by hyphen
     * @return array|bool
     */
    public static function getAccessCodeDetails($access_code)
    {
        $db = Loader::db();
        $sql = "SELECT cgac.ID, cgac.AccessCode, cgac.CreationDate, DATE(cgac.DateActivated) as DateActivated, cgac.Active, cgac.UsageMax,
                    cgac.UsageCount, cgac.Usable, cgacb.EOL
                FROM CupGoAccessCodes cgac
                JOIN CupGoAccessCodeBatch cgacb ON cgac.BatchID = cgacb.ID
                WHERE cgac.AccessCode = ?";
        return $db->GetRow($sql, array($access_code));
    }

    /**
     * ANZGO-3497 Added by John Renzo S. Sunico
     * Access Code Validity Checker
     *
     * @param $accessCode
     * @param bool $setSession
     * @return array
     */
    public static function validateAccessCode($accessCode, $setSession = false)
    {
        $codeInfo = self::getAccessCodeDetails($accessCode);

        $response = array(
            'access_code' => $accessCode,
            'usable' => false,
            'date_activated' => null,
            'isGoProduct' => false,
            'found' => false,
            'hm_brandCode' => strtoupper(substr($accessCode, 0, 2)),
            // ANZGO-2523 add by jbernardez 20170922
            'isProperLength' => false
        );

        // ANZGO-2523 add by jbernardez 20170922
        if (!self::checkAccessCodeLength($accessCode)) {
            return $response;
        } else {
            // ANZGO-2523 add by jbernardez 20170922
            $response['isProperLength'] = true;
        }

        if ($codeInfo) {

            if ($setSession) {
                list($_SESSION['form-accesscode_s1'], $_SESSION['form-accesscode_s2'], $_SESSION['form-accesscode_s3'], $_SESSION['form-accesscode_s4']) = explode('-', $accessCode);
            }

            $response['usable'] = $codeInfo['Usable'] === 'Y' && $codeInfo['Active'] === 'Y';
            $response['date_activated'] = $codeInfo['DateActivated'];
            $response['isGoProduct'] = true;
            $response['found'] = true;

            if ($codeInfo['EOL']) {
                $today = date("Y-m-d");
                $eol = date("Y-m-d", strtotime($codeInfo['EOL']));

                if ($today > $eol) {
                    $response['usable'] = false;
                }
            }
        }

        if (!$codeInfo) {
            // ANZGO-3563 added by jbernardez 20171201
            // added this line as there was code restructure in controller
            // this was loaded globally before
            Loader::library('HotMaths/api');
            $hotmaths = new HotMathsApi(array('userId' => 0, 'accessCode' => $accessCode, 'response' => 'JSON'));
            $hmResponse = $hotmaths->validateAccessCode();

            if ($hmResponse && !isset($hmResponse->success) && !$hmResponse->success && isset($hmResponse->activationState)) {
                $response['usable'] = ($hmResponse->activationState === "CODE_NOT_ACTIVATED");
                $response['date_activated'] = isset($hmResponse->usedDate) ? $hmResponse->usedDate : null;
                $response['found'] = true;
            }
        }

        return $response;
    }

    /**
     * ANZGO-3523 added by jbernardez 20170922
     *
     * @param $accessCode
     * @return bool
     */
    private static function checkAccessCodeLength($accessCode)
    {
        return (strlen($accessCode) != 19) ? false : true;
    }
}