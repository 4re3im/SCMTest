<?php

defined('C5_EXECUTE') || die(_('Access Denied.'));

class DashboardInstitutionManagementAddController extends Controller
{
    private $gi;
    private $formHelper;
    private $notifHelper;

    const PACKAGE_HANDLE = 'institution_management';

    public function on_start()
    {
        $this->formHelper = Loader::helper('institute_form', static::PACKAGE_HANDLE);
        $this->notifHelper = Loader::helper('notification', static::PACKAGE_HANDLE);

        Loader::library('gigya/datastore/GigyaInstitution');
        $this->gi = new GigyaInstitution();
    }

    public function create()
    {
        $institution = $this->post('institution');

        if (!$institution) {
            $this->redirect('/dashboard/institution_management/add');
        }

        $institution = $this->formHelper->sanitizeData($institution);

        $formattedData = $this->formHelper->generateJSON($institution);
        $response = $this->gi->add($formattedData);

        $validationErrors = $response['data']['validationErrors'];

        if (empty($validationErrors)) {
            $this->notifHelper->setNotification('success', 'New Institution has been added successfully');
            $this->redirect('/dashboard/institution_management/review/' . $response['data']['oid']);
        } else {
            $this->notifHelper->setGigyaNotification('error', $validationErrors);
            $this->set('entry', $formattedData);
        }
    }
}
