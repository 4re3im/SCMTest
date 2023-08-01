<?php
if(!isset($_SESSION['inspection_copy']['order_list'])){
	$_SESSION['inspection_copy']['order_list'] = array();
}

$result = array('result'=>'failure');
if(isset($_GET['title_id'])){
	if(!in_array($_GET['title_id'], $_SESSION['inspection_copy']['order_list'])){
		$_SESSION['inspection_copy']['order_list'][]  = $_GET['title_id'];
	}
	
	$result = $result = array('result'=>'success', 'total'=>count($_SESSION['inspection_copy']['order_list']));
}

echo json_encode($result);