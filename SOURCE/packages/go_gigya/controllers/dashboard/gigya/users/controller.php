<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 17/05/2019
 * Time: 10:33 AM
 */
Loader::library('gigya/GigyaAccount');


class DashboardGigyaUsersController extends Controller
{
    const PACKAGE_NAME = 'go_gigya';
    const LIMIT = 15;
    private $tableHelper;
    private $subjectsTaughtHelper;

    public function on_start()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem(
            '<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
                'dashboard/gigya/users.css',
                static::PACKAGE_NAME
            )->href . '?v=2"></link>');

        $this->addFooterItem(
            '<script src="' . (string)$html->javascript(
                'dashboard/gigya/users.js',
                static::PACKAGE_NAME
            )->href . '?v=2"></script>');

        Loader::helper('gigya_users_table', static::PACKAGE_NAME);
        Loader::helper('subjects_taught',static::PACKAGE_NAME);

        $this->tableHelper = new GigyaUsersTableHelper();
        $this->subjectsTaughtHelper = new SubjectsTaughtHelper();
    }

    public function view()
    {
        $this->set('gigyaTable', $this->tableHelper->getInitialTable());
    }

    public function loadTable()
    {
        $gigyaAcct = new GigyaAccount();
        $accounts = $gigyaAcct->searchAllGoTeachers(static::LIMIT);

        $this->subjectsTaughtHelper->setAccounts($accounts);
        $subjectsTaught = $this->subjectsTaughtHelper->getPerTeacher();

        $this->tableHelper->init($accounts);
        $this->tableHelper->setSubjectsTaught($subjectsTaught);

        $data = [
            'tableBody' => $this->tableHelper->loadBody(),
            'pager' => $this->tableHelper->loadPager()
        ];
        echo json_encode($data);
        die;
    }

    public function navigateTable($page)
    {
        $gigyaAcct = new GigyaAccount();
        $accounts = $gigyaAcct->searchAllGoTeachers(static::LIMIT, $page);

        $this->subjectsTaughtHelper->setAccounts($accounts);
        $subjectsTaught = $this->subjectsTaughtHelper->getPerTeacher();

        $this->tableHelper->update($accounts, $page);
        $this->tableHelper->setSubjectsTaught($subjectsTaught);

        $data = [
            'tableBody' => $this->tableHelper->loadBody(),
            'pager' => $this->tableHelper->loadPager()
        ];
        echo json_encode($data);
        die;
    }

    public function searchByEmail()
    {
        $term = $this->get('term');
        $gigyaAcct = new GigyaAccount();
        $result = $gigyaAcct->getProfileByEmail($term);
        $data = ['No results found...'];

        $profile = json_decode($result['profile']);
        if($result) {
            $data = [
                'label' => $result['UID'],
                'value' => $profile->email,
                'name' => "$profile->firstName $profile->lastName"
            ];
        }

        $formatted = [$data];

        echo json_encode($formatted);
        die;
    }

}
