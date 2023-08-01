<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 31/07/2019
 * Time: 5:41 PM
 */

Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');

class GigyaJobStatus extends BaseGigya
{
    const IDX_SEARCH = 'idx.search';

    private $scheduleID;

    public function __construct($scheduleID = null)
    {
        if ($scheduleID) {
            $this->scheduleID = $scheduleID;
        }
    }

    public function getStatusByScheduleId($scheduleId = null)
    {
        $resp = $this->getJobByScheduleID($scheduleId);

        $status = 'pending';

        if($resp) {
            $result = $resp->getArray('result');

            if($result) {
                $status = $result
                    ->getObject('0')
                    ->getString('status');
            }
        }

        return $status;
    }

    public function getJobIDByScheduleID()
    {
        $resp = $this->getJobByScheduleID();
        $jobID = false;

        if ($resp) {
            $result = $resp->getArray('result');

            if ($result) {
                $jobID = $result
                    ->getObject('0')
                    ->getString('id');
            }
        }

        return $jobID;
    }

    public function getJobByScheduleID($scheduleId = null)
    {
        $this->setAPIKey(GIGYA_MIGRATION_API_KEY);

        $schedID = ($scheduleId === null) ? $this->scheduleID : $scheduleId;

        $query = "select * from idx_job_status where schedulingId = '$schedID'";

        return $this->search($query);
    }

    public function search($query)
    {
        $request = $this->newRequest(static::IDX_SEARCH);
        $request->setParam('query', $query);

        return $this->handleResponse($request->send());
    }
}