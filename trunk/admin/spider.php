<?php
include_once ROOT_PATH.'includes/cls_snoopy.php';
switch ($a) {
	case 'pinyin' :
		require_once 	 ROOT_PATH.'includes/lib_pinyin.php';
		$chinese_str = trim($_POST['cstr']);
			if($chinese_str){
				echo strtolower(Pinyin($chinese_str,1));
			}
		break;
}
