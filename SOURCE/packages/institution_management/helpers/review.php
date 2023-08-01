<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 25/01/2021
 * Time: 10:31 PM
 */

Loader::library('hub-sdk/autoload');

use HubEntitlement\Models\Activation;

class ReviewHelper
{
    const PACKAGE_HANDLE = 'institution_management';
    private $filename;

    const TABLE_HEADERS = [
        'UID',
        'First Name',
        'Last Name',
        'Email',
        'GO Role',
        'Add/Remove admin'
    ];
    const GIGYA_MAX_LIMIT = 5000;
    const RESULT_LIMIT = 20;
    const PAGER_URL = '/dashboard/institution_management/review/navigate';

    private $accounts;
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
    private $oid;

    public function extractInstitution($result)
    {
        return $result['data']['results'][0];
    }

    public function format($result)
    {
        $institution = $this->extractInstitution($result);
        $data = $institution['data'];

        $fullAddress = implode(", ", [
            $data['formattedAddress'],
            @$data['addressCity'],
            @$data['addressRegion'],
            @$data['addressCountry']
        ]);

        $formatted = [
            'name' => @$data['name'],
            'oid' => $institution['oid'],
            'createdTime' => $institution['createdTime'],
            'lastUpdatedTime' => $institution['lastUpdatedTime'],
            'addressCity' => @$data['addressCity'],
            'addressCountry' => @$data['addressCountry'],
            'addressRegion' => @$data['addressRegion'],
            'formattedAddress' => $data['formattedAddress'],
            'url' => $data['url'],
            'telephone' => $data['telephone'],
            'fullAddress' => isset($data['addressCity']) ? $fullAddress : $data['formattedAddress'],
            'addressRegionCode' => $data['addressRegionCode'],
            'edueltTeacherCode' => @$data['edueltTeacherCode'],
            'addressCountryCode' => @$data['addressCountryCode'],
            'systemID' => @$data['systemID']
        ];

        return json_encode($formatted);
    }

    public function exportToS3($schoolDetails, $uids)
    {
        $id = Loader::helper('validation/identifier');
        Loader::library('gigya/CWSExport');
        $this->filename = 'dataload_go-' . date('YmdHis');
        $this->filename .= '-' . $id->getString(10);
        $this->filename .= '.json';

        $config = [
            'bucket' => CWS_GIGYA_S3_BUCKET,
            'filename' => $this->filename
        ];

        $exporter = new CWSExport($config);
        $exporter->setMode($exporter::ATTRIBUTE_INSTITUTION);
        $exporter->attributeInstituteRole($schoolDetails, $uids);
        return $exporter->exportToS3();
    }

    public function setSchedule()
    {
        Loader::library('gigya/GigyaSchedule');
        $schedule = new GigyaSchedule();
        $adminEmails = json_decode(GIGYA_BULK_ACTION_ADMIN_EMAILS, true);
        $adminEmailsString = implode(',', $adminEmails);

        $schedule->name = $this->filename;
        $schedule->successEmailNotification = $adminEmailsString;
        $schedule->failureEmailNotification = $adminEmailsString;
        $schedule->nextJobStartTime = date(
            'Y-m-d\TH:i:s.000\Z',
            strtotime('+30 seconds', strtotime(gmdate("Y-m-d H:i:s")))
        );
        $schedule->dataFlowId = CWS_GIGYA_SHARED_IMPORT_ID;
        $scheduleId = $schedule->save();
        return $scheduleId;
    }

