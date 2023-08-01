<?php

/**
 * Dashboard Package
 */

defined('C5_EXECUTE') || die(_("Access Denied."));

class GoDashboardPackage extends Package
{
    protected $pkgHandle = 'go_dashboard';
    protected $appVersionRequired = '5.5.1';
    protected $pkgVersion = '1.0.3';

    public function getPackageDescription()
    {
        return t('Go Dashboard');
    }

    public function getPackageName()
    {
        return t('Go Dashboard');
    }

    protected $blocks = array();

    protected $singlePages = array(
        '/dashboard/code_check',
        '/dashboard/go_users',
        '/dashboard/provisioned_users'
    );

    public function checkCreatePages()
    {
        Loader::model('single_page');
        $pkg = Package::getByHandle($this->pkgHandle);
        foreach ($this->singlePages as $path) {
            if (Page::getByPath($path)->getCollectionID() <= 0) {
                SinglePage::add($path, $pkg);
            }
        }
    }

    public function install()
    {
        $pkg = parent::install();

        Loader::model('single_page');

        foreach ($this->blocks as $each_block) {
            BlockType::installBlockTypeFromPackage($each_block, $pkg);
        }

        $this->checkCreatePages();
    }
}
