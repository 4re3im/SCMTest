<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 29/06/2021
 * Time: 10:36 PM
 */

class ReviewPendingHelper
{
    const GIGYA_MAX_LIMIT = 5000;
    const LIMIT = 15;
    const MAIL_FROM = 'directcs@cambridge.org';
    const PACKAGE_HANDLE = 'institution_management';
    const PAGER_URL = '/dashboard/institution_management/review_pending/navigate';
    const RESULT_LIMIT = 15;

    private $gigyaAccount;
    private $gigyaInstitution;
    private $rpModel;

    // Pager
    private $activePage = 1;
    private $data;
    private $displayLimit = 7;
    private $error;
    private $objectsCount;
    private $pagesArray = [];
    private $pagesArrayChunks = [];
    private $pagesLimit = 0;
    private $results;
    private $totalCount;
    private $totalPages;

    public function __construct()
    {
        Loader::model(
            'rejected_institutions',
            self::PACKAGE_HANDLE
        );
        $this->gigyaInstitution = new GigyaInstitution();
        $this->gigyaAccount = new GigyaAccount();
        $this->rpModel = new RejectedInstitutionsModel();
    }

    public function getInstitutions($page = 1, $keyword = null, $filter = null)
    {
        $this->rpModel = new RejectedInstitutionsModel();
        $options = ['page' => $page, 'limit' => self::LIMIT];
        $unverifiedInstitutions = $this->gigyaInstitution->getUnverifiedInstitutions($options, true, $keyword, $filter);
        if (
            isset($unverifiedInstitutions['error']) &&
            $unverifiedInstitutions['error'] !== ''
        ) {
            return [];
        }
        $this->activePage = $page;
        $this->init($unverifiedInstitutions);
        $formattedInst = $this->formatInstitutions($unverifiedInstitutions);
        $oids = array_keys($formattedInst);
        $users = json_decode($this->gigyaAccount->getUsersByOID($oids));
        $usersArr = (array)$users;
        $formattedUsers = $this->formatRequesters($usersArr);
        $records = $this->rpModel->getByOids($oids);
        $displayData = $this->formatData($formattedInst, $formattedUsers, $records);
        return $displayData;

    }
    public function formatData($institutions, $users, $records)
    {
        foreach ($institutions as $oid => $institution) {
            $remarks = 'Rejected';
            if (!isset($users[$oid])) {
                $remarks = 'Not allowed to reject';
            }

            if (isset($records[$oid])) {
                $remarks .= " ($records[$oid])";
            }
            $institutions[$oid]['requester'] = $users[$oid];
            $institutions[$oid]['enableReject'] = isset($users[$oid]) && $users[$oid] !== '' && !isset($records[$oid]);
            $institutions[$oid]['remarks'] = $remarks;
        }
        return $institutions;
    }

    public function formatInstitutions($institutions)
    {
        $formatted = [];
        $results = $institutions['data']['results'];
        foreach ($results as $result) {
            $formatted[$result['oid']] = [
                'name' => $result['data']['name'],
                'address' => nl2br(trim($result['data']['formattedAddress'])),
                'schoolCode' => $result['data']['edueltTeacherCode']
            ];
        }
        return $formatted;
    }

    public function formatRequesters($requesters)
    {
        function reduceUsers($carry, $item) {
            $instituteRole = $item->data->eduelt->instituteRole;
            $profile = $item->profile;
            foreach ($instituteRole as $role) {
                if (isset($role->key_s)) {
                    $oid = $role->key_s;
                    $carry[$oid]['name'] = "$profile->firstName $profile->lastName";
                    $carry[$oid]['email'] = $profile->email;
                    $carry[$oid]['UID'] = $item->UID;
                }
                continue;
            }
            return $carry;
        }

        $results = $requesters['results'];
        $formatted = array_reduce(
            $results,
            "reduceUsers",
            []
        );
        return $formatted;
    }

    public function init($institutions)
    {
        $obj = (object)$institutions;
        $this->error = $obj->error;
        $this->data = $obj->data;
        $this->results = $this->data['results'];
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

    public function buildTable($data)
    {
        ob_start();
        Loader::packageElement(
            'review_pending/table_body',
            self::PACKAGE_HANDLE,
            ['data' => $data]
        );
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function buildPager()
    {
        $pagerData = $this->getPagerData();
        ob_start();
        Loader::packageElement(
            'review_pending/table_pager',
            self::PACKAGE_HANDLE,
            ['pager' => $pagerData]
        );
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function getPagerData()
    {
        $pageSetKey = 0;
        foreach ($this->pagesArrayChunks as $index => $pagesArrayChunk) {
            if (in_array($this->activePage, $pagesArrayChunk)) {
                $pageSetKey = $index;
                break;
            }
        }

        return [
            'activePage' => $this->activePage,
            'objectsCount' => $this->objectsCount,
            'totalCount' => $this->totalCount,
            'pagesArrayChunks' => $this->pagesArrayChunks,
            'pageSetKey' => $pageSetKey,
            'displayLimit' => $this->displayLimit,
            'totalPages' => $this->totalPages,
            'pagerURL' => static::PAGER_URL
        ];
    }

    public function buildResponse($data, $isSuccess = true)
    {
        $response = [
            'success' => $isSuccess,
            'message' => $isSuccess ? 'OK' : 'Error',
            'data' => $data
        ];
        return json_encode($response);
    }

    public function sendRejectionMail($data)
    {
        foreach ($data as $oid => $datum) {
            $type = $datum['remarks'];
            $mh = Loader::helper('mail');
            $mh->from(self::MAIL_FROM);
            $mh->to($datum['email']);
            $mh->addParameter('username', $datum['username']);
            $mh->addParameter('schoolName', $datum['schoolName']);
            $mh->load('' . $type, self::PACKAGE_HANDLE);
            $mh->sendMail();
        }
    }
}