    public function init($accounts, $oid, $role)
    {
        $this->accounts = $accounts;
        $this->oid = $oid;
        $this->results = $this->sortResults($this->accounts->getArray('results'));
        $this->objectsCount = $this->accounts->getInt('objectsCount');
        $this->totalCount = $this->accounts->getInt('totalCount');

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

    public function buildTable($role)
    {
        ob_start();
        Loader::packageElement(
            'review/table',
            static::PACKAGE_HANDLE,
            [
                'headers' => static::TABLE_HEADERS,
                'results' => $this->results,
                'totalCount' => $this->totalCount,
                'role' => $role
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
            'review/table_body',
            static::PACKAGE_HANDLE,
            [
                'headers' => static::TABLE_HEADERS,
                'results' => $this->results,
                'oid' => $this->oid,
                'totalCount' => $this->totalCount
            ]
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
            'commons/table_pager',
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

    public function updatePage($activePage = 1)
    {
        $this->activePage = $activePage;
    }

    public function sortResults($results) 
    {
        $json = json_decode($results);
        usort($json, function($a, $b)
        {
            return strcmp($a->profile->email, $b->profile->email);
        });
        return $json;
    }

    // SUBSCRIPTIONS functions

    /**
     * Toggles activation status of subscriptions
     *
     * @param $usIDs
     * @param bool $deactivate
     * @return bool
     */
    private function activate($usIDs, $deactivate = false)
    {
        $flag = true;
        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            $metadata = $subscription->metadata;
            if (!$deactivate) {
                $metadata['DateDeactivated'] = null;
            } else {
                $metadata['DateDeactivated'] = date('Y-m-d H:i:s');
            }
            $subscription->metadata = $metadata;
            $flag = $flag && $subscription->save();
            unset($subscription);
        }
        return $flag;
    }

    /**
     * Sets archived status of subscriptions.
     *
     * @param $usIDs
     * @return bool
     */
    private function archive($usIDs)
    {
        $flag = true;
        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            $metadata = $subscription->metadata;
            $metadata['DateDeactivated'] = date('Y-m-d H:i:s');
            $metadata['Archive'] = 'Y';
            $metadata['ArchivedDate'] = date('Y-m-d H:i:s');
            $subscription->metadata = $metadata;
            $flag = $flag && $subscription->save();
            unset($subscription);
        }
        return $flag;
    }

    private function setEndDate($usIDs, $endDate)
    {
        $flag = true;
        foreach ($usIDs as $usID) {
            $subscription = Activation::find($usID);
            $date = date_create($endDate);
            $subscription->ended_at = date_format($date, 'Y-m-d H:i:s');
            $flag = $flag && $subscription->save();
            unset($subscription);
        }
        return $flag;
    }

    public function updateInstitutionSubscriptions($param, $usIDs, $endDate = null)
    {
        switch ($param) {
            case 'activate':
                return $this->activate($usIDs);
            case 'archive':
                return $this->archive($usIDs);
            case 'deactivate':
                return $this->activate($usIDs, true);
            case 'endDate':
                return $this->setEndDate($usIDs, $endDate);
            default:
                return false;
        }
    }

    /**
     * Builds data for the subscriptions table body.
     *
     * @param $activations
     * @return array
     */
    private function generateSubscriptions($activations)
    {
        function unarchived($activation) {
            return $activation->Archive !== 'Y';
        }

        function buildSubscriptions($activation) {
            $permission = $activation->permission()->fetch();
            $entitlement = $permission->entitlement()->fetch();
            $product = $entitlement->product()->fetch();

            $active = 'Y';
            $daysRemaining = $activation->daysRemaining;
            if (!$daysRemaining || $activation->DateDeactivated) {
                $active = 'N';
            }

            return [
                'ID' => $activation->id,
                'CreationDate' => $activation->created_at->format('Y-m-d H:i:s'),
                'StartDate' => $activation->activated_at->format('Y-m-d H:i:s'),
                'EndDate' => $activation->ended_at->format('Y-m-d H:i:s'),
                'Duration' => $entitlement->Duration,
                'Active' => $active,
                'AccessCode' => $permission->proof,
                'PurchaseType' => $activation->PurchaseType,
                'DaysRemaining' => $activation->DaysRemaining,
                'CreatedBy' => $activation->CreatedBy,
                'SubType' => $entitlement->Type,
                'Subscription' => $product->CMS_Name
            ];
        }

        $unarchivedActivations = array_filter($activations, "unarchived");

        return array_map('buildSubscriptions', $unarchivedActivations);
    }

    public function getSubscriptionsTable($activations)
    {
        $subscriptions = $this->generateSubscriptions($activations);
        ob_start();
        Loader::packageElement(
            'review/subscriptions_table',
            static::PACKAGE_HANDLE,
            [
                'pkgHandle' => static::PACKAGE_HANDLE,
                'subscriptions' => $subscriptions,
                'showCreator' => true
            ]
        );
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}