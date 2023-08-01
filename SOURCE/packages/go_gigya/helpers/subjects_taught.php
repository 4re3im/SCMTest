<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 30/05/2019
 * Time: 4:38 PM
 */

Loader::library('gigya/GSSDK');
Loader::library('gigya/GigyaEmailAccount');

class SubjectsTaughtHelper
{
    private $accounts;

    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    public function getPerTeacher()
    {
        $emailAccount = new GigyaEmailAccount();
        $uids = [];
        $results = $this->accounts->getArray('results');

        for ($i = 0; $i < $results->Length(); $i++) {
            $uids[] = $results->GetArray($i)->GetString('UID');
        }

        return $emailAccount->getSubjectsTaught($uids);
    }


}