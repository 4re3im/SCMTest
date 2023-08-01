<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class SSoGoPackage extends Package {
    protected $pkgHandle = 'sso_go';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.2.1';

    protected $singlePages = array(
      '/sso/login' => array('cName' => 'SSO Login', 'cDescription' => 'SSO login pages.'),
      '/sso/logout' => array('cName' => 'SSO Logout', 'cDescription' => 'SSO logout page.'),
      '/sso/build'  => array('cName' => 'SSO Build', 'cDescription' => 'SSO user builder.'),
      '/sso'  => array('cName' => 'SSO API', 'cDescription' => 'SSO API.')
      );

    protected $themes = array('sso_go_theme');

    public function getPackageName()
    {
      return t("SSO Go");
    }

    public function getPackageDescription()
    {
      return t("Single sign on system for all Cambridge Go apps.");
    }

    public function install()
    {
      $pkg = parent::install();
      $this->installSinglePages($pkg);
      $this->installThemes($pkg);
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

    public function installThemes($pkg)
    {
      foreach ($this->themes as $theme) {
          PageTheme::add($theme,$pkg);
      }
    }

  }
