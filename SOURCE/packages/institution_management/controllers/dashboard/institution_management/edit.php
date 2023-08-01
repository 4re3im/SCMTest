<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 27/01/2021
 * Time: 1:21 PM
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DashboardInstitutionManagementEditController extends Controller
{
    private $formHelper;
    private $reviewHelper;
    private $editHelper;
    private $notifHelper;
    private $gi;

    const PACKAGE_HANDLE = 'institution_management';

    const COMMONS_JS_VERSION = '1.0';
    const EDIT_SCRIPTS_JS_VERSION = '1.04';

    public function on_start()
    {
        $this->formHelper = Loader::helper('institute_form', static::PACKAGE_HANDLE);
        $this->reviewHelper = Loader::helper('review', static::PACKAGE_HANDLE);
        $this->editHelper = Loader::helper('edit', static::PACKAGE_HANDLE);
        $this->notifHelper = Loader::helper('notification', static::PACKAGE_HANDLE);

        Loader::library('gigya/datastore/GigyaInstitution');
        $this->gi = new GigyaInstitution();

        $html = Loader::helper('html');

        $editScriptsJsHref = (string)$html->javascript(
            'edit/scripts.js',
            static::PACKAGE_HANDLE)
            ->href;
        $editScriptsJsHref .= '?v=' . static::EDIT_SCRIPTS_JS_VERSION;

        $this->addFooterItem(
            '<script type="text/javascript" src="' . $editScriptsJsHref . '" ></script>'
        );

        $this->log = new Logger('Edit');
        $this->log->pushHandler(
            new StreamHandler('logs/edit_institution.' . date("Y-m-d", time()) .'.log', Logger::INFO)
        );
    }

    public function view($oid = false)
    {
        if (!$oid) {
            $this->redirect('/dashboard/institution_management/search');
        }
        $results = $this->gi->getByOID($oid);
        $institution = $this->reviewHelper->extractInstitution($results);
        $formatted = $this->editHelper->formatForDisplay($institution);
        $this->set('entry', $formatted);
        $this->set('oid', $oid);
    }

    public function setDetails($oid = false)
    {
        if (!$oid) {
            $this->redirect('/dashboard/institution_management/search');
        }

        $institution = $this->post('institution');

        Loader::helper('notification', static::PACKAGE_HANDLE);
        $nh = new NotificationHelper();

        $institution = $this->formHelper->sanitizeData($institution);
        $formattedData = $this->formHelper->formatForGigya($institution);

        unset($formattedData['oid']);

        $inS3Bucket = $this->editHelper->exportToS3([
            'data' => $formattedData,
            'oid' => $oid,
            'updateBehavior' => 'arraySet'
        ]);

        if (!$inS3Bucket) {
            $this->log->info(
                'Error to upload in S3 with oid ('. $oid .') time Start: '. date(DateTime::ISO8601)
            );
            echo $nh->getNotification('error', $nh::$ERROR_S3_BUCKET);
            die;
        }

        $this->log->info(
            'Uploaded to S3 with oids ('. $oid .') time Start: '. date(DateTime::ISO8601)
        );
        echo $nh->getNotification('success', $nh::$SUCCESS_GENERAL,
            ['oid' => $oid]);
        die;
    }

    public function getInstitution($oid)
    {
        $results = $this->gi->getByOID($oid);
        $institution = $this->reviewHelper->extractInstitution($results);
        $formatted = $this->editHelper->formatForDisplay($institution);

        ob_start();
        Loader::packageElement('commons/institute_form', static::PACKAGE_HANDLE, [
            'entry' => $formatted
        ]);
        $buffer = ob_get_contents();
        ob_end_clean();

        Loader::helper('notification', static::PACKAGE_HANDLE);
        $nh = new NotificationHelper();

        echo $nh->getNotification('success', $nh::$SUCCESS_GENERAL,
            ['form' => $buffer]);
        exit;
    }
}
