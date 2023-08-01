<?php

/**
 * Provisioned Users List per Bookseller
 * ANZGO-3595 Added by Shane Camus 01/25/2018
 */

defined('C5_EXECUTE') || die(_("Access Denied."));

class GoDashboardProvisionedUsersList extends DatabaseItemList
{
    private $queryCreated;
    protected $itemsPerPage = 20;

    protected function setBaseQuery()
    {
        $this->setQuery("SELECT *
                        FROM ProvisioningUsers users
                        JOIN ProvisioningFiles files ON users.FileID=files.ID"
        );
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
        $provisionedUsersList = array();
        $this->createQuery();
        $r = parent::get($itemsToGet, $offset);

        foreach ($r as $row) {
            $provisionedUsersList[] = new GoDashboardProvisionedUsers($row['uID'], $row);
        }

        return $provisionedUsersList;
    }

    public function sortByCreatedDate($order = 'DESC')
    {
        $this->sortBy('DateModified', $order);
    }

    public function filterByStaffID($value, $comparison = '=')
    {
        $this->filter('files.StaffID', $value, $comparison);
    }

    public function filterByuID($value, $comparison = '=')
    {
        $this->filter('uID', $value, $comparison);
    }

    public function filterByKeyword($keywords)
    {
        $db = Loader::db();
        $quotedKeywords = $db->quote('%' . $keywords . '%');

        $this->filter(false, '( Email LIKE' . $quotedKeywords . ')');
    }

}
