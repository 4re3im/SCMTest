<?php
/**
 * Handles all product related process
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 26, 2015
 */
class GoProductPackage extends Package
{
    protected $pkgHandle = 'go_product';

    protected $appVersionRequired = '5.3.0';

    protected $pkgVersion = '1.2.4';

    protected $singlePages = array(
        '/go/search' => array('data' => array('cName' => 'search'), 'attributes' => array('exclude_nav' => true)),
        '/go/subject' => array('data' => array('cName' => 'subject'), 'attributes' => array('exclude_nav' => true)),
        '/go/series' => array('data' => array('cName' => 'series'), 'attributes' => array('exclude_nav' => true)),
        '/go/titles' => array('data' => array('cName' => 'titles'), 'attributes' => array('exclude_nav' => true))
    );

    protected $blocks = array('');

    public function on_start()
    {
        $classes = array(
            'CupContentTitle' => array('model', 'cup_content_title', $this->pkgHandle),
            'CupContentSearch' => array('model', 'cup_content_search', $this->pkgHandle),
            'CupContentSeries' => array('model', 'cup_content_series', $this->pkgHandle),
            'CupContentSubject' => array('model', 'cup_content_subject', $this->pkgHandle),
            'CupGoTabContent' => array('model', 'cup_go_tab_content', $this->pkgHandle),
            'CupGoTabs' => array('model', 'cup_go_tabs', $this->pkgHandle),
            'CupGoContentDetail' => array('model', 'cup_go_content_detail', $this->pkgHandle),
            'CommonHelper' => array('helper', 'common', $this->pkgHandle),
            'CupGoRedirect' => array('model', 'cup_go_redirect', $this->pkgHandle),
            'CupGoLogs' => array('model', 'cup_go_logs', 'go_contents'),
            'CupGoUserSubscription' => array('model', 'cup_go_user_subscription', 'go_contents'),
            // SB-16 added by matanada 20190108 CupGoTabHmIds model class
            'CupGoTabHmIds' => array('model', 'cup_go_tab_hmid', $this->pkgHandle),
            // SB-174 added by machua 20190530 CupGoTabOrders model class
            'CupGoTabOrders' => array('model', 'cup_go_tab_order', $this->pkgHandle)

        );

        Loader::registerAutoload($classes);
    }

    public function getPackageName()
    {
        return t("Cambridge Go Products");
    }

    public function getPackageDescription()
    {
        return t("Display and search products");
    }

    public function install()
    {
        $pkg = parent::install();
        $db = Loader::db();
        $sql = "ALTER TABLE `CupContentTitle` ADD COLUMN `isGoTitle` TINYINT(1) NULL DEFAULT 1 AFTER `nz_circa_price`";
        $db->Execute($sql);
        $this->installSinglePages($pkg);
    }

    public function uninstall()
    {
        $db = Loader::db();
        parent::uninstall();
        $db = Loader::db();
        $sql = "ALTER TABLE `CupContentTitle` DROP COLUMN `isGoTitle`";
        $db->Execute($sql);
    }

    //this will need to be overridden by child classes. they can call parent::upgrade to get the pkgID
    public function upgrade()
    {
       	parent::upgrade();

        $pkg = Package::getByHandle($this->pkgHandle);

        $this->installSinglePages($pkg);

        return $pkg;
    }

    protected function installSinglePages($pkg)
    {
        foreach ($this->singlePages as $path => $options) {
            $cID = Page::getByPath($path)->getCollectionID();

            if (intval($cID) > 0 && $cID !== 1) {
                // the single page already exists, so we want
                // to update it to use our package elements
                Loader::db()->execute('UPDATE Pages set pkgID = ? WHERE cID = ?', array($pkg->pkgID, $cID));
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

    protected function installBlocks($pkg)
    {
        foreach ($this->blocks as $block) {
            BlockType::installBlockTypeFromPackage($block, $pkg);
        }
    }
}
