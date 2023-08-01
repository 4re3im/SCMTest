<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class GoProvisioningPackage extends Package {
    protected $pkgHandle = 'go_provisioning';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '3.23';

    protected $singlePages = [
        '/dashboard/provisioning' => [
            'data' => ['cName' => 'Provisioning']
        ],

        '/dashboard/provisioning/setup' => [
            'data' => ['cName' => 'Setup'],
            'attributes' => ['exclude_nav' => false]
        ],

        '/dashboard/provisioning/archive' => [
            'data' => ['cName' => 'Archive'],
            'attributes' => ['exclude_nav' => false]
        ],

        '/dashboard/provisioning/reset_password' => [
            'data' => ['cName' => 'Reset Gigya Password']
        ],

        '/dashboard/provisioning/bulk_delete' => [
            'data' => ['cName' => 'Bulk Delete Users']
        ]
    ];

    protected $jobs = [
        'cleanup_temp_data'
    ];

    public function getPackageName()
    {
      return t("Go Provisioning");
    }

    public function getPackageDescription()
    {
      return t("Provsioning system for TNG Go");
    }

    public function install()
    {
      $pkg = parent::install();
      $this->installSinglePages($pkg);
      $this->installJobs($pkg);
      $this->installDatabaseTables();
    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->installSinglePages($pkg);
        $this->installJobs($pkg);
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

            "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`GigyaResetPasswordFiles` (
                `ID` INT NOT NULL AUTO_INCREMENT,
                `FileID` INT NULL,
                `FileName` VARCHAR(100) NULL,
                `DateUploaded` DATETIME NULL,
                `StaffID` INT NULL,
                PRIMARY KEY (`ID`));",

            "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`GigyaResetPasswordUsers` (
                `ID` INT NOT NULL AUTO_INCREMENT,
                `FirstName` VARCHAR(45) NULL,
                `LastName` VARCHAR(45) NULL,
                `Email` VARCHAR(45) NULL,
                `Status` VARCHAR(100) NULL DEFAULT 'Processing',
                `IsValidForChange` TINYINT(1) NULL DEFAULT 0,
                `TempPassword` VARCHAR(100) NULL,
                `GigyaUID` VARCHAR(100) NULL,
                `FileID` INT NULL,
                `DateUploaded` DATETIME NULL DEFAULT NOW(),
                `DateModified` DATETIME NULL DEFAULT NOW(),
                PRIMARY KEY (`ID`));",

            "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`BulkDeleteFiles` (
                `ID` INT NOT NULL AUTO_INCREMENT,
                `FileID` INT NULL,
                `FileName` VARCHAR(100) NULL,
                `DateUploaded` DATETIME NULL,
                `StaffID` INT NULL,
                PRIMARY KEY (`ID`));",

            "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`BulkDeleteUsers` (
                `ID` INT NOT NULL AUTO_INCREMENT,
                `FirstName` VARCHAR(45) NULL,
                `LastName` VARCHAR(45) NULL,
                `Email` VARCHAR(45) NULL,
                `Status` VARCHAR(200) NULL DEFAULT 'Processing',
                `IsValidForChange` TINYINT(1) NULL DEFAULT 0,
                `FileID` INT NULL,
                `DateUploaded` DATETIME NULL DEFAULT NOW(),
                `DateModified` DATETIME NULL DEFAULT NOW(),
                PRIMARY KEY (`ID`));",
        ];
        
        foreach ($sqls as $sql) {
            $db->Execute($sql);
        }
    }

    public function installJobs($pkg)
    {
        Loader::model('job');
        foreach ($this->jobs as $job) {
            if (!Job::getByHandle($job)) {
                Job::installByPackage($job, $pkg);
            }
        }
    }
  }
