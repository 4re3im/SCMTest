<?php

/**
 * UserModel extends Core User class and adds functionalities
 * ANZGO-3481 , Added by John Renzo S. Sunico, 10/11/2017
 */

class UserModel extends User
{
    const GROUP_STUDENTS = 'Student';
    const GROUP_TEACHERS = 'Teacher';

    private $usersIDs = array();
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Loader::db();
    }

    /**
     * ANZGO-3481 Added by John Renzo S. Sunico, October 10, 2017
     * Returns list of UserIDs from a Group
     * @param $groupName
     * @return array $userIDs
     */
    public function getUserIDsByGroup($groupName)
    {
        $db = Loader::db();
        $userGroup = Group::getByName($groupName);

        if (!$userGroup) {
            throw new InvalidArgumentException("Invalid group name");
        }

        $sql = "SELECT u.uID as IDs FROM Users u JOIN UserGroups ug ON u.uID = ug.uID WHERE ug.gID = ?;";
        $userIDs = $db->GetAll($sql, array($userGroup->getGroupID()));

        if ($userIDs) {
            $this->usersIDs = array_column($userIDs, 'IDs');
        }

        return $this->usersIDs;
    }

    /**
     * ANZGO-3481 Added by John Renzo S. Sunico, October 10, 2017
     * Takes list of userIDs and return list of userIDs that belongs
     * to specified group
     * @param $listOfUserIDs
     * @param $groupName
     * @throws InvalidArgumentException when group is not found
     * @return array
     */
    public static function filterUserIDsByGroup(array $listOfUserIDs, $groupName)
    {
        $db = Loader::db();
        $userGroup = Group::getByName($groupName);

        if (!$userGroup) {
            throw new InvalidArgumentException("Group $groupName not found.");
        }

        if (!$listOfUserIDs) {
            return [];
        }

        $listOfUserIDs = array_map('intval', $listOfUserIDs);
        $listOfUserIDs = implode(',', $listOfUserIDs);

        $sql = <<<SQL
            SELECT u.uID as ID
            FROM Users u
            JOIN UserGroups ug ON u.uID = ug.uID
            WHERE ug.gID = ? AND u.uID IN ($listOfUserIDs);
SQL;

        $results = $db->GetAll($sql, array($userGroup->getGroupID()));
        if ($results) {
            return array_column($results, 'ID');
        }

        return array();
    }

    public static function getByUserID($uID, $login = false, $cacheItemsOnLogin = true)
    {
        $new = new UserModel();
        if ($uID) {
            $user = parent::getByUserID($uID, $login, $cacheItemsOnLogin);
            foreach ($user as $k => $v) {
                $new->$k = $v;
            }
        }

        return $new;
    }

    public function getEmail()
    {
        $uID = $this->getUserID();

        if (property_exists($this, 'uEmail') && $this->uEmail) {
            return $this->uEmail;
        }

        $sql = 'SELECT uEmail FROM Users WHERE uID = ? LIMIT 1';
        $result = $this->db->GetCol($sql, array($uID));

        if ($result) {
            $this->{'uEmail'} = $result[0];

            return $this->uEmail;
        }

        return '';
    }

    public function hasSalesForceID()
    {
        $uEmail = $this->getEmail();
        $sql = 'SELECT email FROM SalesforceContacts WHERE email = ? LIMIT 1';

        return count($this->db->GetRow($sql, array($uEmail))) > 0;
    }
}
