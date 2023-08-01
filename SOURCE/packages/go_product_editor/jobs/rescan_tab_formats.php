<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 27/03/2020
 * Time: 3:25 PM
 */

class RescanTabFormats extends Job
{
    const QUERY = "UPDATE CupGoTabs SET thumbnail_url = ? WHERE ID = ?";
    private $productModel;
    private $db;

    public function getJobName()
    {
        return t("Global Go Tab Formats Rescanner");
    }

    public function getJobDescription()
    {
        return t("Scans all tab formats");
    }

    public function run()
    {
        $this->db = Loader::db();
        Loader::model('go_product_editor_model', 'go_product_editor');
        $this->productModel = new GoProductEditorModel();
        $tabs = $this->productModel->getAllTabs();
        $totalScanned = 0;
        foreach ($tabs as $tab) {
            $tabIcon = (int)$tab['TabIcon'];
            $tabID = $tab['ID'];
            
            $totalScanned += $this->scanFormat($tab);
            
            $thumbnailURL = $this->productModel->getTabThumbnailURL($tabIcon);
            $this->db->Execute(static::QUERY, [$thumbnailURL, $tabID]);
        }
        return t('Finished updating ' . $totalScanned . ' tabs.');
    }

    public function scanFormat($tab)
    {
        $tabIcon = (int)$tab['TabIcon'];
        $path = TAB_ICON_FOLDER . $tabIcon;
        $pathWithPNG = $path . '.png';

        if ($tabIcon === 0 || !file_exists($path)) {
            return 0;
        }

        if (!file_exists($pathWithPNG)) {
            try {
                copy($path, $pathWithPNG);
                chmod($pathWithPNG, 0777);
            } catch (Exception $e) {
                return 0;
            }
        }
        return 1;
    }
}