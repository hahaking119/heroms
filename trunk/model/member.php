<?php
class member 
{
	var $db;
	var $tablepre;
	var $count;
	var $table;
	var $memberfiled;
	function __construct(){
		$this->member();
	}
	function member(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
		$this->table = $this->tablepre.'member';
		$this->memberfiled = $this->tablepre.'memberfield';
	}
	//获取成员全部信息
	function getMemberInfo($mid){
		$sql = "select * from {$this->table} where userid = $mid";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
	}
	//获取会员附加信息
	function getMemberfieldInfo($mid){
		$sql = "select * from {$this->memberfiled} where userid = $mid";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
	}
	//保存成员信息
	function saveMemberInfo($mid,$set){
		$set_str = '';
		$set = is_array($set) ? $set : array($set);
		foreach ($set as $k=>$v){
			$set_str .= $k.'=\''.$v.'\',';
		}
		$set_str = substr($set_str,0,-1);
		$sql = "update {$this->table} set ".$set_str." where userid = {$mid}";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//保存成员附加信息
	function saveMemberfieldInfo($mid,$set){
		$set_str = '';
		$set = is_array($set) ? $set : array($set);
		foreach ($set as $k=>$v){
			$set_str .= $k.'=\''.$v.'\',';
		}
		$set_str = substr($set_str,0,-1);
		$sql = "update {$this->memberfiled} set ".$set_str." where userid = {$mid}";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//添加一个会员
	function addMember($userid,$username,$password,$email,$gender,$realname,$regip){
		$regtime = time();
		$password = trim($password);
		$password = md5($password);
		$sql = "insert into {$this->table} (userid,username,password,email,gender,realname,regip,regtime) values ('$userid','$username','$password','$email','$gender','$realname','$regip','$regtime')";
		if($this->db->query($sql)){
			$this->db->query("insert into {$this->memberfiled} (userid) values ('$userid')");
			return 1;
		}else{
			return 0;
		}
		
	}
	//检查是否存在这个会员
	//获取会员信息列表
	function getMemberList($page = 1,$pagesize = 10){
		$start = ($page-1)*$pagesize;	
		$sql = "select m.userid,m.username,m.realname,m.logintime, mf.xueli,mf.benke_xuyuan,mf.job_company from {$this->table} m left join {$this->memberfiled} mf on m.userid = mf.userid order by m.userid desc limit $start,$pagesize";
		$sql_c = "select count(*) from  {$this->table}";
		$c = $this->db->getOne($sql_c);
		$this->count = $c;
		if($rows = $this->db->getAll($sql)){
			return $rows;
		}
		return array();	
	}
	//搜索会员
	function searchMember($setarr = array(),$page = 1,$pagesize = 10){
		$start = ($page-1)*$pagesize;	
		$where = '';
		if(isset($setarr['username'])){
			$where .= "and m.username = '{$setarr['username']}' ";
		}
		if(isset($setarr['realname'])){
			$where .= "and m.realname = '{$setarr['realname']}' ";
		}
		if(isset($setarr['benke_bengin'])){
			$where .= "and mf.benke_bengin = '{$setarr['benke_bengin']}' ";
		}
		if(isset($setarr['xueli'])){
			$where .= "and mf.xueli = '{$setarr['xueli']}' ";
		}
		$sql = "select m.*,mf.* from {$this->table} m left join {$this->memberfiled} mf on m.userid = mf.userid where 1=1 ".$where."order by m.userid desc limit $start,$pagesize";
		$sql_c = "select count(*) from {$this->table} m left join {$this->memberfiled} mf on m.userid = mf.userid where 1=1 ".$where;
		$c = $this->db->getOne($sql_c);
		$this->count = $c;
		if($rows = $this->db->getAll($sql)){
			return $rows;
		}
		return array();	
	}
	//
	function updateLogin($uid,$realip){
		$time = time();
		$this->db->query("update {$this->table} set loginip = '$realip',logintime='$time',logintimes = logintimes + 1 where userid = '$uid'");
		return true;
	}
}
	