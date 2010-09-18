<?php
class user
{
	var $db;
	function __construct(){
		$this->user();
	}
	function user(){
		$this->db = $GLOBALS['db'];
	}
	//检查用户名是否存在
	function checkExistUser($username){
		$sql = "SELECT COUNT(uid) AS c FROM {$GLOBALS['tablepre']}users WHERE username = '$username'";
		$rs = $this->db->getOne($sql);
		if($rs > 0){
			return true;
		}else{
			return false;
		}
	}
	
	//获得某一个用户的全部信息
	function getUserInfo($user,$type = 'ID'){
		if($type == 'ID'){
			$sql = "SELECT * FROM {$GLOBALS['tablepre']}users WHERE uid = '$user'";
		}else{
			$sql = "SELECT * FROM {$GLOBALS['tablepre']}users WHERE username = '$user'";
		}
		if($rs = $this->db->getRow($sql)){
			$rs['groupname'] = $this->id2name($rs['groupid']);
			return $rs;
		}else{
			return false;
		}
	}
	//获得用户列表
	function getUserlist(){
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}users ORDER BY uid DESC";
		if($rs = $this->db->getAll($sql)){
			return $this->doUserGroup($rs);
		}else{
			return false;
		}
	}
	//检查登陆
	/**
	 * 返回-1 表示用户不存在 -2 表示密码错误 1 表示正确
	 *
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	function checkUserLogin($username,$password){		
		$username = trim($username);
		$password = trim($password);
		if(!$this->checkExistUser($username)){
			return -1;
		}else{
			if(empty($password)){
				return -2;
			}
			$userinfo = $this->getUserInfo($username,'NAME');
			$password = md5(strtolower($password));
			if($userinfo['password'] !== $password){
				return -2;
			}else{
				return $userinfo;
			}			
		}
	}
	
	function doUserGroup($arr){
		if(empty($arr) || !is_array($arr)){
			return false;
		}
		foreach($arr as $k=>$v){
			$arr[$k]['groupname'] = $this->id2name($v['groupid']);
		}
		return $arr;
	}
	//更新用户信息
	function updateLoginInfo($id){
		$ip = real_ip();
		$sql = "UPDATE {$GLOBALS['tablepre']}users SET lastlogintime = {$GLOBALS['systime']} ,lastloginip = '$ip',logincount = logincount + 1 WHERE uid = '$id'";
		$this->db->query($sql);
	}
	//删除用户
	function DelUserById($uid){
		if($uid == 1){
			return 1;
		}
		if(!$this->checkExistUser($uid)){
			return -1;
		}
		$sql = "DELETE FROM {$GLOBALS['tablepre']}users WHERE uid = '$uid'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return -2;
		}
	}
	//convert
	function id2name($id){
		switch ($id){
			case 1 :
				return '超级管理员';
				break;
			case 2 :
				return '普通管理员';
				break;
			case 3 :
				return '栏目管理员';
				break;
			case 4 :
				return '网站编辑';
				break;
		}
	}
	
}