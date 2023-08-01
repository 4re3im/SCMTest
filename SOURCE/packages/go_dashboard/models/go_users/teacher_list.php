<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

class GoDashboardGoUsersTeacherList extends DatabaseItemList
{
    private $queryCreated;
    protected $itemsPerPage = 10;

    protected function setBaseQuery()
    {
        /**
         * ANZGO-3327 Modified by John Renzo S. Sunico, 12/08/2017
         * Removed slow join salesforce
         */
        // ANZGO-3678 added by jbernardez 20180411
        // added join UserSearchIndexAttributes to get value of ak_uHideSalesForce
        $this->setQuery("SELECT u.uID
                        FROM Users u
                        LEFT JOIN UserGroups ug ON ug.uID = u.uID
                        INNER JOIN UserSearchIndexAttributes usia ON usia.uID = u.uID");
    }

    protected function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        Loader::model('user/user_model');
        $teacherList = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);

        foreach ($r as $row) {
            $teacherList[] = UserModel::getByUserID($row['uID']);
        }

        return $teacherList;
    }

    public function sortByCreatedDate($order = 'DESC')
    {
        $this->sortBy('u.uDateAdded', $order);
    }

    public function sortByUserID($order = 'DESC')
    {
        $this->sortBy('u.uID', $order);
    }


    public function filterByTeacherGroup($gID = null)
    {
        $this->filter(false, "ug.gID = $gID");
    }

    public function filterByInterval($interval = null)
    {
        $today = date("Y-m-d H:i:s");
        $this->filter(false, "u.uDateAdded BETWEEN NOW() - INTERVAL '$interval' DAY AND '$today' ");
    }

    // ANZGO-3745 added by jbernardez 20180606
    public function filterByDateTillToday($interval = null)
    {
        $today = date("Y-m-d H:i:s");
        $this->filter(false, "u.uDateAdded BETWEEN '$interval' AND '$today' ");
    }

    public function filterByIsActive($value, $comparison = '=')
    {
        $this->filter('uIsActive', $value, $comparison);
    }

    public function filterByuID($value, $comparison = '=')
    {
        $this->filter('uID', $value, $comparison);
    }

    public function filterByKeyword($keywords)
    {
        $db = Loader::db();
        $qKeyword = $db->quote('%' . $keywords . '%');
        $this->filter(false, '( uEmail LIKE' . $qKeyword . ')');
    }

    // ANZGO-3678 added by jbernardez 20180411
    public function filterBySFAttribute($value = null)
    {
        $this->filter(false, "usia.ak_uHideSalesForce = $value");
    }

    // ANZGO-3745 added by jbernardez 20180606
    public function getCurrentPage() {
        return $this->currentPage;
    }
}
