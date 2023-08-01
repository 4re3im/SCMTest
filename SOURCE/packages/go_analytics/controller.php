<?php

/**
 * Analytics Package
 * @author Sharrena Mae Camus <scamus@cambridge.org>
 * Last Edited : June 30, 2017
 */

defined('C5_EXECUTE') || die(_("Access Denied."));

define('ATTRIBUTES', 'attributes');
define('DATA', 'data');
define('CNAME', 'cName');
define('EXCLUDE_NAV', 'exclude_nav');

class GoAnalyticsPackage extends Package
{
    protected $pkgHandle = 'go_analytics';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.0.4';

    protected $themes = array('go_analytics');

    protected $singlePages = array(
        '/analytics' => array(
            DATA => array(CNAME => 'Analytics Integration'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/analytics/epub' => array(
            DATA => array(CNAME => 'Analytics EPub Integration'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/analytics/interactive_book' => array(
            DATA => array(CNAME => 'Analytics ITB Integration'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        ),
        '/analytics/defaults' => array(
            DATA => array(CNAME => 'Analytics Default Responses'),
            ATTRIBUTES => array(EXCLUDE_NAV => true)
        )
    );

    public function installThemes($pkg)
    {
        foreach ($this->themes as $theme) {
            PageTheme::add($theme, $pkg);
        }
    }

    public function getPackageDescription()
    {
        return t("Go Analytics Tools and APIs");
    }

    public function getPackageName()
    {
        return t("Go Analytics");
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);
    }

    public function upgrade()
    {
        $pkg = Package::getByHandle($this->pkgHandle);
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
        $this->installThemes($pkg);

        return $pkg;
    }

    protected function installSinglePages($pkg)
    {
        foreach ($this->singlePages as $path => $options) {
            $cID = Page::getByPath($path)->getCollectionID();

            if (intval($cID) > 0 && $cID !== 1) {
                Loader::db()->execute('update Pages set pkgID = ? where cID = ?', array($pkg->pkgID, $cID));
            } else {
                $p = SinglePage::add($path, $pkg);
                if (is_object($p) && !$p->isError()) {
                    $p->update($options[DATA]);
                    if (isset($options[ATTRIBUTES])) {
                        foreach ($options[ATTRIBUTES] as $k => $v) {
                            $p->setAttribute($k, $v);
                        }
                    }
                }
            }
        }
    }
}
