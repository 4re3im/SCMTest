<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class GoThemePackage extends Package {

    protected $pkgHandle = 'go_theme';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.1.8';
    
    // ANZGO-2815
    // add activate theme
    protected $themes = array('go_theme', 'go_plain_theme', 'activate_theme');
    
    protected $singlePages = array(
        '/confirmsignup' => array('data' => array('cName' => 'Confirm Signup'),'attributes' => array('exclude_nav' => true)),
        '/editaccount' => array('data' => array('cName' => 'Edit Account Modal'),'attributes' => array('exclude_nav' => true)),
        '/go' => array('data' => array('cName' => 'Cambridge GO Home'),'attributes' => array('exclude_nav' => true)),
    );

    public function getPackageDescription() {
        return t("Install GO Theme.");
    }

    public function getPackageName() {
        return t("Go Theme");
    }

    public function install() {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);
    }

    public function upgrade() {
        $pkg = Package::getByHandle('go_theme');
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
    }
    
    public function installThemes($pkg) {
        foreach ($this->themes as $theme) {
            PageTheme::add($theme,$pkg);
        }
    }
    
    protected function installSinglePages($pkg){
        foreach($this->singlePages as $path => $options){
            $cID = Page::getByPath($path)->getCollectionID();
            if(intval($cID) > 0 && $cID !== 1){
                // the single page already exists, so we want
                // to update it to use our package elements
                Loader::db()->execute('update Pages set pkgID = ? where cID = ?', array($pkg->pkgID, $cID));
            }else{
                $p = SinglePage::add($path, $pkg);
                if(is_object($p) && !$p->isError()){
                    $p->update($options['data']);
                    //set any specified attributes
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
