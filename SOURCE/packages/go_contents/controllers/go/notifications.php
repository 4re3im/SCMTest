<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BrowseController
 *
 * @author paulbalila
 */
class GoNotificationsController extends Controller {
    
    public function on_start() {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));
    }
    
    public function view() {
        
    }
}
