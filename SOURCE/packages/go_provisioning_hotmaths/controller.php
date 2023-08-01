<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class GoProvisioningHotmathsPackage extends Package {
    protected $pkgHandle = 'go_provisioning_hotmaths';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '1.01';

    protected $singlePages = [
        '/dashboard/provisioning_hotmaths' => [
            'data' => ['cName' => 'Provisioning Hotmaths']
        ],

        '/dashboard/provisioning_hotmaths/setup' => [
            'data' => ['cName' => 'Setup'],
            'attributes' => ['exclude_nav' => false]
        ]
    ];

    protected $jobs = [
        'cleanup_temp_data'
    ];

    public function getPackageName()
    {
      return t("Go Provisioning Hotmaths");
    }

    public function getPackageDescription()
    {
      return t("Provsioning system for TNG Go & Hotmaths");
    }

    public function install()
    {
      $pkg = parent::install();
      $this->installSinglePages($pkg);
      $this->installDatabaseTables();
    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->installSinglePages($pkg);
        $this->installDatabaseTables();
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

    public function installDatabaseTables()
    {
        $db = Loader::db();
        $sqls =
        [
            "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`ProvisioningFiles` (
                `ID` INT NOT NULL AUTO_INCREMENT,
                `FileID` INT NULL,
                `FileName` VARCHAR(100) NULL,
                `DateUploaded` DATETIME NULL,
                `StaffID` INT NULL,
                PRIMARY KEY (`ID`));",

            "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`ProvisioningUsers` (
                `ID` INT NOT NULL AUTO_INCREMENT,
                `uID` INT NULL,
                `FirstName` VARCHAR(45) NULL,
                `LastName` VARCHAR(45) NULL,
                `Email` VARCHAR(45) NULL,
                `Type` VARCHAR(45) NULL,
                `Status` VARCHAR(45) NULL DEFAULT 'Processing',
                `Remarks` VARCHAR(45) NULL,
                `FileID` INT NULL,
                `DateUploaded` DATETIME NULL,
                `DateModified` DATETIME NULL,
                PRIMARY KEY (`ID`));",
        ];
        
        foreach ($sqls as $sql) {
            $db->Execute($sql);
        }
    }
  }
