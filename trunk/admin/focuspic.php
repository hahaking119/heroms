<?php
/**
 * focuspic.php
 * 焦点图片管理
 */
loadModel('focuspic');
$focuspic = new focuspic();
require_once ROOT_PATH.'includes/cls_image.php';
$img = new cls_image();
switch ($a) {
	case 'ls' :
		$pagesize = 20;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$focuslist = $focuspic->getFocusList(1,null,$page,$pagesize);
		if($focuslist){
			$smarty->assign('focus',$focuslist);
		}else{
			$smarty->assign('nullmsg',"暂时还没有焦点图");
		}
		$smarty->display('focuspic_ls.htm');
	break;
	case 'addindex' :
		$smarty->display('focuspic_add.htm');
		break;
	case 'add' :
		$channelid = intval($_POST['channelid']);
		$state = intval($_POST['state']);
		$order = empty($_POST['pic_order']) ? 1 : intval($_POST['pic_order']);
		$title = addslashes($_POST['pic_title']);
		$url = trim($_POST['pic_url']);	
		$picaddress = $_POST['picaddress'];
		if(empty($picaddress)){
			showmsg('您还没有上传焦点图片，请重新上传',PRE_URL,'error');
		}
		
		if($focuspic->addFocus($channelid,0,$title,$picaddress,$url,$order,$state)){
			$focuspic->makeXml('upanddown',5);
			showmsg($title.'->图片添加成功',PRE_URL,'success');
		}else{
			showmsg($title.'->图片添加失败',PRE_URL,'error');
		}
		
	break;
	case 'piceditindex' :
		$focusid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($focusid)){
			showmsg('请选择你要编辑的项目，操作禁止',PRE_URL);
		}
		if($focusdetail = $focuspic->focusDetail($focusid)){
			foreach($focusdetail as $k=>$v){
				$smarty->assign($k,$v);
			}
				$smarty->display('focuspic_edit.htm');
		}else{
				showmsg('你选择的处理项目不存在，操作禁止',PRE_URL);
		}
	
		break;
	case 'edit' :
		$id = isset($_POST['id']) ? intval($_POST['id']) : '';
		$channelid = intval($_POST['channelid']);
		$state = intval($_POST['state']);
		$order = empty($_POST['pic_order']) ? 1 : intval($_POST['pic_order']);
		$title = addslashes($_POST['pic_title']);
		$url = trim($_POST['pic_url']);	
		$picaddress = $_POST['picaddress'];
		if(empty($picaddress)){
			showmsg('您还没有上传焦点图片，请重新上传',PRE_URL,'error');
		}
		if(empty($id)){
			showmsg('对不起，你没有选择要处理的项目',PRE_URL,'error');
		}
		if($focuspic->editFocus($id,$channelid,0,$title,$picaddress,$url,$order,$state)){
			$focuspic->makeXml('upanddown',5);
			showmsg($title.'|->图片编辑成功',PRE_URL,'success');
		}else{
			showmsg($title.'|->图片编辑失败',PRE_URL,'error');
		}
		break;
	case 'picdel' :
		$focusid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($focusid)){
			showmsg('请选择你要处理的项目，操作禁止',PRE_URL);
		}
		if($focuspic->deleteFocusById($focusid)){
			$focuspic->makeXml('upanddown',5);
			showmsg('|->图片删除成功',PRE_URL,'success');
		}else{
			showmsg('|->图片删除失败',PRE_URL,'error');
		}
		break;
		
}