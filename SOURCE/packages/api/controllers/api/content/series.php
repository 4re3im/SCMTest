<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../base_controller.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../restApi_interface.php";

class APIContentSeriesController extends BaseController implements RestAPI
{
    private $series;

    public function on_start()
    {
        parent::on_start();
        Loader::model('content/series', $this->pkgHandle);
    }

    public function create($id = null)
    {
        // SB-269 added by machua 20190726 to check if there is an existing series on TNG before creation
        $this->series = new ContentSeries();
        $this->series->loadBySeriesID($_POST['seriesID']);

        if ($this->series->id) {
            $seriesPK = $this->series->id;
            $this->series->unserialize($this->post());
            $this->series->id = $seriesPK;
            
            if ($this->series->save()) {
                $data = ['id' => $this->series->id, 'seriesID' => $this->series->seriesID];
                $this->setAPIResponse(true, 'Series has been updated.', $data);
            } else {
                $errorList = array($this->series->ErrorMsg());
                $this->setErrorAPIResponse('Series has not been updated.', $errorList);
            }
        } else {
            $this->series = new ContentSeries($this->post());
            $this->series->__set('id', null);
            $this->series->__set('isEnabled', '0');
            $this->series->__set('createdAt', date('Y-m-d H:i:s'));
            $this->series->__set('modifiedAt', date('Y-m-d H:i:s'));

            if ($this->series->save()) {
                $data = ['id' => $this->series->id, 'seriesID' => $this->series->seriesID];
                $this->setAPIResponse(true, 'Series has been added.', $data);
            } else {
                $errorList = array($this->series->ErrorMsg());
                $this->setErrorAPIResponse('Series not saved.', $errorList);
            }
        }
        
    }

    public function read($id = null)
    {
        $this->series = new ContentSeries();
        $this->series->loadBySeriesID($id);

        if ($this->series->id) {
            $this->setAPIResponse(true, 'Series found.', $this->series->serialize());
        } else {
            $errorList = array($this->series->ErrorMsg());
            $this->setErrorAPIResponse('Series not found.', $errorList);
        }
    }

    public function update($id = null)
    {
        $this->series = new ContentSeries();
        $this->series->loadBySeriesID($_POST['seriesID']);

        // SB-269 added by machua 20190730 to create a series if it does not exist
        if ($this->series->id) {
            $seriesPK = $this->series->id;
            $this->series->unserialize($this->post());
            $this->series->id = $seriesPK;

            if ($this->series->save()) {
                $data = ['id' => $this->series->id, 'seriesID' => $this->series->seriesID];
                $this->setAPIResponse(true, 'Series has been updated.', $data);
            } else {
                $errorList = array($this->series->ErrorMsg());
                $this->setErrorAPIResponse('Series has not been updated.', $errorList);
            }
        } else {
            $this->series = new ContentSeries($this->post());
            $this->series->__set('id', null);
            $this->series->__set('isEnabled', '0');
            $this->series->__set('createdAt', date('Y-m-d H:i:s'));
            $this->series->__set('modifiedAt', date('Y-m-d H:i:s'));

            if ($this->series->save()) {
                $data = ['id' => $this->series->id, 'seriesID' => $this->series->seriesID];
                $this->setAPIResponse(true, 'Series has been added.', $data);
            } else {
                $errorList = array($this->series->ErrorMsg());
                $this->setErrorAPIResponse('Series not saved.', $errorList);
            }
        }
        
    }

    public function delete($id = null)
    {
        $this->series = new ContentSeries();
        $this->series->loadBySeriesID($id);

        if ($this->series->Delete()) {
            $this->setAPIResponse(true, 'Series has been deleted.');
        } else {
            $errorList = array($this->series->ErrorMsg());
            $this->setErrorAPIResponse('Series has not been deleted.', $errorList);
        }
    }
}