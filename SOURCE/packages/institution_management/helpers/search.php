<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 25/01/2021
 * Time: 9:14 PM
 */

class SearchHelper
{
    const PACKAGE_HANDLE = 'institution_management';
	// SB-1117 modified by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    const TABLE_HEADERS = [
        'OID',
        'Name',
        'Full Address',
        'Remove Rejection Status'
    ];
    const GIGYA_MAX_LIMIT = 5000;
    const RESULT_LIMIT = 15;
    const PAGER_URL = '/dashboard/institution_management/search/navigate';

	// SB-1117 added by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    private $rpModel;

    private $error;
    private $data;
    private $results;
    private $objectsCount;
    private $totalCount;
    private $totalPages;
    private $pagesLimit = 0;
    private $pagesArray = [];
    private $pagesArrayChunks = [];
    private $displayLimit = 7;
    private $activePage = 1;

	// SB-1117 added by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    public function __construct()
    {
        Loader::model(
            'rejected_institutions',
            self::PACKAGE_HANDLE
        );
        $this->rpModel = new RejectedInstitutionsModel();
    }

    public function init($institutions)
    {
        $obj = (object)$institutions;
        $this->error = $obj->error;
        $this->data = $obj->data;
		// SB-1117 modified by timothy.perez - Users who have been rejected from a school registration are unable to join another school
        $this->results = $this->formatResults($this->data['results']);
        $this->objectsCount = (int)$this->data['objectsCount'];
        $this->totalCount = (int)$this->data['totalCount'];

        if ($this->objectsCount < static::RESULT_LIMIT) {
            $this->objectsCount = static::RESULT_LIMIT;
        }

        if ($this->objectsCount > 0) {
            $this->totalPages = ceil($this->totalCount / $this->objectsCount);
            $this->pagesLimit = floor((static::GIGYA_MAX_LIMIT + $this->objectsCount) / $this->objectsCount);
        }

        if ($this->totalPages > $this->pagesLimit) {
            $this->totalPages = $this->pagesLimit;
        }
        $this->pagesArray = range(1, $this->totalPages);
        $this->pagesArrayChunks = array_chunk($this->pagesArray, $this->displayLimit);
    }

    public function displayResults($institutions)
    {
        $hasResult = (int)$institutions['data']['objectsCount'] > 0;
        $this->init($institutions);
        return json_encode([
            'table' => $this->buildTable(),
            'pager' => $this->buildPager(),
            'hasResult' => $hasResult
        ]);
    }

    public function navigateResults($institutions)
    {
        $this->init($institutions);
        return json_encode([
            'tableBody' => $this->buildTableBody(),
            'pager' => $this->buildPager(),
            'hasContents' => $this->objectsCount > 0
        ]);
    }

	// SB-1117 added by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    public function formatResults($institutions) 
    {
        $this->rpModel = new RejectedInstitutionsModel();
        $oids = array_column($institutions, 'oid');
        $rejectedInstOids = array_keys($this->rpModel->getByOids($oids));
        $formattedInst = [];

        foreach ($institutions as $institution) {
            $institution['isRejected'] = in_array($institution['oid'], $rejectedInstOids);
            $formattedInst[] = $institution;
        }
        return $formattedInst;
    }

    public function buildTable()
    {
        ob_start();
        Loader::packageElement(
            'search/table',
            static::PACKAGE_HANDLE,
            [
                'headers' => static::TABLE_HEADERS,
                'results' => $this->results,
                'totalCount' => $this->totalCount
            ]
        );
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function buildTableBody()
    {
        ob_start();
        Loader::packageElement(
            'search/table_body',
            static::PACKAGE_HANDLE,
            ['headers' => static::TABLE_HEADERS, 'results' => $this->results]
        );
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function buildPager()
    {
        $pageSetKey = 0;
        foreach ($this->pagesArrayChunks as $index => $pagesArrayChunk) {
            if (in_array($this->activePage, $pagesArrayChunk)) {
                $pageSetKey = $index;
                break;
            }
        }

        ob_start();
        Loader::packageElement(
            'search/table_pager',
            static::PACKAGE_HANDLE,
            [
                'activePage' => $this->activePage,
                'objectsCount' => $this->objectsCount,
                'totalCount' => $this->totalCount,
                'pagesArrayChunks' => $this->pagesArrayChunks,
                'pageSetKey' => $pageSetKey,
                'displayLimit' => $this->displayLimit,
                'totalPages' => $this->totalPages,
                'pagerURL' => static::PAGER_URL
            ]
        );
        $buffer = ob_get_clean();
        ob_end_clean();
        return $buffer;
    }

    public function setActivePage($page)
    {
        $this->activePage = $page;
    }

}