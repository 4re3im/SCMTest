<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class DynamicEnglishPackage extends Package {

    protected $pkgHandle = 'dynamic_english';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '0.0.1';
    
    protected $themes = array('dynamic_english_theme');
    
    protected $singlePages = array(
        '/dynamicenglish' => array('data' => array('cName' => 'Dynamic English'),'attributes' => array('exclude_nav' => true))
    );

    public function getPackageDescription() {
        return t("Install Dynamic English Package.");
    }

    public function getPackageName() {
        return t("Dynamic English");
    }

    public function install() {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);
    }

    public function upgrade() {
        $pkg = Package::getByHandle('dynamic_english');
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
