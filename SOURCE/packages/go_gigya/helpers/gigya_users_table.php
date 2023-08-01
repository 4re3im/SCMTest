<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 20/05/2019
 * Time: 1:29 PM
 */
Loader::library('gigya/GSSDK');

class GigyaUsersTableHelper
{
    const PACKAGE_NAME = 'go_gigya';
    const GIGYA_MAX_LIMIT = 5000;

    private $tableHeaders = [
        'Creation Date',
        'Full Name',
        'Email',
        'School',
        'Subjects',
        'Platform'
    ];

    private $accounts;
    private $subjectsTaught;
    private $results;
    private $totalCount;
    private $objectsCount;
    private $activePage = 1;
    private $displayLimit = 7;
    private $pagesArray = [];
    private $pagesArrayChunks = [];
    private $totalPages;
    private $pagesLimit = 0;
    // GCAP-839 added by mtanada 20200427
    private $platforms;

    public function setSubjectsTaught($subjects)
    {
        $tempSubjects = $subjects->getArray('results');

        for ($i = 0; $i < $tempSubjects->Length(); $i++) {
            $uid = $tempSubjects->getObject($i)->getString('UID');
            $subjects = $tempSubjects->getObject($i)
                ->getObject('subscriptions')
                ->getObject('go.platformUpdates')
                ->getObject('email')
                ->getArray('tags');
            $subjects = json_decode($subjects);
            $this->subjectsTaught[$uid] = $subjects;
        }
    }

    // GCAP-839 added by mtanada 20200427
    public function getPlatforms($results)
    {
        $tempResults = json_decode($results, true);
        $preferences = array();

        // Loop through user's details and eliminate unnecessary data
        foreach ($tempResults as $result) {
            $uid = $result['UID'];
            $preference = $result['preferences']['terms'];
            $preferences[$uid] = $preference;
        }
        // Isolate all the gigya terms name only (hub, GO, clms, etc.)
        // @return array
        foreach ($preferences as $uid => $value) {
            if ($value !== null) {
                $tmp = array_keys($value);
                $key = array_search('hub', $tmp);
                if ($tmp[$key] === 'hub') {
                    $replace = array($key => 'ANZ');
                    $platform = array_replace($tmp, $replace);
                } else {
                    $platform = $tmp;
                }
                $this->platforms[$uid] = $platform;
            } else {
                $this->platforms[$uid] = null;
            }
        }
    }

    public function init($accounts)
    {
        $this->accounts = $accounts;
        $this->results = $this->accounts->getArray('results');
        $this->getPlatforms($this->results);
        $this->totalCount = $this->accounts->getInt('totalCount');
        $this->objectsCount = $this->accounts->getInt('objectsCount');
        $this->totalPages = ceil($this->totalCount / $this->objectsCount);
        $this->pagesLimit = floor((static::GIGYA_MAX_LIMIT + $this->objectsCount) / $this->objectsCount);
        if ($this->totalPages > $this->pagesLimit) {
            $this->totalPages = $this->pagesLimit;
        }
        $this->pagesArray = range(1, $this->totalPages);
        $this->pagesArrayChunks = array_chunk($this->pagesArray, $this->displayLimit);
    }

    public function update($accounts, $activePage = 1)
    {
        $this->activePage = $activePage;
        $this->init($accounts);
    }

    public function getInitialTable()
    {
        ob_start();
        Loader::packageElement('gigya/users_table/table', static::PACKAGE_NAME, ['headers' => $this->tableHeaders]);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    // GCAP-839 modified by mtanada 20200427 added platforms data
    public function loadBody()
    {
        ob_start();
        Loader::packageElement(
            'gigya/users_table/table_body',
            static::PACKAGE_NAME,
            [
                'results' => $this->results,
                'subjectsTaught' => $this->subjectsTaught,
                'platforms' => $this->platforms
            ]);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function loadPager()
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
            'gigya/users_table/pager',
            static::PACKAGE_NAME,
            [
                'activePage' => $this->activePage,
                'objectsCount' => $this->objectsCount,
                'totalCount' => $this->totalCount,
                'pagesArrayChunks' => $this->pagesArrayChunks,
                'pageSetKey' => $pageSetKey,
                'displayLimit' => $this->displayLimit,
                'totalPages' => $this->totalPages
            ]
        );
        $buffer = ob_get_clean();
        ob_end_clean();
        return $buffer;
    }
}
