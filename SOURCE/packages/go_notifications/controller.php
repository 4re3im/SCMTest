<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class GoNotificationsPackage extends Package {
    protected $pkgHandle = 'go_notifications';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.01';

    protected $singlePages = [
        '/dashboard/go_notifications' => [
            'data' => ['cName' => 'Notifications']
        ],

        '/dashboard/go_notifications/announce' => [
            'data' => ['cName' => 'Announcement Banner'],
            'attributes' => ['exclude_nav' => false]
        ],

        '/dashboard/go_notifications/survey' => [
            'data' => ['cName' => 'Survey Config'],
            'attributes' => ['exclude_nav' => false]
        ]
    ];

    public function getPackageName()
    {
      return t("Go Notification Management Package");
    }

    public function getPackageDescription()
    {
      return t("Notification management for Global Go");
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
