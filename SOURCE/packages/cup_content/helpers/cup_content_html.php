<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('format/model', 'cup_content');

class CupContentHtmlHelper {
	static $formats = array();
	
	public function buildQuery($action, $params){
		$uh = Loader::helper('url');
		return $uh->buildQuery(rtrim($this->url($action), '/'), $params);
	}
	
	public function url($action, $task = null) {
		$dispatcher = '';
		if ((!URL_REWRITING_ALL) || !defined('URL_REWRITING_ALL')) {
			$dispatcher = '/' . DISPATCHER_FILENAME;
		}
		
		$action = trim($action, '/');
		if ($action == '') {
			return DIR_REL . '/';
		}
		
		// if a query string appears in this variable, then we just pass it through as is
		if (strpos($action, '?') > -1) {
			return DIR_REL . $dispatcher. '/' . $action;
		} else {
			$_action = DIR_REL . $dispatcher. '/' . $action . '/';
		}
		
		if ($task != null) {
			if (ENABLE_LEGACY_CONTROLLER_URLS) {
				$_action .= '-/' . $task;
			} else {
				$_action .= $task;			
			}
			$args = func_get_args();
			if (count($args) > 2) {
				for ($i = 2; $i < count($args); $i++){
					$_action .= '/' . $args[$i];
				}
			}
			
			if (strpos($_action, '?') === false) {
				$_action .= '/';
			}
		}
		
		return $_action;
		//return $_action;
	}
	
	public function renderFormats($formats) {		//formats array
		foreach($formats as $format_name){
			if(!isset($this->formats[$format_name])){
				$this->formats[$format_name] = CupContentFormat::fetchByName($format_name);
			}
			
			if(isset($this->formats[$format_name]) && $this->formats[$format_name] !== false){
				$image_url = $this->formats[$format_name]->getImageURL();
				?>
				<img src="<?php echo $image_url;?>" title="<?php echo $format_name;?>" alt="<?php echo $format_name;?>"/>
				<?php
			}else{
				?>
				[<?php echo $format_name;?>]
				<?php
			}
			
		}
	}
	
	public function printFormats($obj, $full_detail = false) {		//formats array
		if(in_array(get_class($obj), array('CupContentTitle', 'CupContentSeries'))){
			foreach($obj->formats as $format_name){
				if(!isset($this->formats[$format_name])){
					$this->formats[$format_name] = CupContentFormat::fetchByName($format_name);
				}
				$image_url = $this->formats[$format_name]->getImageURL();
				
				if(!$full_detail):?>
					<img src="<?php echo $image_url;?>" title="<?php echo $format_name;?>" alt="<?php echo $format_name;?>"/>
				<?php else:?>
					<div class="cup_format_detail_info">
						<div class="img_frame">
							<img src="<?php echo $image_url;?>" title="<?php echo $format_name;?>" alt="<?php echo $format_name;?>"/>
						</div>
						<div class="info_frame">
							<div class="title"><?php echo $this->formats[$format_name]->name;?></div>
							<div class="description"><?php echo nl2br($this->formats[$format_name]->longDescription);?></div>
						</div>
					</div>
					<div style="width:1px; height:15px;"></div>
				<?php endif;
			}
		}
	}
	
	public function printAuthors($obj, $dilimeter = ', ', $isDetail = false){
		if(in_array(get_class($obj), array('CupContentTitle'))){
			$html = array();
			
			foreach($obj->getAuthorObjects() as $each_name => $author_object){
	
				ob_start();
				
				if(!$isDetail){
					?>
						<?php echo $each_name;?>
					<?php
				}else{
					?>
					<div class="cup_author_detail_info">
					<?php
						if($author_object):?>
							<div class="title">
								<?php echo $each_name;?>
							</div>
							<div class="biography"><?php echo $author_object->biography;?></div>
						<?php else:?>
							<div class="title"><?php echo $each_name;?></div>
							<div class="biography"><?php echo $author_object->biography;?></div>
						<?php endif;?>
					</div>
					<?php
				}
				$html[] = ob_get_clean();
			}
			
			if($isDetail){
				echo implode('<div style="width:1px; height: 15px"></div>', $html);
			}else{
				echo implode($dilimeter, $html);
			}
		}
	}
	
	public function printSeries($obj){
		if(in_array(get_class($obj), array('CupContentTitle'))){
			$seriesObj = $obj->getSeriesObject();
			?>
			<div class="cup_series_detail_info">
				<!-- <div class="title"><?php echo $seriesObj->name;?></div> -->
				<div class="description">
					<?php echo $seriesObj->shortDescription;?>
				</div>
				<div class="tag">ALL TITLES IN SERIES:</div>
			</div>
			<?php
			
			$series_title_objects = $seriesObj->getTitleObjects($only_enabled);
			foreach($series_title_objects as $each_object){
				?>
				<div class="title_item">
					<div class="spacer"></div>
					<div>
						<table>
							<tr>
								<td class="gap"></td>
								<td class="image-frame">
									<img src="<?php echo $each_object->getImageURL(60);?>"/>
								</td>
								<td class="gap"></td>
								<td class="info-frame">
									<div class="title-info"><a href="<?php echo $each_object->getUrl();?>"><?php echo $each_object->name;?></a></div>
									<diV class="isbn-info">ISBN <?php echo $each_object->isbn13;?></div>
									<div class="format-info">
										INCLUDED COMPONENTS
										<div class="formats_frame">
										<?php $this->renderFormats($each_object->formats);?>
										</div>
									</div>
								</td>
								<td class="gap"></td>
								<td class="action-frame">
									<?php Loader::packageElement('frontend/title_simple_prce_info_tag', 'cup_content', array('titleObject'=>$each_object)); ?>
									<?php //Loader::packageElement('page_component/title_price', 'cup_content', array('titleObject' => $each_object, 'display_quantity' => false)); ?>
								</td>
							</tr>
						</table>
					</div>
					<div class="spacer"></div>
				</div>
				<?php
			}
		}
	}
	
