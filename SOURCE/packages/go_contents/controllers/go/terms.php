<?php
/**
 * Description of activate
 *
 * @author paulbalila
 */
class GoTermsController extends Controller {
    
    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
    }
    
    public function view() {
    }
}
