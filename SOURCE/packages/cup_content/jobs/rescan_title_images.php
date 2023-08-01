<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 07/11/2020
 * Time: 5:32 PM
 */

class RescanTitleImages extends QueueableJob
{
    private $skippedImages = 0;
    private $processedImages = 0;
    private $skipped = [];
    private $processed = [];

    public function getJobName()
    {
        return t("Global Go Title Images Rescanner");
    }

    public function getJobDescription()
    {
        return t("Scans all title images and creates a 260 x 260 copy");
    }

    public function start(Zend_Queue $q)
    {
        Loader::model('title/simple_model', 'cup_content');
        $simpleModel = new SimpleModel();
        $titleData = $simpleModel->getAllTitleIDs();

        foreach ($titleData as $data) {
            $q->send($data['id']);
        }
    }

    public function finish(Zend_Queue $q)
    {
        return t('Processed images: ' . $this->processedImages . ' Skipped images: ' . $this->skippedImages);
    }

    public function processQueueItem(Zend_Queue_Message $msg)
    {
        Loader::model('title/model', 'cup_content');
        $id = $msg->body;
        $title = new CupContentTitle($id);
        $filename = TITLE_IMAGES_FOLDER . $title->isbn13;

        if (!file_exists($filename)) {
            $this->skippedImages++;
            $this->skipped[] = $id;
            $title->saveThumbnailURL();
        } else {
            $globalGoFileName = $title->saveGlobalGoImage($filename);
            $title->saveThumbnailURL($globalGoFileName);
            $this->processedImages++;
            $this->processed[] = $id;
        }
    }

    private function getAllTitleIDs()
    {

    }

}