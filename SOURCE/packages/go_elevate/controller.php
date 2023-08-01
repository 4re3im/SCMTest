<?php

/**
 * Elevate Package
 * @author John Renzo Sunico <jsunico@cambridge.org>
 * Last Edited : June 07, 2017
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

class GoElevatePackage extends Package {

    protected $pkgHandle = 'go_elevate';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.0.2';

    protected $singlePages = array(
        '/Elevate' => array('data' => array('cName' => 'Elevate Integration'), 'attributes' => array('exclude_nav' => true)),
        '/Elevate/services' => array('data' => array('cName' => 'Elevate Services'), 'attributes' => array('exclude_nav' => true)),
        '/Elevate/services/credentials' => array('data' => array('cName' => 'Elevate Credentials'), 'attributes' => array('exclude_nav' => true)),
        '/Elevate/services/GetBookDetails' => array('data' => array('cName' => 'Elevate Subscriptions'), 'attributes' => array('exclude_nav' => true)),
        '/Elevate/services/login' => array('data' => array('cName' => 'Elevate Login'), 'attributes' => array('exclude_nav' => true)),
    );

    public function getPackageDescription()
    {
        return t("Go Elevate Package, Tools and APIs");
    }

    public function getPackageName()
    {
        return t("Go Elevate");
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installSinglePages($pkg);
    }

    public function uninstall()
    {
        $db = Loader::db();
        parent::uninstall();
    }

    public function upgrade()
    {
        $pkg = Package::getByHandle($this->pkgHandle);
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
        return $pkg;
    }

    protected function installSinglePages($pkg)
    {
        foreach($this->singlePages as $path => $options) {
            $cID = Page::getByPath($path)->getCollectionID();

            if (intval($cID) > 0 && $cID !== 1){
                Loader::db()->execute('update Pages set pkgID = ? where cID = ?', array($pkg->pkgID, $cID));
            } else {

                $p = SinglePage::add($path, $pkg);
                if(is_object($p) && !$p->isError()){
                    $p->update($options['data']);
                    if(isset($options['attributes'])){
                        foreach($options['attributes'] as $k => $v){
                            $p->setAttribute($k, $v);
                        }
                    }
                }
            }
        }
    }
}
