<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class IceemPackage extends Package {

    protected $pkgHandle = 'iceem';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0';

    protected $themes = array('iceem_theme');

    protected $singlePages = array(
      '/iceem' => array('cName' => 'ICE-EM', 'cDescription' => 'ICE-EM Landing page')
    );

    public function getPackageDescription() {
        return t("ICE-EM package");
    }

    public function getPackageName() {
        return t("ICE-EM Package");
    }

    public function install() {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);
    }

    public function upgrade() {
        $pkg = Package::getByHandle('go_theme');
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
    }

    public function installThemes($pkg) {
        foreach ($this->themes as $theme) {
            PageTheme::add($theme,$pkg);
        }
    }

    protected function installSinglePages($pkg){
      Loader::model('single_page');
      foreach ($this->singlePages as $path => $attr) {
        $page = SinglePage::add($path, $pkg);
        $page->update($attr);
      }
    }
}
