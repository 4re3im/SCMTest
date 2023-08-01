<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));


Loader::model('hsc_slider', 'cup_competition');

$carousel = new HscSlider();
echo $carousel->toResultJSON();