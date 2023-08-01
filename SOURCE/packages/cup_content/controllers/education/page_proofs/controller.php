<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class EducationPageProofsController extends Controller {

    public function on_start() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content'));
        $this->addHeaderItem($html->css('cup_content.css', 'cup_content'));
        $this->addHeaderItem($html->css('page_proofs.css', 'cup_content'));

        if (!isset($_SESSION['DEFAULT_LOCALE'])) {
            $_SESSION['DEFAULT_LOCALE'] = 'en_AU';
        } elseif (!in_array($_SESSION['DEFAULT_LOCALE'], array('en_AU', 'en_NZ'))) {
            $_SESSION['DEFAULT_LOCALE'] = 'en_AU';
        }

        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("education_theme"));
    }

    public function view($subject = false) {
        Loader::model('title/list', 'cup_content');
        $list = new CupContentTitleList();
        $list->filterByIsEnabled();
        $list->filterByHasSamplePages();



        $criteria = array();
        $criteria['base_url'] = 'page_proofs';

        if ($subject) {
            Loader::model('subject/model', 'cup_content');
            $subjectObj = CupContentSubject::fetchByPrettyUrl($subject);
            $list->filterBySubject($subjectObj->name);
            $criteria['subject_prettyUrl'] = $subject;
        }

        $selected_region = 'New Zealand';
        if (strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0) {
            $selected_region = 'All Australia';
            if (isset($_SESSION['inspection_copy']['filter_region'])) {
                $selected_region = $_SESSION['inspection_copy']['filter_region'];
            }
            $list->filterByRegion($selected_region);
        } else {
            $list->filterByRegion($selected_region);
        }

        if (isset($_GET['cc_sort'])) {
            $list->sortBy('name', $_GET['cc_sort']);
        }

        if (isset($_GET['q_department'])) {
            $list->filterByDepartment($_GET['q_department']);
        } else {
            /* Secondary as default */
            $list->filterByDepartment('Secondary');
            $_GET['q_department'] = 'Secondary';
        }

        $list->filterByHasPageProof();


        $page_size = 10;
        if (isset($_GET['cc_size'])) {
            $page_size = $_GET['cc_size'];
        }
        $list->setItemsPerPage($page_size);



        if (isset($_GET['ajax']) && $_GET['ajax'] == 'yes') {
            Loader::element('frontend/page_proofs_result', array('list' => $list, 'criteria' => $criteria), 'cup_content');
            exit();
        }



        $this->set('criteria', $criteria);
        $this->set('selected_region', $selected_region);
        $this->set('list', $list);
    }

}
