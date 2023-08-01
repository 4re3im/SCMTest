<?php
/**
 * ANZGO-3553 Added by Jeszy Tanada 10/24/2017
 * Getting Started page package controller
 */
defined('C5_EXECUTE') || die(_("Access Denied."));

class GettingStartedPackage extends Package
{
    protected $pkgHandle = 'gettingstarted';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.1';

    protected $themes = array('getting_started_theme');

    protected $singlePages = array(
      '/gettingstarted' => array('cName' => 'Getting Started Page', 'cDescription' => 'Cambridge GO Getting Started')
    );

    public function getPackageDescription()
    {
        return t("Cambridge GO Getting Started");
    }

    public function getPackageName()
    {
        return t("Getting Started Package");
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);
    }

    public function upgrade()
    {
        $pkg = Package::getByHandle($this->pkgHandle);
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
    }

    public function installThemes($pkg)
    {
        foreach ($this->themes as $theme) {
            PageTheme::add($theme, $pkg);
        }
    }

    protected function installSinglePages($pkg)
    {
      Loader::model('single_page');
      foreach ($this->singlePages as $path => $attr) {
        $page = SinglePage::add($path, $pkg);
        $page->update($attr);
      }
    }
}
