<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

/**
 * Trial Block Controller
 * GCAP-1286 Added by mabrigos 20210428
 */
Loader::library('hub-sdk/autoload');

class DashboardTrialsReportController extends Controller
{
    private $tModel;
    private $pkgHandle = 'go_trials';

    public function __construct()
    {
        parent::__construct();
        Loader::model('trials', $this->pkgHandle);
        $this->tModel = new TrialsModel();
    }

    public function on_start()
    {
        $htmlHelper = Loader::helper('html');
        $cssPath = (string)$htmlHelper->css('styles.css', $this->pkgHandle)->file . "?v=1";
        $jsPath = (string)$htmlHelper->javascript('scripts.js', $this->pkgHandle)->file . "?v=1.4";

        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $cssPath . '">');
        $this->addFooterItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
        $this->addFooterItem($htmlHelper->javascript('plugins/malsup/jquery.form.min.js', $this->pkgHandle));
    }

    public function fetchActivations()
    {
        Loader::helper('report', $this->pkgHandle);
        $reportHelper = new ReportHelper();
        $filters = $reportHelper->sanitizeFilters($_POST);
        $products = array_combine($_POST['entitlementIds'], $_POST['name']);
        $entitlementIds = $_POST['entitlementIds'];
        $results = $this->tModel->getActivationsByEntitlementId($entitlementIds, $filters, $products);

        if (count($results) === 0) {
            echo $reportHelper->formatReturnData(false, null);
            exit;
        }

        echo $reportHelper->formatReturnData(true, 'activations', $results);
        exit;
    }

    public function fetchGigyaDetails()
    {
        Loader::helper('report', $this->pkgHandle);
        $country = $_POST['country'];
        $reportHelper = new ReportHelper();
        $activations = $_POST['activations'];
        $results = $this->tModel->mergeGigyaDetailsWithActivations($activations, $country);

        if (count($results) === 0) {
            echo $reportHelper->formatReturnData(false, null);
            exit;
        }

        echo $reportHelper->formatReturnData(true, 'gigya', $results);
        exit;
    }

    public function exportToCSV()
    {
        $filename = "TrialActivationsReport_" . date("d/m/Y");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $fp = fopen('php://output', 'wb');
        $headers = array('Gigya UID', 'Email', 'First name', 'Last name', 
                            'Institution', 'Country', 'Entitlement ID', 
                            'Product name', 'Date added', 'Days Remaining'
                        );

        $csvData = $_SESSION['csvData'];
        array_unshift($csvData, $headers);

        foreach ($csvData as $lines) {
            fputcsv($fp, $lines);
        }

        fclose($fp);
        unset($_SESSION['csvData']);
        exit;
    }

}

