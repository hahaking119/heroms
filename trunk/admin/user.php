<?php
/**
 * user.php
 * 处理管理员
 */
loadModel('user');
$user = new user();
switch ($a) {
	case 'ls' :
		$users = $user->getUserlist();
		$smarty->assign('users',$users);
		$smarty->display('user_ls.htm');
		break;	
	case 'add' :
		foreach($_POST as $k=>$v){
			$$k = $v;
		}
		$username = trim($username);
		//检查用户是否重复
		if(empty($username) || empty($password1) || empty($password2)){
			showmsg('必填项不能为空！',PRE_URL,'error');
		}
		if($user->checkExistUser($username)){
			showmsg('用户名重复，换个用户名吧^_^',PRE_URL);
		}
		if($password1 !== $password2){
			showmsg('两次密码不匹配请重新输入',PRE_URL);
		}
		$password = md5($password1);
		$groupid = intval($groupid);
		$sql = "INSERT INTO {$tablepre}users (username,password,truename,telephone,address,created,updated,groupid) ".
				"VALUES ('$username','$password','$truename','$telephone','$address','$systime','$systime','$groupid')";
		$db->query($sql);
		if($db->insert_id()){
			showmsg('添加管理员成功',PRE_URL,'success');
		}else{
			showmsg('添加管理员失败',PRE_URL);
		}
		break;
	case 'editindex' :
		$uid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($uid)){
			showmsg('请选择要编辑的用户',PRE_URL,'error'); 
		}
		$userinfo = $user->getUserInfo($uid);
		foreach ($userinfo as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->display('user_edit.htm');
		break;
	case 'edit' :
		foreach($_POST as $k=>$v){
			$$k = $v;
		}
		//检查用户是否重复
		$groupid = intval($groupid);
		if(!empty($password)){
			if($password1 !== $password2){
			showmsg('两次密码不匹配请重新输入',PRE_URL);
			}
		$password = md5($password);
		$sql = "UPDATE {$tablepre}users SET password = '$password',truename = '$truename',telephone = '$telephone',address = '$address',updated = '$systime',groupid = '$groupid' WHERE uid = '$uid'";
		}else{
		$sql = "UPDATE {$tablepre}users SET truename = '$truename',telephone = '$telephone',address = '$address',updated = '$systime',groupid = '$groupid' WHERE uid = '$uid'";
		}
		$db->query($sql);
		showmsg('更新用户信息成功',PRE_URL,'success');
		break;
	case 'del' :
		$uid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($uid)){
			showmsg('请选择要删除的用户',PRE_URL,'error'); 
		}elseif($uid == $userinfo['uid']){
			showmsg('你不能删除自己',PRE_URL,'error'); 
		}
		$delinfo = $user->DelUserById($uid);
		if($delinfo == -1){
			showmsg('用户不存在',PRE_URL,'error');
		}elseif($delinfo == 1 ){
			showmsg('创始人无法删除',PRE_URL,'error');
		}elseif($delinfo === true){
			showmsg('删除用户成功',PRE_URL,'success');
		}
		break;
	case 'login' :
		$smarty->display('user_login.htm');
		break;
	case 'checkin' :
		$checkinfo = $user->checkUserLogin($_POST['username'],$_POST['password']);
		if($checkinfo == -1){
			$error = '用户名不存在';
			$smarty->assign('errormsg',$error);
			$smarty->display('user_login.htm');
		}elseif($checkinfo == -2){
			$error = '密码错误';
			$smarty->assign('errormsg',$error);
			$smarty->display('user_login.htm');
		}else{
			$user->updateLoginInfo($checkinfo['uid']);
			$_SESSION['uid'] = $checkinfo['uid'];
			$_SESSION['username'] = $checkinfo['username'];
			$_SESSION['groupid'] = $checkinfo['groupid'];
			$_SESSION['groupname'] = $checkinfo['groupname'];
			$_SESSION['truename'] = $checkinfo['truename'];	
			showmsg($checkinfo['truename']."欢迎您，您已成功登陆".$admincp,'admin.php','success');	
		}
		break;
	case 'logout' :
		session_destroy();
		showmsg("成功退出系统",'index.php','success');	
		break;
		
}
?>