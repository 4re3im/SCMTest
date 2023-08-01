<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

class CodeCheckPackage extends Package {

    protected $pkgHandle = 'codecheck';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.1';

    protected $themes = array('codecheck_theme');

    protected $singlePages = array(
        '/codecheck' => array(
            'data' => array('cName' => 'Code Check'),
            ATTRIBUTES => array('exclude_nav' => true)),
        '/codecheck/api' => array(
            'data' => array('cName' => 'Code Check API'),
            ATTRIBUTES => array('exclude_nav' => true))
    );

    public function getPackageDescription()
    {
        return t("Cambridge GO Code Check");
    }

    public function getPackageName()
    {
        return t("Code Health Check Package");
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

    public function installThemes($pkg)
    {
        foreach ($this->themes as $theme) {
            PageTheme::add($theme, $pkg);
        }
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
                    $p->update($options['data']);
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
