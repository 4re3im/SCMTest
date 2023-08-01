<?php
/**
 * Created by Gerard Paul Balila <gxbalila@cambridge.org>.
 * User: gxbalila
 * Date: 16/05/2019
 * Time: 12:41 PM
 */

class GoGigyaPackage extends Package
{
    protected $pkgHandle = 'go_gigya';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.1';

    protected $singlePages = [
        '/dashboard/gigya' => ['data' => ['cName' => 'Go-Gigya']],
        '/dashboard/gigya/users' => ['data' => ['cName' => 'Users']],
        '/dashboard/gigya/subscriptions' => [
            'data' => ['cName' => 'Subscriptions'],
            'attributes' => ['exclude_nav' => true]
        ],
    ];

    public function getPackageName()
    {
        return t('Go-Gigya Management');
    }

    public function getPackageDescription()
    {
        return t('Handles Go - Gigya integrated functions.');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installSinglePages($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->installSinglePages($pkg);
    }

    protected function installSinglePages($package)
    {
        Loader::model('single_page');
        foreach ($this->singlePages as $path => $options) {
            $cID = Page::getByPath($path)->getCollectionID();

            if (intval($cID) > 0 && $cID !== 1) {
                $sql = 'UPDATE Pages SET pkgID = ? WHERE cID = ?';
                Loader::db()->execute($sql, [$package->pkgID, $cID]);
                continue;
            }

            $page = SinglePage::add($path, $package);
            if (is_object($page) && !$page->isError()) {
                $page->update($options['data']);
                if(isset($options['attributes'])) {
                    foreach ($options['attributes'] as $index => $attribute) {
                        $page->setAttribute($index, $attribute);
                    }
                }
            }

        }
    }
}