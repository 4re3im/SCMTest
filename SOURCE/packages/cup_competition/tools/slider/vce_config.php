<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));


Loader::model('vce_slider', 'cup_competition');

$carousel = new VceSlider();
echo $carousel->toResultJSON();