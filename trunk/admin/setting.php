<?php
/**
 * setting.php
 * 处理管理员
 */
loadModel('category');
switch ($a) {
	case 'ls' :
		$settings = $db->getAll("SELECT * FROM {$tablepre}site_config");
		foreach ($settings as $k=>$v){
			$smarty->assign($k,$v);			
		}
		$smarty->display('setting_ls.htm');
		break;	
	case 'update' :
		
		break;
}
?>