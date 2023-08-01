<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

class DashboardInstitutionManagementSearchController extends Controller
{
    private $searchHelper;

    const PACKAGE_HANDLE = 'institution_management';
    const SCRIPTS_JS_VERSION = '1.3';

    public function on_start()
    {
        $this->searchHelper = Loader::helper(
            'search',
            static::PACKAGE_HANDLE
        );

        $html = Loader::helper('html');
        $scriptsJsHref = (string)$html->javascript(
            'search/scripts.js',
            static::PACKAGE_HANDLE)
            ->href;
        $scriptsJsHref .= '?v=' . static::SCRIPTS_JS_VERSION;

        $this->addFooterItem(
            '<script type="text/javascript" src="' . $scriptsJsHref . '" ></script>'
        );
        
    }
    public function view()
    {
        
    }

    public function getInstitutions($page = 1)
    {
        Loader::library('gigya/datastore/GigyaInstitution');
        $keyword = $this->get('keyword');
        $filter = $this->get('filter');
        $options = ['page' => $page, 'limit' => 15];
        $gi = new GigyaInstitution();
        // SB-1089 added by mabrigos - added filter as param
        $results = $gi->getValidatedInstitutions($keyword, true, $options, $filter);
        echo $this->searchHelper->displayResults($results);
        die;
    }

    public function navigate($page = 1)
    {
        Loader::library('gigya/datastore/GigyaInstitution');
        $keyword = $this->get('keyword');
        $filter = $this->get('filter');
        $options = ['page' => $page, 'limit' => 15];
        $gi = new GigyaInstitution();
        // SB-1089 added by mabrigos - added filter as param
        $results = $gi->getValidatedInstitutions($keyword, true, $options, $filter);

        $this->searchHelper->setActivePage($page);
        echo $this->searchHelper->navigateResults($results);
        die;
    }

    // SB-1117 added by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    public function remove_rejection($oid) 
    {
        $this->rpModel = new RejectedInstitutionsModel();
        $result = $this->rpModel->deleteRejectedInstitutionByOid($oid);
        echo json_encode(array('result' => $result, 'error' => $this->rpModel->errors));
        die;
    }
}
