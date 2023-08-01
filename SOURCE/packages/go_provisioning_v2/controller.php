<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class GoProvisioningV2Package extends Package
{
    protected $pkgHandle = 'go_provisioning_v2';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '5.1';

    protected $singlePages = [
        '/dashboard/provisioning_v2' => [
            'data' => ['cName' => 'Provisioning V2']
        ],

        '/dashboard/provisioning_v2/setup' => [
            'data' => ['cName' => 'Setup'],
            'attributes' => ['exclude_nav' => false]
        ]
    ];

    public function getPackageName()
    {
        return t("Go Provisioning v2");
    }

    public function getPackageDescription()
    {
        return t("Provsioning system for TNG Go v2");
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installSinglePages($pkg);
        $this->installProvisioningTables();

        // install jobs
        Loader::model("job");
        Job::installByPackage("cup_initiate_crons_pendings_sync_new", $pkg);
        Job::installByPackage("cup_pending_provisioning_sync_new", $pkg);
        Job::installByPackage("cup_pending_product_expiry_date_sync_new", $pkg);
        Job::installByPackage("cup_hm_products_sync_new", $pkg);

    }

    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->installSinglePages($pkg);

        Loader::model("job");
        Job::installByPackage("cup_initiate_crons_pendings_sync_new", $pkg);
        Job::installByPackage("cup_pending_provisioning_sync_new", $pkg);
        Job::installByPackage("cup_pending_product_expiry_date_sync_new", $pkg);
        Job::installByPackage("cup_hm_products_sync_new", $pkg);
    }

    public function uninstall()
    {
        parent::uninstall();
    }

    protected function installSinglePages($pkg)
    {
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

    public function installProvisioningTables()
    {
        $db = Loader::db();
        $provFilesSql = "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`ProvisioningFiles` (
              `ID` INT NOT NULL AUTO_INCREMENT,
              `FileID` INT NULL,
              `FileName` VARCHAR(100) NULL,
              `DateUploaded` DATETIME NULL,
              `StaffID` INT NULL,
              PRIMARY KEY (`ID`));";
        $db->Execute($provFilesSql);

        $provUsersSql = "CREATE TABLE IF NOT EXISTS `" . DB_DATABASE . "`.`ProvisioningUsers` (
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
              PRIMARY KEY (`ID`));";
        $db->Execute($provUsersSql);
    }
}
