<?php
switch ($a) {
	case 'pinyin' :
		require_once 	 ROOT_PATH.'includes/lib_pinyin.php';
		$chinese_str = trim($_POST['cstr']);
			if($chinese_str){
				echo strtolower(Pinyin($chinese_str,1));
			}
		break;
	case 'upload_puke' : 
		$classid = $input['photoid'] ? $input['photoid'] : '';
		if(empty($input['photoid'])){
			echo 0;
			exit;
		}
		if(empty($input['picaddress'])){
			echo 0;
			exit;
		}
		$total_coumt = $db->getOne("select count(*) as ct from {$tablepre}puke where classid = '$classid'");
		$db->query("insert into {$tablepre}puke (classid,picaddress,orderby) values ('".$classid."','".$input['picaddress']."','".$total_coumt."')");
		
		if($insert_id = $db->insert_id()){
			echo $insert_id;
			exit;
		}else{
			echo 0;
			exit;
		}
		break;
	case 'del_puke_pic' :
		$pukeid = $input['id'];
		$db->query("delete from {$tablepre}puke where pukeid = '$pukeid'");
		if($db->affected_rows() > 0){
			echo 1;
		}else{
			echo 0;
		}
		break;
}
