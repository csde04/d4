<?
	$here = isset($_REQUEST['here']) ? $_REQUEST['here'] : "";
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
	
// determines main menu

	include "./view/menu.php";
	
// determines sub_menu

	include "./view/sub_menu.php";
	
// determines main page depending on $here and $mode

	include "./view/main_page.php";
	
	
?>