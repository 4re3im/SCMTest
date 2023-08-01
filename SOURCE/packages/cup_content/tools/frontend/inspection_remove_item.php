<?php
if(!isset($_SESSION['inspection_copy']['order_list'])){
	$_SESSION['inspection_copy']['order_list'] = array();
}

$result = array('result'=>'failure');
if(isset($_GET['title_id'])){
	if(($key = array_search($_GET['title_id'], $_SESSION['inspection_copy']['order_list'])) !== false) {
		unset($_SESSION['inspection_copy']['order_list'][$key]);
	}
	
	$result = array('result'=>'success', 'total'=>count($_SESSION['inspection_copy']['order_list']));
}

echo json_encode($result);