<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

Loader::model('event/model', 'cup_competition');
Loader::model('event_entry/model', 'cup_competition');
Loader::model('event_entry/list', 'cup_competition');

class EducationCompetitionController extends Controller {

    // ANZGO-1873
    public function __construct() {

        header('Location: https://www.cambridge.edu.au/education/checkpoints/hsc/');
        
    }

    public function on_start() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('webfontkit/stylesheet.css'));
        $this->addHeaderItem($html->css('competition.css', 'cup_competition'));
        $this->set('theme_competition', true);
    }

    public function view() {
        $html = Loader::helper('html');

        $this->addHeaderItem($html->css('slider.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('slider.js', 'cup_competition'));

        if (CupCompetitionEvent::fetchOneByCategory('HSC')) {
            $this->redirect('/education/competition/hsc');
        } elseif (CupCompetitionEvent::fetchOneByCategory('VCE')) {
            $this->redirect('/education/competition/vce');
        } else {
            $this->render('/education/competition/view_null');
        }
    }

    public function hsc() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('slider.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('slider.js', 'cup_competition'));

        $event = CupCompetitionEvent::fetchOneByCategory('HSC');
        if ($event === false) {
            $this->redirect('/education/competition');
        }

        $this->set('eventObj', $event);
        $this->set('category', 'hsc');
        //$this->render('/dashboard/cup_competition/config/hsc_slider');
    }

    public function vce() {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('slider.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('slider.js', 'cup_competition'));

        $event = CupCompetitionEvent::fetchOneByCategory('VCE');
        if ($event === false) {
            $this->redirect('/education/competition');
        }
        $this->set('category', 'vce');
        $this->set('eventObj', $event);
    }

    public function entry_form($category = false) {
        if (!$category) {
            $this->redirect('/education/competition');
        }

        $html = Loader::helper('html');

        $event = CupCompetitionEvent::fetchOneByCategory(strtoupper($category));
        $entry = new CupCompetitionEventEntry();
        $entry->eventID = $event->id;

        $this->set('category', $category);
        $this->set('eventObj', $event);

        $errors = array();
        if (count($this->post()) > 0) {
            $post = $this->post();
            $entry->setPost($this->post());

            $captcha = Loader::helper('validation/captcha');
            if (!$captcha->check('ccmCaptchaCode')) {
                $errors[] = 'CAPTCHA incorrect! please try again.';
            } elseif (!isset($post['agree_terms_and_conditions'])) {
                $errors[] = 'Terms and conditions must be accepted';
            } else {
                if ($entry->save()) {
                    $this->on_entry_submission($event, $entry);

                    $this->render('/competition/entry_form_success');
                    return;
                } else {
                    $errors = $entry->getErrors();
                }
            }


            /*
              if($entry->save()){
              echo 'success';
              }else{
              print_r($entry->getErrors());
              }
              //print_r($this->post());
              exit();
             */
        }

        $this->set('errors', $errors);
        $this->set('entryObj', $entry);
        $this->render('/education/competition/entry_form');
    }

    public function testEmail() {
        $entry = new CupCompetitionEventEntry(19);
        $event = $entry->getEventObject();
        $this->on_entry_submission($event, $entry);
        exit("finished");
    }

    protected function on_entry_submission($event, $entry) {

        $pkg = Package::getByHandle('cup_competition');
        //$category = "";
        $admin_email = false;
        $category = array($event->category); /* for some werid reason, magic function return a boolean */
        $category = $category[0];
        if (strcmp(strtoupper($category), 'HSC') == 0 && $pkg->config('HSC_NOTIFICATION_EMAIL')) {
            $admin_email = $pkg->config('HSC_NOTIFICATION_EMAIL');
        } elseif (strcmp(strtoupper($category), 'VCE') == 0 && $pkg->config('VCE_NOTIFICATION_EMAIL')) {
            $admin_email = $pkg->config('VCE_NOTIFICATION_EMAIL');
        }

        $mh = Loader::helper('mail');
        $mh->addParameter('entryObj', $entry);
        $mh->addParameter('eventObj', $event);
        $mh->to($entry->email);
        $mh->from($admin_email);
        $mh->load('notification_on_submission', 'cup_competition');
        @$mh->sendMail();


        if (strcmp(strtoupper($category), 'HSC') == 0 && $pkg->config('HSC_NOTIFICATION_EMAIL')) {
            $admin_email = $pkg->config('HSC_NOTIFICATION_EMAIL');
            $mh = Loader::helper('mail');
            $mh->addParameter('entryObj', $entry);
            $mh->addParameter('eventObj', $event);
            $mh->to($admin_email);
            $mh->load('notification_on_submission_admin', 'cup_competition');
            @$mh->sendMail();
        }

        if (strcmp(strtoupper($category), 'VCE') == 0 && $pkg->config('VCE_NOTIFICATION_EMAIL')) {
            $admin_email = $pkg->config('VCE_NOTIFICATION_EMAIL');
            $mh = Loader::helper('mail');
            $mh->addParameter('entryObj', $entry);
            $mh->addParameter('eventObj', $event);
            $mh->to($admin_email);
            $mh->load('notification_on_submission_admin', 'cup_competition');
            @$mh->sendMail();
        }
    }

    public function terms_and_conditions($category = false) {
        if (!$category) {
            $this->redirect('/education/competition');
        }

        //echo "Terms & Conditions :".$category;
        //exit();
        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('slider.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('slider.js', 'cup_competition'));

        $event = CupCompetitionEvent::fetchOneByCategory(strtoupper($category));
        $this->set('category', $category);
        $this->set('eventObj', $event);
        $this->render('/education/competition/terms_and_conditions');
    }

    public function gallery($category = false) {
        if (!$category) {
            $this->redirect('/education/competition');
        }

        $html = Loader::helper('html');
        $this->addHeaderItem($html->css('slider.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('slider.js', 'cup_competition'));

        $this->addHeaderItem($html->css('jquery.wspecial.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_competition'));

        $this->addHeaderItem($html->css('prettyPhoto.css', 'cup_competition'));
        $this->addHeaderItem($html->javascript('jquery.prettyPhoto.js', 'cup_competition'));

        $event = CupCompetitionEvent::fetchOneByCategory(strtoupper($category));
        if (strcmp($event->type, 'Photo') != 0) {
            $this->redirect('/education/competition/' . strtolower($event->category));
            return;
        }

        $list = new CupCompetitionEventEntryList();
        $list->filterByEventID($event->id);
        $list->filterByStatus('approved');
        $list->sortBy('id', 'desc');

        if ($_REQUEST['page_size']) {
            $list->setItemsPerPage($_REQUEST['page_size']);
        } else {
            $list->setItemsPerPage(12);
        }


        if (isset($_GET['ajax'])) {
            Loader::packageElement('event_entry/frontend_list', 'cup_competition', array('entryList' => $list));
            exit();
        }


        $this->set('category', $category);
        $this->set('eventObj', $event);
        $this->set('entryList', $list);
        $this->render('/education/competition/gallery');
    }

}
