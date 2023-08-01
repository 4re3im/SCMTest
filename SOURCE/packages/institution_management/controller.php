<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class InstitutionManagementPackage extends Package {
    protected $pkgHandle = 'institution_management';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.03';

    protected $singlePages = [
        '/dashboard/institution_management' => [
            'data' => ['cName' => 'Institution Management']
        ],

        '/dashboard/institution_management/add' => [
            'data' => ['cName' => 'Create new'],
            'attributes' => ['exclude_nav' => true]
        ],

        '/dashboard/institution_management/search' => [
            'data' => ['cName' => 'Search'],
            'attributes' => ['exclude_nav' => false]
        ],

        '/dashboard/institution_management/review' => [
            'data' => ['cName' => 'Review'],
            'attributes' => ['exclude_nav' => true]
        ],

        '/dashboard/institution_management/edit' => [
            'data' => ['cName' => 'Edit'],
            'attributes' => ['exclude_nav' => true]
        ],

        '/dashboard/institution_management/review_pending' => [
            'data' => ['cName' => 'Pending'],
            'attributes' => ['exclude_nav' => false]
        ]
    ];

    public function getPackageName()
    {
      return t("Go Institution Management Package");
    }

    public function getPackageDescription()
    {
      return t("Gigya Institution Management for Global Go");
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
