<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('subject/model', 'cup_content');
Loader::model('cup_content_search', 'cup_content');
Loader::helper('tools', 'cup_content');

class EducationSubjectsController extends Controller {

    public function view($subject_pretty_url = false, $department = false) {

        if ($subject_pretty_url) {
            $this->subjectView($subject_pretty_url, $department);
        } else {
            $this->redirect('/');
        }
    }

    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("education_theme"));
        CupContentToolsHelper::initialLocate();

        $this->error = Loader::helper('validation/error');
        $this->addHeaderItem(Loader::helper('html')->css('ccm.profile.css'));

        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('cup_content.css', 'cup_content'));


        $this->addHeaderItem(Loader::helper('html')->javascript('jquery.js'));
        $this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
        $this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
        $this->addFooterItem(Loader::helper('html')->javascript('jquery.form.js'));
        $this->addHeaderItem(Loader::helper('html')->javascript('jquery.ui.js'));
        $this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));

        $pkg = Package::getByHandle('core_commerce');
        if ($pkg->config('WISHLISTS_ENABLED')) {
            $this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.wishlist.js', 'core_commerce'));
            $this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.wishlist.css', 'core_commerce'));
        }

        if (!isset($_SESSION['DEFAULT_LOCALE'])) {
            $_SESSION['DEFAULT_LOCALE'] = 'en_AU';
        } elseif (!in_array($_SESSION['DEFAULT_LOCALE'], array('en_AU', 'en_NZ'))) {
            $_SESSION['DEFAULT_LOCALE'] = 'en_AU';
        }
    }

    public function on_before_render() {
        $this->set('error', $this->error);
    }

    public function subjectView($subject_pretty_url, $department = false) {
        $subject = CupContentSubject::fetchByPrettyUrl($subject_pretty_url);
        $this->set('subject', $subject);

        $criteria = array();

        $criteria['q_subject'] = $subject->name;

        $search = new CupContentSearch();
        $search->filterBySubject($subject->name);
        if ($department) {
            $criteria['q_department'] = $department;
            $search->filterByDepartment($department);
        }

        if (strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0) {
            $search->filterByRegion('Australia');
            if (isset($_GET['q_region'])) {
                if (strlen(trim($_GET['q_region'])) > 0) {
                    $search->filterByRegion($_GET['q_region']);
                    $criteria['q_region'] = $_GET['q_region'];
                } else {
                    $reload_criteria = true;
                }
            }
        } else {
            $search->filterByRegion('New Zealand');
        }

        $this->set('criteria', $criteria);

        $page_size = 10;

        if (isset($_GET['cc_sort'])) {
            $search->setSortBy('name', $_GET['cc_sort']);
        }

        if (isset($_GET['cc_page'])) {
            $search->setPageNumber($_GET['cc_page']);
        }

        if (isset($_GET['cc_size'])) {
            $page_size = $_GET['cc_size'];
        }

        $search->setPageSize($page_size);

        $this->set('page_size', $page_size);
        $this->set('search', $search);
        
        $this->render('/education/subjects/subject_view');
    }

}
