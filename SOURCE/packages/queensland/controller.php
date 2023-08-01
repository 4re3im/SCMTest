<?php
/**
 *  Author: Maryjes Tanada 03/21/2018
 *  Landing page for /queensland
 */
defined('C5_EXECUTE') || die(_("Access Denied."));

class QueenslandPackage extends Package
{
    protected $pkgHandle = 'queensland';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.0.0';

    protected $themes = array('queensland_theme');

    protected $singlePages = array(
      '/queensland' => array('cName' => 'Queensland', 'cDescription' => 'Queensland Landing page')
    );

    protected $blocks = array(
      'cup_landing_page_header',
      'cup_landing_page_social',
      'cup_landing_page_subjects'
    );

    public function getPackageDescription()
    {
        return t("Queensland package");
    }

    public function getPackageName()
    {
        return t("Queensland Package");
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installThemes($pkg);
        $this->installSinglePages($pkg);

        foreach ($this->blocks as $each_block) {
            $blockType = BlockType::getByHandle($each_block);

            if (!$blockType) {
                BlockType::installBlockTypeFromPackage($each_block, $pkg);
            }
        }
    }

    public function upgrade()
    {
        $pkg = Package::getByHandle('go_theme');
        parent::upgrade($pkg);
        $this->installSinglePages($pkg);
    }

    public function installThemes($pkg)
    {
        foreach ($this->themes as $theme) {
            PageTheme::add($theme, $pkg);
        }
    }

    protected function installSinglePages($pkg)
    {
      Loader::model('single_page');
      foreach ($this->singlePages as $path => $attr) {
        $page = SinglePage::add($path, $pkg);
        $page->update($attr);
      }
    }
}
