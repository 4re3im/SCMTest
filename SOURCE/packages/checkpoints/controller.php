<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class CheckpointsPackage extends Package
{
    protected $pkgHandle = 'checkpoints';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.0';

    protected $themes = array('checkpoints_theme');

    protected $singlePages = array(
      '/checkpoints' => array('cName' => 'Checkpoints', 'cDescription' => 'Checkpoints Landing page')
    );

    public function getPackageDescription()
    {
        return t("Checkpoints Landing page");
    }

    public function getPackageName()
    {
        return t("Checkpoints Package");
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
