<?php
/**
 * SB-674 Added by mtanada 20200904
 */
defined('C5_EXECUTE') || die(_("Access Denied."));

class ProvisioningHotmathsJobList extends DatabaseItemList
{
    private $queryCreated;
    protected $itemsPerPage = 10;

    protected function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM ProvisioningJobs");
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
        $this->createQuery();
        $jobList = parent::get($itemsToGet, $offset);

        return $jobList;
    }

    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function sortByDateCreated($order = 'ASC')
    {
        $this->sortBy('DateCreated', $order);
    }

    public function sortByID($order = 'DESC')
    {
        $this->sortBy('ID', $order);
    }
}