	public function printRegions($obj, $dilimeter = ', '){
		if(in_array(get_class($obj), array('CupContentTitle', 'CupContentSeries'))){
			$aus_states = array('New South Wales', 'Northern Territory', 'Queensland',
							'South Australia', 'Tasmania', 'Victoria', 'Western Australia');
			$aus_states = array_intersect($aus_states, $obj->regions);
			if(in_array('Australia & New Zealand', $obj->regions)){
				echo 'Australia & New Zealand';
			}elseif(in_array('New Zealand', $obj->regions)){
				echo 'New Zealand';
			}elseif(count($aus_states) == 7){
				echo 'Australia';
			}else{
				echo implode($dilimeter, $aus_states);
			}
		}
	}
	
	public function printLevels($obj, $dilimeter = ', '){
		if(in_array(get_class($obj), array('CupContentTitle', 'CupContentSeries'))
			&& is_array($obj->yearLevels)){
			$levels = array('F', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
			$levels = array_intersect($levels, $obj->yearLevels);
			
			$str = array();
			foreach($levels as $each){
				if($each == 'F'){
					$str[] = 'Foundation';
				}else{
					$str[] = 'Year '.$each;
				}
			}
			
			echo implode($dilimeter, $str);
		}
	}
	
	public function printSubjects($obj, $dilimeter = ', '){
		if(in_array(get_class($obj), array('CupContentTitle'))){
			echo implode($dilimeter, $obj->subjects);
		}
	}
	
	
	public function renderPagination($pages, $base_url, $page_size = 10, $criteria = array()){
		$uh = Loader::helper('url');
		$form = Loader::helper('form');
		$html = array();
		
		$current_idx = 0;
		$show_number_of_page = 5;
		?>
		<?php foreach($pages as $name=>$pageNumber):?>
			<?php ob_start();?>
			<?php if($pageNumber):?>
				<span>
					<a href="<?php echo $uh->setVariable('cc_page', $pageNumber);?>"><?php echo $name;?></a>
				</span>
			<?php elseif(in_array($name, array('. ..', '.. .', 'Previous', 'Next'))):?>
				<span class="current"><?php echo $name;?></span>
			<?php else:?>
				<span class="current_page"><?php echo $name;?></span>
			<?php 
				$current_idx = count($html);
			endif;?>
			<?php $html[] = ob_get_clean();?>
		<?php endforeach;?>
		<div class="cup_pagination">
		
		<?php echo implode('|', $html);?>
			<div class="page_size">
				Items per page <?php echo $form->select('cup_page_size', array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20'), $page_size);?>
			</div>
		</div>
		<script>
			jQuery('.cup_pagination .page_size select[name="cup_page_size"]').change(function(){
				var url = "<?php echo $base_url;?>";
				var page_size = jQuery(this).val();
				var query = "<?php unset($criteria['cc_size']); echo http_build_query($criteria);?>";
				if(query.length > 1){
					url = url+'?'+query+'&cc_size='+page_size;
				}else{
					url = url+'?cc_size='+page_size;
				}
				document.location.href = url;
			});
		</script>
		<?php
	}
	
	public function currency(){
		if(!isset($_SESSION['DEFAULT_LOCALE'])){
			setcookie('DEFAULT_LOCALE', 'en_AU', time()+60*60*24*365);
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}
		
		if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0){
			echo 'AUD';
		}elseif(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
			echo 'NZD';
		}
	}
	
	public function html2text($str){
		require_once(DIR_PACKAGES.'/cup_content/libraries/html2text.php');
		$h2t =& new html2text($str);
		return $h2t->get_text();
	}

	public function getCurrentLocate($default = false){
		if(isset($_SESSION['DEFAULT_LOCALE'])){
			return $_SESSION['DEFAULT_LOCALE'];
		}
		return $default;
	}
	
	public function formatRawSize($bytes) {
 
        //CHECK TO MAKE SURE A NUMBER WAS SENT
        if(!empty($bytes)) {
 
            //SET TEXT TITLES TO SHOW AT EACH LEVEL
            $s = array('bytes', 'kb', 'MB', 'GB', 'TB', 'PB');
            $e = floor(log($bytes)/log(1024));
 
            //CREATE COMPLETED OUTPUT
            $output = sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
 
            //SEND OUTPUT TO BROWSER
            return $output;
 
        }
   }
}
