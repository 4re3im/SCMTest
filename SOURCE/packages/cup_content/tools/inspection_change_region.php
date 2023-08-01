<?php 
if(isset($_GET['region'])){
	$_SESSION['inspection_copy']['filter_region'] = $_GET['region'];
}