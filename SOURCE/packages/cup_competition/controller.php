<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class CupCompetitionPackage extends Package {

    protected $pkgHandle = 'cup_competition';
    protected $appVersionRequired = '5.5.1';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription() {
        return t('CUP Competition Management');
    }

    public function getPackageName() {
        return t('CUP Competition Management');
    }

    protected $blocks = array();
    protected $singlePages = array(
        '/dashboard/cup_competition/event',
        '/dashboard/cup_competition/entry',
        '/dashboard/cup_competition/config',
        '/education/competition',
    );

    public function checkCreatePages() {
        Loader::model('single_page');
        $pkg = Package::getByHandle($this->pkgHandle);
        foreach ($this->singlePages as $path) {
            if (Page::getByPath($path)->getCollectionID() <= 0) {
                $page = SinglePage::add($path, $pkg);
            }
        }

        $c1 = Page::getByPath('/dashboard/cup_competition');
        $c1->update(array('cName' => t('CUP Competition Management'), 'cDescription' => $this->getPackageDescription()));

        /*
          $c1 = Page::getByPath('/dashboard/cup_toolbox/payment');
          $c1->update(array('cName'=>t('Payment & Sales Tax')));
         */
    }

    public function on_start() {
        /*
          define('DIRNAME_ECOMMERCE_LOCAL', 'core_commerce');

          define('FILENAME_CUP_TOOLBOX_DB', 'db.xml');
          define('FILENAME_ECOMMERCE_SHIPPING_CONTROLLER', 'controller.php');

          define('DIRNAME_ECOMMERCE_DISCOUNT', 'discount');
          define('DIRNAME_ECOMMERCE_DISCOUNT_TYPES', 'types');
          define('FILENAME_ECOMMERCE_DISCOUNT_DB', 'db.xml');
         */
    }

    public function uninstall() {
        parent::uninstall();

        $db = Loader::db();

        $db->Execute('truncate table CupCompetitionEventEntry');

        $db->Execute('truncate table CupCompetitionEvent');


        $db->Execute('drop table if exists CupCompetitionEventEntry');

        $db->Execute('drop table if exists CupCompetitionEvent');
    }

    public function upgrade() {
        // 1.1
        parent::upgrade();
    }

    public function install() {
        $pkg = parent::install();

        Loader::model('single_page');


        // install block
        foreach ($this->blocks as $each_block) {
            BlockType::installBlockTypeFromPackage($each_block, $pkg);
        }


        $this->checkCreatePages();
    }

}
