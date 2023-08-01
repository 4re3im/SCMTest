<?php

/**
 * Provision Package
 * @author Ariel Tabag <atabag@cambridge.org>
 * Last Edited : May 20, 2014
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoPackage extends Package {

    protected $pkgHandle = 'cup_go';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.0';

    /*
    * Format for $singlePages array:
    */
    protected $singlePages = array(
        '/education/provision' => array('data' => array('cName' => 'Provision'),'attributes' => array('exclude_nav' => true)),
        '/education/go_login' => array('data' => array('cName' => 'Login'),'attributes' => array('exclude_nav' => true)),
        '/client' => array('data' => array('cName' => 'client'),'attributes' => array('exclude_nav' => true)),
        '/api' => array('data' => array('cName' => 'api'),'attributes' => array('exclude_nav' => true))
    );

    protected $themes = array('cup_go');

    public function getPackageDescription() {
        return t("Package for Cambridge Go Account management.");
    }

    public function getPackageName() {
        return t("Cambridge Go Account management");
    }


    public function on_start() {

        $classes = array(

            'User' => array('model', 'user', $this->pkgHandle),

            'Provision' => array('model', 'provision', $this->pkgHandle),

            'Products' => array('model', 'products', $this->pkgHandle),

            'ExcelReader' => array('model', 'excel_reader', $this->pkgHandle),

            'PHPFunction' => array('model', 'php_function', $this->pkgHandle),

            'EpubTesthubUser' => array('model', 'epub_testhub_user', $this->pkgHandle),

            'CupGoExternalUser' => array('model', 'cup_go_external_user', $this->pkgHandle),

            'CupGoPhpSession' => array('model', 'cup_go_php_session', 'go_contents'),
        );

        Loader::registerAutoload($classes);

    }

    public function install() {

        $pkg = parent::install();

        $this->installThemes($pkg);

        $this->installSinglePages($pkg);

        $this->installCustomDB();

    }

    public function uninstall() {

        $db = Loader::db();

        parent::uninstall();

        $this->uninstallCustomDB();

    }

    public function upgrade() {
        $pkg = Package::getByHandle($this->pkgHandle);
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
        return $pkg;
    }

    protected function installCustomDB(){

      $xmlFile = dirname(__FILE__).'/custom_db.xml';


      if(file_exists($xmlFile)) {

         Log::AddEntry('PKG INSTALL: Custom DB file exists. Creating tables in storage DB.');

         // establish connection to custom database
         $db = Loader::db(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, true);




         Package::installDB($xmlFile);

         // reset connection to core database
         $db = Loader::db(null, null, null, null, true);

         Log::AddEntry('Connection re-established with core database.');
      }

    }

    protected function uninstallCustomDB(){

        $xmlFile = dirname(__FILE__).'/custom_db.xml';

        if(file_exists($xmlFile)) {

            $db = Loader::db(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, true);

            $schema = Database::getADOSchema();

            $sql = $schema->RemoveSchema($xmlFileName);

            $schema->ExecuteSchema();

            $db->Execute('drop table if exists Provision_Files');

            $db->Execute('drop table if exists Provision_Codes');

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

    protected function installThemes($pkg){

        foreach($this->themes as $theme){

            PageTheme::add($theme, $pkg);

        }

    }

}
