<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentPackage extends Package
{
    protected $pkgHandle = 'cup_content';
    protected $appVersionRequired = '5.5.1';
    protected $pkgVersion = '0.0.9';

    protected $blocks = array(
        'cup_main_sidebar',
        'cup_main_carousel',
        'cup_headline_viewer',
        'cup_simple_header',
        'cup_contact_form'
    );

    protected $singlePages = array(
        '/dashboard/cup_content/formats',
        '/dashboard/cup_content/formats/add',
        '/dashboard/cup_content/authors',
        '/dashboard/cup_content/authors/add',
        '/dashboard/cup_content/authors/search',
        '/dashboard/cup_content/subjects',
        '/dashboard/cup_content/subjects/add',
        '/dashboard/cup_content/subjects/search',
        '/dashboard/cup_content/series',
        '/dashboard/cup_content/series/add',
        '/dashboard/cup_content/series/search',
        '/dashboard/cup_content/titles',
        '/dashboard/cup_content/titles/add',
        '/dashboard/cup_content/titles/search',
        '/education/search',
        '/education/series',
        '/education/authors',
        '/education/subjects',
        '/education/titles',
    );

    protected $jobs = [
        'cup_inventory_sync',
        'cup_order_sync',
        'rescan_series_images',
        'rescan_title_images'
    ];

    public function getPackageDescription()
    {
        return t('CUP Content Management');
    }

    public function getPackageName()
    {
        return t('CUP Content Management');
    }

    public function checkCreatePages()
    {
        Loader::model('single_page');
        $pkg = Package::getByHandle($this->pkgHandle);
        foreach ($this->singlePages as $path) {
            if (Page::getByPath($path)->getCollectionID() <= 0) {
                $page = SinglePage::add($path, $pkg);
            }
        }

        $c1 = Page::getByPath('/dashboard/cup_content');
        $c1->update(array('cName' => t('CUP Content Management'), 'cDescription' => $this->getPackageDescription()));
    }

    /**
     * Added by gxbalila
     * Check and adds needed jobs
     */
    public function checkCreateJobs($pkg)
    {
        // Added by gxbalila
        // GCAP-786
        Loader::model("job");
        foreach ($this->jobs as $job) {
            if (!Job::getByHandle($job)) {
                Job::installByPackage($job, $pkg);
            }
        }
    }

    public function on_start()
    {
        Events::extend(
            'core_commerce_on_get_payment_methods',
            'CupContentEvent',
            'onGetPaymentMethods',
            'packages/' . $this->pkgHandle . '/models/cup_content_event.php',
            array($currentOrder, $methods));

        Events::extend(
            'core_commerce_on_get_shipping_methods',
            'CupContentEvent',
            'onGetShippingMethods',
            'packages/' . $this->pkgHandle . '/models/cup_content_event.php',
            array($currentOrder, $methods));

        Events::extend(
            'core_commerce_on_checkout_shipping_address_submit',
            'CupContentEvent',
            'onCheckoutShippingAddressSubmit',
            'packages/' . $this->pkgHandle . '/models/cup_content_event.php',
            array($address));

        Events::extend(
            'core_commerce_on_checkout_start',
            'CupContentEvent',
            'onCheckoutStart',
            'packages/cup_content/models/cup_content_event.php',
            array($checkout));

        Events::extend(
            'core_commerce_on_checkout_finish_order',
            'CupContentEvent',
            'onPurchaseComplete',
            'packages/' . $this->pkgHandle . '/models/cup_content_event.php',
            array($ui, $order));
    }

    public function cleanUpData()
    {
        Loader::model("product/model", 'core_commerce');
        Loader::model("title/model", 'cup_content');

        $db = Loader::db();
        $q = "select id from CupContentTitle";
        $r = $db->Execute($q);
        while ($row = $r->FetchRow()) {
            $title_id = $row['id'];

            $title = CupContentTitle::fetchByID($title_id);
            if ($title) {
                $title->delete();
            }
        }
    }

    public function uninstall()
    {
        parent::uninstall();
        $this->cleanUpData();
        $db = Loader::db();
    }

    // this will need to be overridden by child classes. they can call parent::upgrade to get the pkgID

    /**
     * Modified by gxbalila
     * Added checker of jobs if installed or not.
     * Add new job of rescanning series and title(?) images for Global Go.
     * @return Package
     */
    public function upgrade()
    {
        parent::upgrade();
        $pkg = Package::getByHandle($this->pkgHandle);

        $this->checkCreateJobs($pkg);
        $this->checkCreatePages();

        return $pkg;
    }

    public function install()
    {
        $pkg = parent::install();
        Loader::model('single_page');

        // install block
        foreach ($this->blocks as $each_block) {
            BlockType::installBlockTypeFromPackage($each_block, $pkg);
        }

        // install job
        $this->checkCreateJobs($pkg);

        // install single_pages
        $this->checkCreatePages();
    }
}
