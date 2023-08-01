<?php 
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$uh = Loader::helper('concrete/urls');
?>

<style type="text/css">
table.ccm-grid {border-left: 1px solid #D4D4D4; border-top: 1px solid #D4D4D4; font-size: 12px; }
table.ccm-grid th, table.ccm-grid > tbody > tr > td {border-right: 1px solid #D4D4D4; border-bottom: 1px solid #D4D4D4; font-size: 12px; padding: 7px; background: #fff}
table.ccm-grid tr.ccm-row-alt td {background-color: #F0F5FF !important}
table.ccm-grid th {font-weight: bold; color: #999999; background-color: #efefef; text-align: center;}
table.ccm-grid tr.version-active td, table.ccm-grid tr.active td {font-weight: bold; font-size: 13px}
table.ccm-grid td.actor img {float: right}
table.ccm-grid td.ccm-grid-cb {text-align: center}
table.ccm-grid img {border: 0px}
</style>

<input type="hidden" name="blockToolsDir" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/" />
<input type="hidden" name="currentCID" value="<?php echo $c->getCollectionId()?>" />
<ul id="ccm-blockEditPane-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-blockEditPane-tab-search" href="javascript:void(0);"><?php echo t('Search') ?></a></li>
	<li class=""><a id="ccm-blockEditPane-tab-formatting"  href="javascript:void(0);"><?php echo t('Data Formatting')?></a></li>
	<li class=""><a id="ccm-blockEditPane-tab-layout"  href="javascript:void(0);"><?php echo t('Results Layout')?></a></li>
</ul>
<div id="ccm-blockEditPane-search" class="ccm-blockEditPane">
<?php 
$bt->inc('elements/form_setup_filter.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args   ) );
?>
</div>
<div id="ccm-blockEditPane-formatting" class="ccm-blockEditPane" style="display:none">
<?php 
$bt->inc('elements/form_setup_results.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args ) );
?>
</div>
<div id="ccm-blockEditPane-layout" class="ccm-blockEditPane" style="display:none">
<?php 
$bt->inc('elements/form_setup_layout.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args ) );
?>
</div>