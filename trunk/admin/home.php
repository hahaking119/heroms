<?php
/**
 * index.php 
 * 后台主页面文
 */
switch ($a){
	case 'index' :
		$smarty->display('admin_frame_index.htm');
		break;
	case 'header' :
		$smarty->display('admin_frame_header.htm');
		break;
	case 'menu' :
		$smarty->display('admin_frame_menu.htm');
		break;
	case 'main' :
		//$smarty->assign('phpinfo',phpinfo());
		$smarty->display('admin_frame_main.htm');
		break;
}
?>