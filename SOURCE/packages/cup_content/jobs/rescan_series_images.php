<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 27/03/2020
 * Time: 3:25 PM
 */

class RescanSeriesImages extends QueueableJob
{
    private $skippedImages = 0;
    private $processedImages = 0;

    public function getJobName()
    {
        return t("Global Go Series Images Rescanner");
    }

    public function getJobDescription()
    {
        return t("Scans all series images and creates a 260 x 260 copy");
    }

    public function start(Zend_Queue $q)
    {
        Loader::model('series/list', 'cup_content');
        $seriesList = new CupContentSeriesList();
        $totalSeries = $seriesList->getTotal();
        $series = $seriesList->get($totalSeries);

        if (!is_dir(SERIES_IMAGES_FOLDER)) {
            mkdir(SERIES_IMAGES_FOLDER, 0777, true);
        }

        foreach ($series as $s) {
            $q->send($s->id);
        }
    }

    public function finish(Zend_Queue $q)
    {
        return t('Processed images: ' . $this->processedImages . ' Skipped images: ' . $this->skippedImages);

    }

    public function processQueueItem(Zend_Queue_Message $msg)
    {
        Loader::model('series/model', 'cup_content');
        $id = $msg->body;
        $series = new CupContentSeries($id);
        $filename = SERIES_IMAGES_FOLDER . $series->seriesID . '.png';

        if (!file_exists($filename)) {
            $this->skippedImages++;
            $series->saveThumbnailURL();
        } else {
            $globalGoFileName = $series->saveGlobalGoImage($filename);
            $series->saveThumbnailURL($globalGoFileName);
            $this->processedImages++;
        }
    }
}