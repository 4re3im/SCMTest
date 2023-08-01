<?php 
// SB-456 modified by jbernardez 20200213
// standardized code for both add and edit
defined('C5_EXECUTE') or die(_("Access Denied."));

$html = Loader::helper('html');
$this->addHeaderItem($html->css("jquery.ui.css"));
$this->addHeaderItem($html->javascript("jquery.ui.js"));

$this->addHeaderItem($html->css('wform.css', 'cup_content')); 
$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 

$valt = Loader::helper('validation/token');
$th = Loader::helper('concrete/urls');
$pkg = Package::getByHandle('cup_content');
$loaderPath = $th->getPackageURL($pkg);
$url = $th->getToolsURL('autocomplete', 'cup_content');

?>
<style>
    /* GCAP-625 added by mtanada 20200128 */
    .ui-autocomplete {
        font-size: 12px;
        max-height: 300px;
        max-width: 1000px;
        overflow-y: auto;
        overflow-x: auto;
    }
    .ui-autocomplete-loading {
        background: url('<?php echo $loaderPath?>/images/ajax-loader.gif') no-repeat right center
    }
</style>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Title'), false, false, false); ?>
<form 
    method="post" 
    id="ccm-core-commerce-product-add-form" 
    enctype="multipart/form-data" 
    action="<?php echo $this->url('/dashboard/cup_content/titles/edit', $entry['id'])?>">
<div class="ccm-pane-body">
    <?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
    <?php echo $valt->output('edit_title')?>
    <?php Loader::packageElement('title/form', 'cup_content', array('entry'=>@$entry)); ?>
</div>
<div class="ccm-pane-footer">
    <input type="hidden" name="create" value="1" />
    <input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
    <a href="<?php echo $this->url('/dashboard/cup_content/titles')?>" class="btn"><?php echo t('Back to Titles')?></a>
    <input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</div>  
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
