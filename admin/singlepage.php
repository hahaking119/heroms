<?php
loadModel('singlePage');
$singlePage = new singlePage();
switch ($a) {
	case 'ls' : 
	if($ls = $singlePage->getSingleList()){
		$smarty->assign('singles',$ls);
	}else{
		$smarty->assign('nullmsg','暂时没有内容');
	}
	$smarty->display('singlepage_ls.htm');
	break;
	case 'addindex' :
		$time = date('y-m-d h:i:s a',$systime);
		$tpl_list = $singlePage->getTplList();
		$smarty->assign('tpl_list',$tpl_list);
		$smarty->assign('time',$time);
		$smarty->display('singlepage_add.htm');
		break;
	case 'add' :
		$title = trim($_POST['title']);
		$title = htmlspecialchars(strip_tags($title));
		$content = $_POST['content'];
		$created = strtotime($_POST['created']);
		$state = intval($_POST['status']);
		$static_name = $_POST['static_name'];
		$tpl_name = trim($_POST['tpl_name']);
		$sql = "INSERT INTO {$tablepre}singlepage (title,content,created,updated,state,static_name,tpl_name) VALUES ('$title','$content','$created','$systime','$state','$static_name','$tpl_name')";
		$db->query($sql);
		if($nid = $db->insert_id()){
			$static_name = $static_name ? $static_name : 'content_view_'.$nid;
			$html_content = file_get_contents($hostname.'/singlepage.php?id='.$nid);
			//如果有路径信息则须先创建路径
			if(preg_match('/([^\/]*)\/?/i',$static_name,$matches)){
				$fold_path = substr($static_name,0,strrchr($static_name,'/'));
				make_dir($fold_path);
			}
			fopen($static_name.'.html','a');
			file_put_contents($static_name.'.html',$html_content);
			showmsg('成功添加单网页','admin.php?m=singlepage&a=ls','success');
		}
		break;
	case 'editindex' :
		$singleid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($singleid)){
			showmsg('没有要选择编辑的网页，操作禁止',PRE_URL);
		}
		$detail = $singleDetail = $singlePage->getSingleDetail($singleid);
		
		foreach ($detail as $k=>$v){
			$smarty->assign($k,$v);
		}
		$tpl_list = $singlePage->getTplList();
		$smarty->assign('tpl_list',$tpl_list);
		$smarty->display('singlepage_edit.htm');
		break;
	case 'edit' :
		$title = $_POST['title'];
		$content = $_POST['content'];
		$state = intval($_POST['status']);
		$singleid = intval($_POST['singleid']);
		$static_name = $_POST['static_name'];
		$tpl_name = trim($_POST['tpl_name']);
		$sql = "UPDATE {$tablepre}singlepage SET title = '$title',content = '$content',state = '$state',updated = '$systime',static_name = '$static_name',tpl_name = '$tpl_name' WHERE singleid = '$singleid'";
		$db->query($sql);
			$static_name = $static_name ? $static_name : 'content_view_'.$singleid;
			$html_content = file_get_contents($hostname.'/singlepage.php?id='.$singleid);
			if(preg_match('/([^\/]*)\/?/i',$static_name,$matches)){
				$fold_path = substr($static_name,0,strrchr($static_name,'/'));
				make_dir($fold_path);
			}
			fopen($static_name.'.html','a');
			file_put_contents($static_name.'.html',$html_content);
		if($db->affected_rows() > 0){
			showmsg('更新成功','?m=singlepage&a=ls','success');
		}else{
			showmsg('更新失败',PRE_URL);
		}
		break;
	case 'del' :
		$singleid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($singleid)){
			showmsg('没有要选择编辑的网页，操作禁止',PRE_URL);
		}
		if($singlePage->deleteSinglePage($singleid)){
			showmsg('成功删除该网页',PRE_URL,'success');
		}else{
			showmsg('删除失败',PRE_URL,'error');
		}
		break;
}