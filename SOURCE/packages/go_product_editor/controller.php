<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class GoProductEditorPackage extends Package {

    protected $pkgHandle = 'go_product_editor';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.03';
    
    // ANZGO-2815
    // add activate theme
    protected $themes = array('go_product_editor_theme');
    
    protected $singlePages = array(
        '/go_product_editor' => array('data' => array('cName' => 'Go Product editor'),'attributes' => array('exclude_nav' => true)),
        '/go_product_editor/tab' => array('data' => array('cName' => 'Go Product editor tab'),'attributes' => array('exclude_nav' => true)),
        // SB-385 added by jbernardez 20191029
        '/go_series_editor' => array('data' => array('cName' => 'Go Series editor'),'attributes' => array('exclude_nav' => true)),
        '/go_series_editor/tab' => array('data' => array('cName' => 'Go Series editor tab'),'attributes' => array('exclude_nav' => true))
    );

    protected $jobs = ['rescan_tab_formats'];

    public function getPackageDescription() {
        return t("Installs Go Product editor.");
    }

    public function getPackageName() {
        return t("Go Product editor");
    }

    public function install() {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);
        $this->installJobs($pkg);
    }

    public function upgrade() {
        $pkg = Package::getByHandle($this->pkgHandle);
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
        $this->installJobs($pkg);
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

    /**
     * Added by gxbalila
     * GCAP-790
     */
    protected function installJobs($pkg)
    {
        Loader::model("job");
        foreach ($this->jobs as $job) {
            if (!Job::getByHandle($job)) {
                Job::installByPackage($job, $pkg);
            }
        }
    }
}
