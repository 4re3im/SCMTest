<?php
/**
 * @author Ariel Tabag <atabag@cambridge.org> 2015
 */

defined('C5_EXECUTE') or die("Access Denied.");

class GoSupportController extends Controller {

	public function on_start() {

		$html = Loader::helper('html');
		$v = View::getInstance();
		$v->setTheme(PageTheme::getByHandle("go_theme"));
	}

	public function view() {
		// ANZGO-3013
		CupGoLogs::trackUser('Support', 'View', '');
	}

}

?>