<?php
loadModel('friendlink');
$friendlink = new friendlink();
switch ($a) {
	case 'ls' :
		$links = $friendlink->getLinks(100);
		$smarty->assign('links',$links);
		$smarty->display('friendlink_ls.htm');
		break;
	case 'add':
		$sitename = trim($_POST['sitename']);
		$siteurl = $_POST['siteurl'];
		$orderby = intval($_POST['orderby']);
		$sitedesc = $_POST['sitedesc'];
		$linkid = $friendlink->addLink($sitename,$siteurl,$sitedesc,$orderby);
		showmsg('成功添加友情链接','admin.php?m=friendlink&a=ls','success');
		break;
	case 'editindex':
		$linkid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($linkid)){
			$linkid('没有要选择编辑的网页，操作禁止',PRE_URL);
		}
		$links = $friendlink->getLinkDetail($linkid);
		$smarty->assign('link',$links);
		$smarty->display('friendlink_edit.htm');
		break;
	case 'edit':
		$sitename = trim($_POST['sitename']);
		$siteurl = $_POST['siteurl'];
		$orderby = intval($_POST['orderby']);
		$sitedesc = $_POST['sitedesc'];
		$linkid = intval($_POST['linkid']);
		$ok = $friendlink->updateLink($linkid,$sitename,$siteurl,$sitedesc,$linkid);
		if($ok){
			showmsg('成功修改友情链接','admin.php?m=friendlink&a=ls','success');
		}else{
			showmsg('修改失败','admin.php?m=friendlink&a=ls','error');
		}
		break;
	case 'del':
		$linkid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($linkid)){
			$linkid('没有要选择编辑的网页，操作禁止',PRE_URL);
		}
		$ok = $friendlink->delLink($linkid);
		if($ok){
			showmsg('成功删除友情链接','admin.php?m=friendlink&a=ls','success');
		}else{
			showmsg('删除失败','admin.php?m=friendlink&a=ls','error');
		}
		break;
}
?>
	 