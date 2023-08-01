<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('format/model', 'cup_content');

class CupContentHtmlHelper {
	static $formats = array();
	
	public function renderFormats($formats) {		//formats array
		foreach($formats as $format_name){
			if(!isset($this->formats[$format_name])){
				$this->formats[$format_name] = CupContentFormat::fetchByName($format_name);
			}
			$image_url = $this->formats[$format_name]->getImageURL();
			?>
			<img src="<?php echo $image_url;?>" title="<?php echo $format_name;?>" alt="<?php echo $format_name;?>"/>
			<?php
		}
		
		return $save_result;
	}
	
	public function renderPagination($basic_url, $page_info){
		$uh = Loader::helper('url');
		
		?>
		<ul class="cup_pagination">
		<?php foreach($search->getPages() as $name => $page_number):?>
			<li><a href="<?php echo $uh->setVariable('cc_page', $page_number, $basic_url);?>"><?php echo $name;?></a></li>
		<?php endforeach;?>
		<ul>
		<?php
	}
	
}
