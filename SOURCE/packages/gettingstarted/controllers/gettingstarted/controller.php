<?php
/**
 * ANZGO-3553 Add Getting Started Page
 * by Jeszy Tanada 10/23/2017
 */

defined('C5_EXECUTE') || die(_("Access Denied."));


class GettingStartedController extends Controller
{
    const PKG_HANDLE = 'gettingstarted';
    private $html;
    private $view;


    public function __construct()
    {
        $this->html = Loader::helper('html');
        $this->view = View::getInstance();
    }

    public function on_start()
    {
        parent::on_start();
        $this->view->setTheme(PageTheme::getByHandle('getting_started_theme'));
        $this->addHeaderStyle();
    }

    private function addHeaderStyle()
    {
        $this->addHeaderItem($this->html->css('bootstrap.min.css', self::PKG_HANDLE));
        $this->addHeaderItem($this->html->css('animate.css', self::PKG_HANDLE));
        $this->addHeaderItem($this->html->css('getstartedv2.css', self::PKG_HANDLE));
    }
}
