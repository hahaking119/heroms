<?php
//投票管理
loadModel('vote');
$vote = new vote();
switch ($a) {
	case 'ls' :
		$votelist = $vote->getVoteList();
		$smarty->assign('votelist',$votelist);
		$smarty->display('vote_ls.htm');
		break;
	case 'addindex' :
		$smarty->display('vote_add.htm');
		break;
	case 'add' :
		$title = trim($_POST['title']);
		$description = $_POST['description'];
		$type = intval($_POST['type']);
		$username = $_SESSION['username'];
		$options = doOptions($_POST['option']);
		$sql = "INSERT INTO {$tablepre}vote (title,description,type,created,username) VALUES ('$title','$description','$type','$systime','$username')";
		$db->query($sql);
		$voteid = $db->insert_id();
		foreach($options as $k=>$v){
		$sql_option = "INSERT INTO {$tablepre}vote_option (voteid,orderby,options) VALUES ('$voteid','$k','$v')";
		$db->query($sql_option);
		}
		showmsg('投票添加成功',PRE_URL,'success');
		break;
	case 'editindex' :
		$voteid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($voteid)){
			showmsg('没有要选择编辑的投票，操作禁止',PRE_URL);
		}
		$catinfo = $vote->getOneVote($voteid);
		foreach($catinfo as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->display('vote_edit.htm');
		break;
	case 'edit' :
		$title = trim($_POST['title']);
		$description = $_POST['description'];
		$voteid = intval($_POST['voteid']);
		$type = intval($_POST['type']);
		$username = $_SESSION['username'];
		$options = doOptions($_POST['option']);
		$sql = "UPDATE {$tablepre}vote SET title = '$title',description = '$description',type = '$type',username = '$username' WHERE voteid = '$voteid'";
		$db->query($sql);
		$vote->DelOptionsById($voteid);
		foreach($options as $k=>$v){
		$sql_option = "INSERT INTO {$tablepre}vote_option (voteid,orderby,options) VALUES ('$voteid','$k','$v')";
		$db->query($sql_option);
		}
		showmsg($title.'投票编辑成功',PRE_URL,'success');
		break;
		
	case 'del' :
		$voteid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($voteid)){
			showmsg('没有要选择删除的投票，操作禁止',PRE_URL);
		}
		if($vote->DelVoteById($voteid)){
			showmsg('投票删除成功',PRE_URL,'success');
		}else{
			showmsg('删除失败可能投票不存在',PRE_URL);
		}
		break;
}
function doOptions($arr){
	$option = array_filter($arr,'trimall');
	return array_values($option);
	
}
function trimall($v){
	$v = trim($v);
	$s = empty($v) ? false : true;
	return $s;
}