<?php
/**
 * Description of controller
 *
 * @author paulbalila
 */
class GoController extends Controller {
    
    
    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
    }
    
    public function view() {
        
    }
}
