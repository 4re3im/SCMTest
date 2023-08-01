<?php
/**
 * Description of controller
 *
 * @author paulbalila
 */
class GlobalContentPackage extends Package {
    
    protected $pkgHandle = 'global_content';
    protected $pkgVersion = '1.0';
    protected $appVersionRequired = '5.3.0';
    
    protected $singlePages = array(
        '/dashboard/global_tab_management/management' => array('data' => array('cName' => 'Global Tab Management'), 'attributes' => array('exclude_nav' => true))
    );
    
    public function on_start() {
        ;
    }
    
    public function getPackageDescription() {
        return t("Package for Global Tab Management.");
    }

    public function getPackageName() {
        return t("Global Tab Management");
    }
    
    public function install() {
        $pkg = parent::install();
        $this->installSinglePages($pkg);
    }
    
    protected function installSinglePages($pkg) {
        foreach ($this->singlePages as $path => $options) {
            $cID = Page::getByPath($path)->getCollectionID();
            if (intval($cID) > 0 && $cID !== 1) {
                // the single page already exists, so we want
                // to update it to use our package elements
                Loader::db()->execute('update Pages set pkgID = ? where cID = ?', array($pkg->pkgID, $cID));
            } else {
                $p = SinglePage::add($path, $pkg);
                if (is_object($p) && !$p->isError()) {
                    $p->update($options['data']);
                    //set any specified attributes
                    if (isset($options['attributes'])) {
                        foreach ($options['attributes'] as $k => $v) {
                            $p->setAttribute($k, $v);
                        }
                    }
                }
            }
        }
    }
}
