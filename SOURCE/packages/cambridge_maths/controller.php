<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class CambridgeMathsPackage extends Package
{
    protected $pkgHandle = 'cambridge_maths';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.3';

    protected $themes = array('cambridge_maths_theme');

    protected $singlePages = array(
      '/cambridgemaths' => array('cName' => 'Cambridge Maths', 'cDescription' => 'Cambridge Maths Landing page')
    );

    public function getPackageDescription()
    {
        return t("Cambridge Maths Landing page");
    }

    public function getPackageName()
    {
        return t("CambridgeMATHS Package");
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
