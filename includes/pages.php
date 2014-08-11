<?php

$user_id	= $_SESSION['USERID'];
$page		= $_REQUEST['pg'];
$action		= $_REQUEST['act'];

if(!empty($action) && $action == 'logout'){
	require_once ("includes/logout.php");
}else{	
	require_once ("includes/menu.php");	
	require_once ("includes/classes/page.class.php");
	if (empty($page)) {
		$page = 11;
	}
	$page = new page($user_id, $page);
	$page->reqPage();
}

?>