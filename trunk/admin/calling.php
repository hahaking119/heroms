<?php
//投票管理
loadModel('calling');
$calling = new calling();
switch ($a) {
	case 'ls' :
		$callinglist = $calling->getCallingList();
		$smarty->assign('cats',$callinglist);
		$smarty->display('calling.htm');
		break;
	case 'add' :
		$smarty->display('calling.htm');
		break;
	case 'save' :
		$catname = trim($_POST['catname']);
		$keywords = trim($_POST['keywords']);
		$description = $_POST['description'];
		$catdir = trim($_POST['catdir']);
		if( empty($catname) || empty($catdir) ){
			showmsg('产品分类名称或者分类目录不能为空!',PRE_URL,'error');
		}
		if(strpos('/',$catdir)) showmsg('栏目名称不合法，请去掉“/”!',PRE_URL,'error');
		$sort = intval($_POST['sort']);
		$sql = "INSERT INTO {$tablepre}calling (catname,seo_keywords,seo_description,listorder,catdir) ".
			   "VALUES ('$catname','$keywords','$description','$sort','$catdir')";
		$db->query($sql);
		if($db->insert_id()){
			showmsg('添加产品分类成功!',PRE_URL,'success');
		}
		break;
	case 'editindex' :
		$catid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($catid)){
			showmsg('请选择要编辑的栏目，操作禁止',PRE_URL);
		}
		$catdetail = $calling->getCallingDetail($catid);
		foreach($catdetail as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->display('calling.htm');
		break;
	case 'edit' :
		$catid = intval($_POST['catid']);
		$catname = trim($_POST['catname']);
		$keywords = trim($_POST['keywords']);
		$description = $_POST['description'];
		$sort = intval($_POST['sort']);
		$sql = "UPDATE {$tablepre}calling SET catname = '$catname',".
				"seo_keywords = '$keywords',seo_description = '$description',listorder = '$sort' WHERE catid = '$catid'";
		$db->query($sql);
		showmsg($catname.'产品分类更新成功!','?m=calling&a=ls','success');
		break;
		
	case 'del' :
		$pid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($pid)){
			showmsg('没有要选择删除的分类，操作禁止',PRE_URL);
		}
		if($calling->DelProductById($pid)){
			showmsg('分类删除成功',PRE_URL,'success');
		}else{
			showmsg('删除失败可能分类不存在',PRE_URL);
		}
		break;
}

function trimall($v){
	$v = trim($v);
	$s = empty($v) ? false : true;
	return $s;
}