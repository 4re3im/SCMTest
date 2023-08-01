<?php
// ANZGO-3216
// require redirect first
// this will help redirect links to Education Marketing links
//ANZGO-3898 modified by jbernardez 20181019
//re-enabling redirect.php as it was accidentally disabled

$exceptions =  array('myresources','logout','login','activate','support','');
$url =  explode('/', $_SERVER['REQUEST_URI']);
//bypass redirect for TNG pages
if (!isset($url[2]) || !in_array($url[2],$exceptions)){ require('redirect.php'); }
require('concrete/dispatcher.php');
