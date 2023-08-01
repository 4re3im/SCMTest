<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('go_users/model', 'go_dashboard');
Loader::model('go_users/list', 'go_dashboard');

class DashboardGoSubscriptionsController extends Controller {

    public function on_start() {
        
    }

    public function view() {

        $teacherlist = new GoDashboardGoUsersList();

        $teacherlist->filterByGroups();
        $teacherlist->sortByCreatedDate();

        $this->set('teacherlist', $teacherlist->getPage());
        $this->set('teacherlistPagination', $teacherlist->displayPaging(false, true));

        // if ($_REQUEST['numResults']) {
        // 	$codefaillist->setItemsPerPage($_REQUEST['numResults']);
        // }

        if ($_POST['searchstring'] != '') {

            // need to sanitize this 
            $accesscode = $_POST['searchstring'];


            $list = new GoDashboardGoUsersList();


            if (isset($_POST['searchstring']) && strlen(trim($_POST['searchstring'])) > 0) {
                $list->filterByuID(trim($_POST['searchstring']));
            }

            // Tab setting using array
            $tabs = array(
                // array('tab-id', 'Tag Label', true=active)
                array('tab-1', 'General', true),
                array('tab-2', 'Tools'),
                array('tab-3', 'Subscriptions'),
                array('tab-4', 'Tracking General'),
                array('tab-5', 'Activation Errors'),
            );

            $this->set('tabs', $tabs);
            // $this->set('list', $list);		
            $this->set('records', $list->getPage());
            //$this->set('pagination', $list->getPagination());
        }
    }

    public function add() {
        
    }

    public function edit($id) {
        
    }

    public function delete($id) {
        
    }

}
