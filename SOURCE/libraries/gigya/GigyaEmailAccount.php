<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 30/05/2019
 * Time: 2:21 PM
 */
Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');

class GigyaEmailAccount extends BaseGigya
{
    const METHOD_SEARCH = 'accounts.search';

    public function getSubjectsTaught($uids)
    {
        $uidsString = implode("','", $uids);
        $query = "select UID, subscriptions from emailAccounts where subscriptions.go.platformUpdates.email.isSubscribed=true ";
        $query .= "and UID in('$uidsString')";

        return $this->search($query);
    }

    public function search($query)
    {
        $request = $this->newRequest(static::METHOD_SEARCH);
        $request->setParam('query', $query);

        return $this->handleResponse($request->send());
    }
}