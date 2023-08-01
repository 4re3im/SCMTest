<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 25/01/2021
 * Time: 10:31 PM
 */

class EditHelper
{
    private $filename;
    private $oid;

    public function formatForDisplay($institution)
    {
        $formatted = $institution['data'];
        $formatted['oid'] = $institution['oid'];

        $formattedAddress = @$formatted['formattedAddress'];
        $newFormattedAddress = preg_replace('/\r/', '#FA#', $formattedAddress);
        $addressArr = explode('#FA#', $newFormattedAddress);

        $formatted['addressLine1'] = $addressArr[1];
        $formatted['addressLine2'] = $addressArr[2];
        return $formatted;
    }

    public function setOID($oid)
    {
        $this->oid = $oid;
    }

    public function exportToS3($institution)
    {
        $id = Loader::helper('validation/identifier');
        Loader::library('gigya/CWSExport');
        $this->filename = 'sr_institution_go-' . date('YmdHis');
        $this->filename .= '-' . $id->getString(10);
        $this->filename .= '.json';

        $config = [
            'bucket' => CWS_GIGYA_S3_BUCKET,
            'filename' => $this->filename,
            'data' => $institution,
        ];
        $exporter = new CWSExport($config);
        $exporter->setMode($exporter::EDIT_INSTITUTION);
        return $exporter->exportToS3();
    }

    public function createSchedule()
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
        $schedule->dataFlowId = CWS_GIGYA_IMPORT_SYSREF_ID;

        $scheduleId = $schedule->save();
        return $scheduleId;
    }
}