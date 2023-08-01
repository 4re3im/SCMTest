<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tab_content_edit
 *
 * @author gerardbalila
 */
class GoProductEditorTabController extends Controller
{
    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_product_editor_theme"));
    }

    public function view()
    {
    }
}
