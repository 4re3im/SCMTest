<?php

/**
 * Queensland landing page controller
 * @author Maryjes Tanada 03/21/2018
 */

class QueenslandController extends Controller
{
  public function on_start()
  {
    $v = View::getInstance();
    $v->setTheme(PageTheme::getByHandle('queensland_theme'));
    // SB-341 added by mabrigos 20190917
    $html = Loader::helper('html');
    $this->addHeaderItem($html->javascript('googleTagManager.js', 'go_theme'));
  }
}
