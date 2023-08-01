<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));


Loader::model('block_main_carousel/model', 'cup_content');

$carousel = new CupBlockMainCarousel();

if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0){
	$carousel = new CupBlockMainCarousel('AU');
}else{
	$carousel = new CupBlockMainCarousel('NZ');
}

echo $carousel->toResultJSON();