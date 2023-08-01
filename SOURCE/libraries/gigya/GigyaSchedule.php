<?php

Loader::library('gigya/GSSDK');
Loader::library('gigya/BaseGigya');

class GigyaSchedule extends BaseGigya
{
    const METHOD_CREATE_SCHEDULE = 'idx.createScheduling';

    public $id;
    public $name;
    public $dataFlowId;
    public $frequencyType = 'once';
    public $frequencyInterval;
    public $nextJobStartTime;
    public $successEmailNotification;
    public $failureEmailNotification;
    public $limit;
    public $fullExtract;
    public $enabled = true;
    public $schedulingStatus = 'ready';

    public function toJSON()
    {
        return json_encode($this);
    }

    public function save()
    {
        $this->setAPIKey(GIGYA_MIGRATION_API_KEY);
        $request = $this->newRequest(static::METHOD_CREATE_SCHEDULE);
        $request->setParam('data', $this->toJSON());
        $response = $request->send();

        if ($response->getInt('errorCode') > 0) {
            return false;
        }

        return $this->id = $response->getString('id');
    }

    public function getSchedulesCount()
    {
        $query = "SELECT * FROM scheduling WHERE dataflowId = '" . GIGYA_MIGRATION_MASTER_DATAFLOW_ID . "'";
        $response = $this->search($query);
        return $response->getInt('resultCount');
    }

    public function search($query)
    {
        $request = $this->newRequest(static::METHOD_IDX_SEARCH);
        $request->setParam('query', $query);

        return $this->handleResponse($request->send());
    }
}
