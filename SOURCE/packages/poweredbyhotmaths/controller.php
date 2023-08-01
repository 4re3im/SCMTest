<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

class PoweredByHotmathsPackage extends Package
{
    protected $pkgHandle = 'poweredbyhotmaths';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '1.1';

    protected $themes = array('education');

    protected $singlePages = array(
      '/poweredbyhotmaths' => array('cName' => 'PoweredByHotmaths', 'cDescription' => 'PoweredByHotmaths Landing page')
    );

    protected $blocks = array(
      'cup_landing_page_header',
      'cup_landing_page_social',
      'cup_landing_page_subjects'
    );

    public function getPackageDescription()
    {
        return t("PoweredByHotmaths package");
    }

    public function getPackageName()
    {
        return t("PoweredByHotmaths Package");
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
