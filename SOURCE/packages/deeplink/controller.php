<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class DeeplinkPackage extends Package {
    protected $pkgHandle = 'deeplink';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.0.0';

    protected $singlePages = array(
        '/deeplink' => array('cName' => 'Deeplink', 'cDescription' => 'Deeplinking for HM.')
    );

    public function getPackageName()
    {
        return t("Deeplinking");
    }

    public function getPackageDescription()
    {
        return t("Deeplinking Package.");
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installSinglePages($pkg);
    }

    public function uninstall() {
        parent::uninstall();
    }

    protected function installSinglePages($pkg) {
        Loader::model('single_page');
        foreach ($this->singlePages as $path => $attr) {
          $page = SinglePage::add($path, $pkg);
          $page->update($attr);
        }
    }
}
