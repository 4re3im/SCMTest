<?php

/**
 * PoweredByHotmaths landing page controller
 * @author Shane Camus
 */

class PoweredByHotmathsController extends Controller
{
  public function on_start()
  {
    $v = View::getInstance();
    $v->setTheme(PageTheme::getByHandle('education'));
    // SB-341 added by mabrigos 20190917
    $html = Loader::helper('html');
    $this->addHeaderItem($html->javascript('googleTagManager.js', 'go_theme'));
  }
}
