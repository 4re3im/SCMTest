<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

//$category = 'vce';
$category = strtolower($eventObj->category);
Loader::element('slim_header', array('category'=>$category), 'cup_competition'); ?>

<div class="cup-competition-content-menu">
	<div class="gap"></div>
	<a href="<?php echo $this->url('/competition/'.$category);?>"><div class="item home">COMPETITION</div></a>
	<a href="<?php echo $this->url('/competition/entry_form/'.$category);?>"><div class="item">ENTRY FORM</div></a>
	<a href="<?php echo $this->url('/competition/terms_and_conditions/'.$category);?>"><div class="item">TERMS & CONDITIONS</div></a>
	<?php if($eventObj && strcmp($eventObj->type, 'Photo') == 0):?>
	<a href="<?php echo $this->url('/competition/gallery/'.$category);?>"><div class="item active">PHOTO GALLERY</div></a>
	<?php endif;?>
	<div style="clear:both;height:0px;height:0px;"></div>
</div>

<?php if($eventObj && strcmp($eventObj->type, 'Photo') == 0):?>
	<div style="float:right">
		<a href="<?php echo $this->url('/competition/entry_form/'.$category);?>"><div id="btn_competition_enter_now"></div></a>
	</div>
<?php endif;?>
<div style="clear:both;height:0px;height:0px;"></div>

<div style="width:100%;height: 20px;"></div>

<div class="cup_competition_gallery_content">
	<?php Loader::packageElement('event_entry/frontend_list', 'cup_competition', array('entryList' => $entryList));?>
	<div style="clear:both;height:5px;width:100%"></div>
</div>

<div style="width:100%;height: 20px;"></div>

<script>
	function gotoPage(dom, pageNumber){
		var ref = jQuery(dom).attr('href');
		if(ref.indexOf("ajax=yes") == -1){
			ref = ref+'&ajax=yes';
		}
		//alert(ref);
		jQuery('.cup_competition_gallery_content').addLoadingMask();
		jQuery.get(ref, 
			function(html_data){
				jQuery('.cup_competition_gallery_content').html(html_data);
				jQuery('.cup_competition_gallery_content').removeLoadingMask();
			}
		);
		return false;
	}
	
</script>