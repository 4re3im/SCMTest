<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

class APIPackage extends Package
{
    const CNAME = 'cName';
    const CDESCRIPTION = 'cDescription';

    protected $pkgHandle = 'api';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.4';
    protected $themes = array('json_theme');

    protected $singlePages = array(
        '/api' => array(self::CNAME => 'Cambridge GO APIs', self::CDESCRIPTION => 'Cambridge GO APIs'),
        '/api/content' => array(self::CNAME => 'Content API', self::CDESCRIPTION => 'Cambridge GO Content APIs'),
        '/api/content/series' => array(self::CNAME => 'Content Series API', self::CDESCRIPTION => 'Series API'),
        '/api/content/title' => array(self::CNAME => 'Content Title API', self::CDESCRIPTION => 'Title API'),
        '/api/default' => array(self::CNAME => 'Generic API Responses', self::CDESCRIPTION => 'Defaults'),
        '/api/subscription' => array(self::CNAME => 'Subscription API', self::CDESCRIPTION => 'Subscriptions'),
        '/api/subscription/user' => array(self::CNAME => 'UserSubscription API', self::CDESCRIPTION => 'Subscriptions'),
        // ANZGO-3757 added by jbernardez 20180621
        '/api/reactivation' => array(self::CNAME => 'Reactivation API', self::CDESCRIPTION => 'Reactivations'),
        '/api/gigya' => array(self::CNAME => 'Gigya API', self::CDESCRIPTION => 'Gigya Related APIs'),
        '/api/gigya/provisioning' => array(self::CNAME => 'Gigya Dashboard API', self::CDESCRIPTION => 'Provisioning'),
    );

    public function getPackageDescription()
    {
        return t("Cambridge Go APIs");
    }

    public function getPackageName()
    {
        return t("Cambridge Go APIs");
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
