<?php
require_once('library/pearls.php');
if (isset($_GET['request'])) {
	$req_arr = explode("/" , $_GET['request']);
	if(isset($req_arr[0]) && $req_arr[0] != '') {
		$controller = $req_arr[0];
	} else {
		$controller = "index";
	}
	if(isset($req_arr[1]) && $req_arr[1] != '') {
		$action = $req_arr[1];
	} else {
		$action = "run";
	}
	if(isset($req_arr[2]) && $req_arr[2] != '') {
		$id = $req_arr[2];
	} else {
		$id = "";
	}
} else {
	$controller = "index";
	$action = "run";
}

$question_actions = array("create", "update", "delete");

if($controller == 'question' && !in_array($action, $question_actions) || $controller == 'space' && !in_array($action, $question_actions) || $controller == 'post' && !in_array($action, $question_actions) ) {
	if($controller == 'post') { 
		$controller = 'question';
	}
	$id = $action; 
	$action = 'read';
}

require_once(LIBRARY_PATH ."routes.php");

#Current Version: 3.1 - 11/08/2020

?>