<?php
/**
 * 下载控制器
 */
loadModel('down');
$down = new down();
switch ($a) {
	case 'ls' :
		$pagesize = 20;
		$down_data = $down->getDownList();
		$totalrecored = $down->totalcount;
		$mutipage = mutipage('?m=down&a=ls&page=',$totalrecored,$page,$pagesize);
		$nullmsg = $down_data == false ? '暂时还没有记录' : ''; 
		$smarty->assign('mutipage',$mutipage);
		$smarty->assign('nullmsg',$nullmsg);
		$smarty->assign('downs',$down_data);
		$smarty->display('down_ls.htm');
		break;
	case 'addindex' :
		$smarty->assign('time',date('Y-m-d H:i:s'));;
		$smarty->display('down_add.htm');
		break;
	case 'add' :
		$check  = $down->checkData($_POST);
		if($check === true){
		foreach ($_POST as $k=>$v){
			$$k = $v;
		}
		$title = trim($title);
		$username = $_SESSION['username'];
		$created = isset($created) ? strtotime($created) : $systime;
		//处理上传的图片
		$sql = "INSERT INTO {$tablepre}down (
					`catid` ,`title` ,`tcolor` ,title_image,`file1` ,`url1`,`content` ,`username` ,`created` ,`updated` ,`status` )
					VALUES ('1','$title','$tcolor','$title_image','$fileaddress','$url1','$content','$username','$created','$systime','$status') ";
		$db->query($sql);
		showmsg('下载添加成功，继续添加下载吧',PRE_URL,'success');
		}else{
			showmsg($check,PRE_URL);
		}
		break;
	case 'editindex' :
		$downid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($downid)){
			showmsg('请选择你要编辑的下载，操作禁止',PRE_URL);
		}
		$downinfo = $down->getDownInfo($downid);
		foreach ($downinfo as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->display('down_edit.htm');
		break;
	case 'edit' :
		$check  = $down->checkData($_POST,'edit');
		if($check === true){
		foreach ($_POST as $k=>$v){
			$$k = $v;
		}
		$title = trim($title);
		$username = $_SESSION['username'];
		$sql = "UPDATE {$tablepre}down SET `title` ='$title',`tcolor` = '$tcolor',`title_image` = '$title_image',`file1` = '$fileaddress' ,`url1` = '$url1',`content` = '$content',`updated` = '$systime',`status` = '$status' WHERE downid = '$downid'";
		$db->query($sql);
		showmsg('更新下载成功',PRE_URL,'success');
		}else{
			showmsg($check,PRE_URL,'error');
		}
		break;
		
	case 'del' :
		$downid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($downid)){
			showmsg('没有要选择删除的下载，操作禁止',PRE_URL);
		}
		if($down->deleteDownById($downid)){
			showmsg('成功删除该条信息和相应的文件',PRE_URL,'success');
		}else{
			showmsg('删除失败，请检查你的权限，操作禁止',PRE_URL,'error');
		}
		break;

		
}
