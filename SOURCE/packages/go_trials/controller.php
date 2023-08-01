<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class GoTrialsPackage extends Package {
    protected $pkgHandle = 'go_trials';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.0.0';

    protected $singlePages = [
        '/dashboard/trials' => [
            'data' => ['cName' => 'Trials']
        ],

        '/dashboard/trials/report' => [
            'data' => ['cName' => 'Report'],
            'attributes' => ['exclude_nav' => false]
        ]
    ];

    public function getPackageName()
    {
      return t("Go Trials");
    }

    public function getPackageDescription()
    {
      return t("Trials Package for TNG Go and Global Go");
    }

    public function install()
    {
      $pkg = parent::install();
      $this->installSinglePages($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->installSinglePages($pkg);
    }

    public function uninstall() {
        parent::uninstall();
    }

    protected function installSinglePages($pkg) {
        ## Install Single pages
       Loader::model('single_page');

        foreach ($this->singlePages as $path => $options) {
            $page = Page::getByPath($path);
            $cID = $page->getCollectionID();

            if (intval($cID) > 0 && $cID !== 1) {
                $sql = 'UPDATE Pages SET pkgID = ? WHERE cID = ?';
                Loader::db()->execute($sql, array($pkg->pkgID, $cID));
            } else {
                $page = SinglePage::add($path, $pkg);
            }

            if (is_object($page) && !$page->isError()) {
                $page->update($options['data']);
                if (isset($options['attributes'])) {
                    foreach ($options['attributes'] as $k => $v) {
                        $page->setAttribute($k, $v);
                    }
                }
            }
        }
    }
  }
