<?php
/**
 * Description of MenuController
 *
 * @author paulbalila
 */
class GoMenuController extends Controller {
    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));
    }
    
    public function view() {
        
    }
}
