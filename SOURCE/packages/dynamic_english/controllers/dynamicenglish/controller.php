<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class DynamicEnglishController extends Controller {
    
    protected $pkgHandle = 'dynamic_english';

    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("dynamic_english_theme"));

        $this->html = Loader::helper('html', $this->pkgHandle);
    }
    
    public function view() {        
        $this->addHeaderItem($this->html->javascript('bootstrap.min.js', $this->pkgHandle));           
        $this->addHeaderItem($this->html->javascript('custom.js', $this->pkgHandle));

        // Added by Paul Balila, 12-05-2016
        // For ticket ANZGO-2516
        $this->addHeaderItem($this->html->javascript('validate.js', $this->pkgHandle));
        $this->addHeaderItem($this->html->css('validate.css', $this->pkgHandle));
        $this->addHeaderItem($this->html->css('dynamic_english.css', $this->pkgHandle));
    }

    public function processContactForm() {
        $data = $this->post();

        $mh = Loader::helper("mail");
        $data = $this->post();

        $mh->addParameter('firstName',$data['contactName']);
        $mh->addParameter('lastName',$data['contactLastName']);
        $mh->addParameter('email',$data['contactMail']);
        $mh->addParameter('confirmEmail',$data['contactConfirm']);
        $mh->addParameter('comment',$data['contactComments']);

        $mh->from($data['contactMail']);
        $mh->to('enquiries@cambridge.edu.au');

        $mh->load('contact',$this->pkgHandle);

        $mh->sendMail();

        echo 1; exit;
    }
}