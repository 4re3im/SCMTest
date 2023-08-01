<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class CambridgeSeniorMathsPackage extends Package
{

    protected $pkgHandle = 'cambridge_senior_maths';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.0';

    protected $themes = array('queenslandsenior');

    protected $singlePages = array(
        '/queenslandsenior' => array(
            'cName' => 'Queensland Senior Mathematics',
            'cDescription' => 'Queensland Senior Mathematics Landing Page'
        )
    );

    public function getPackageDescription()
    {
        return t("Queensland Senior Mathematics Landing Page");
    }

    public function getPackageName()
    {
        return t("Queensland Senior Mathematics Package");
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
            SinglePage::add($path, $pkg);
        }
    }
}
