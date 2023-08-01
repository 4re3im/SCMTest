<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class CupCompetitionHtmlHelper {
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
	
	public function html2text($str){
		require_once(DIR_PACKAGES.'/cup_content/libraries/html2text.php');
		$h2t =& new html2text($str);
		return $h2t->get_text();
	}


}
