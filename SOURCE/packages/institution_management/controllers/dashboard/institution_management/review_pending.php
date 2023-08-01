<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 25/06/2021
 * Time: 11:28 PM
 */

Loader::library('gigya/datastore/GigyaInstitution');

class DashboardInstitutionManagementReviewPendingController extends Controller
{
    const LIMIT = 10;
    const PACKAGE_HANDLE = 'institution_management';
    const REVIEW_PENDING_SCRIPTS_VERSION = '1.4';
    const REVIEW_PENDING_CSS_VERSION = '1.1';

    private $gigyaAccount;
    private $gigyaInstitution;
    private $rpHelper;
    private $rpModel;


    public function on_start()
    {
        $this->gigyaInstitution = new GigyaInstitution();
        $this->gigyaAccount = new GigyaAccount();
        $this->rpHelper = Loader::helper(
            'review_pending',
            self::PACKAGE_HANDLE
        );
        Loader::model(
            'rejected_institutions',
            self::PACKAGE_HANDLE
        );
        $html = Loader::helper('html');

        $reviewPendingStylesCssHref = (string)$html->css(
            'review_pending/styles.css',
            static::PACKAGE_HANDLE
        )->file;
        $reviewPendingStylesCssHref .= '?v=' . static::REVIEW_PENDING_CSS_VERSION;
        $this->addHeaderItem(
            '<link rel="stylesheet" href="' . $reviewPendingStylesCssHref . '">'
        );

        $reviewPendingScriptsJsHref = (string)$html->javascript(
            'review_pending/scripts.js',
            static::PACKAGE_HANDLE
        )->href;
        $reviewPendingScriptsJsHref .= '?v=' . static::REVIEW_PENDING_SCRIPTS_VERSION;

        $this->addFooterItem(
            '<script type="text/javascript" src="' . $reviewPendingScriptsJsHref . '" ></script>'
        );
    }

    public function view()
    {
        $displayData = $this->rpHelper->getInstitutions(1);
        $pagerData = $this->rpHelper->getPagerData();

        $this->set('data', $displayData);
        $this->set('pager', $pagerData);
    }

    public function save()
    {
        $this->rpModel = new RejectedInstitutionsModel();
        $institutions = $this->post('institution');
        $this->rpModel->insertMultipleInstitutions($institutions);
        $this->rpHelper->sendRejectionMail($institutions);
        die;
    }

    public function searchInstitution($page = 1)
    {
        $keyword = $this->get('keyword');
        $filter = $this->get('filter');
        $displayData = $this->rpHelper->getInstitutions($page, $keyword, $filter);
        $tableBody = $this->rpHelper->buildTable($displayData);
        $pager = $this->rpHelper->buildPager();
        echo $this->rpHelper->buildResponse(
            [
                'tableBody' => $tableBody,
                'pager' => $pager
            ],
            true
        );
        die;
    }

    public function navigate($page = 1)
    {
        $keyword = $this->get('keyword');
        $filter = $this->get('filter');
        $displayData = $this->rpHelper->getInstitutions($page, $keyword, $filter);
        $tableBody = $this->rpHelper->buildTable($displayData);
        $pager = $this->rpHelper->buildPager();
        echo $this->rpHelper->buildResponse(
            [
                'tableBody' => $tableBody,
                'pager' => $pager
            ],
            true
        );
        die;

    }
}