<?php
	$ch = Loader::helper('cup_content_html', 'cup_content');
?>
<div class="price-info">
	<?php Loader::packageElement('page_component/title_price', 'cup_content', array('titleObject' => $titleObject, 'display_quantity' => false)); ?>
	<a href="javascript:void(0);" class="cup_request_more_info_btn" ref_title="<?php echo str_replace('"', '\"', $titleObject->getDisplayName());?>" ref_isbn="<?php echo $titleObject->isbn13;?>">
		<div class="require-more-info">
			Request more information
		</div>
	</a>
	<div style="clear:both;width:1px;height:0px;"></div>
</div>