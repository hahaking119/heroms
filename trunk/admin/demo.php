<?php
/**
 * demo.php
 * 焦点图片管理
 */
loadModel('demo');
$demo = new demo();
require_once ROOT_PATH.'includes/cls_image.php';
$img = new cls_image();
switch ($a) {
	case 'ls' :
		$pagesize = 20;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$demolist = $demo->getDemoList($page,$pagesize);
		if($demolist){
			$smarty->assign('demo',$demolist);
		}else{
			$smarty->assign('nullmsg',"暂时还没有案例");
		}
		$smarty->display('demo_ls.htm');
	break;
	case 'addindex' :
		$time = date('Y-m-d');
		$smarty->assign('time',$time);
		$smarty->display('demo_add.htm');
		break;
	case 'add' :
		$membertitle = addslashes($_POST['membername']);
		$description = addslashes($_POST['content']);
		$website = trim($_POST['website']);	
		$orderby = trim($_POST['orderby']);	
		$picaddress = $_POST['picaddress'];
		$username = $_SESSION['username'];
		$created = isset($created) ? strtotime($created) : $systime;
		if(empty($picaddress)){
			showmsg('您还没有上传案例图片，请重新上传',PRE_URL,'error');
		}
		$db->query("insert into {$tablepre}demo (membername,picaddress,website,description,orderby,created,updated) values ('$membertitle','$picaddress','$website','$description','$orderby','$created','$created')");
		if($db->insert_id()){
			showmsg($membertitle.'->案例添加成功',PRE_URL,'success');
		}else{
			showmsg($membertitle.'->案例添加失败',PRE_URL,'error');
		}
		
	break;
	case 'piceditindex' :
		$demoid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($demoid)){
			showmsg('请选择你要编辑的项目，操作禁止',PRE_URL);
		}
		if($demodetail = $demo->getDemoDetail($demoid)){
			foreach($demodetail as $k=>$v){	
				$$k = $v;
				$smarty->assign($k,$v);
			}
			$created = date('Y-m-d',$created);
			$smarty->assign('created',$created);
				$smarty->display('demo_edit.htm');
		}else{
				showmsg('你选择的处理项目不存在，操作禁止',PRE_URL);
		}
	
		break;
	case 'edit' :
		$id = intval($_POST['demoid']);
		$membertitle = addslashes($_POST['membername']);
		$description = addslashes($_POST['content']);
		$website = trim($_POST['website']);	
		$orderby = trim($_POST['orderby']);	
		$picaddress = $_POST['picaddress'];
		$username = $_SESSION['username'];
		$created = isset($created) ? strtotime($created) : $systime;
		if(empty($picaddress)){
			showmsg('您还没有上传案例图片，请重新上传',PRE_URL,'error');
		}
		if(empty($id)){
			showmsg('对不起，你没有选择要处理的项目',PRE_URL,'error');
		}
		$sql = "UPDATE {$tablepre}demo SET membername = '$membertitle',picaddress = '$picaddress',website='$website',description='$description',orderby='$orderby',created='$created',updated='$systime' WHERE demoid = '$id'";
		$db->query($sql);
		if($db->affected_rows() >0){
			showmsg($membertitle.'|->案例编辑成功',PRE_URL,'success');
		}else{
			showmsg($membertitle.'|->案例编辑失败',PRE_URL,'error');
		}
		break;
	case 'picdel' :
		$demoid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($demoid)){
			showmsg('请选择你要处理的项目，操作禁止',PRE_URL);
		}
		if($demo->deleteDemoById($demoid)){
			showmsg('|->案例删除成功',PRE_URL,'success');
		}else{
			showmsg('|->案例删除失败',PRE_URL,'error');
		}
		break;
		
}