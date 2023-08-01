<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 20/06/2020
 * Time: 10:24 PM
 */

class CleanupTempData extends Job
{
    public function getJobName()
    {
        return t("Bulk Actions Temporary Data Cleanup");
    }

    public function getJobDescription()
    {
        return t("Truncates all temporary data from involved tables to maintain GDPR compliance.");
    }

    public function run()
    {
        Loader::model('bulk_delete', 'go_provisioning');
        $bdModel = new BulkDeleteModel();

        $deletedRecordsCount = $bdModel->removeTempData();
        $count = (!$deletedRecordsCount) ? 0 : $deletedRecordsCount;

        return t("${count} records deleted.");
    }
}
