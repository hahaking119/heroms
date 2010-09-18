<?php
class juhui 
{
	var $db;
	var $tablepre;
	var $count;
	var $table;
	var $totalRecord=0;
	function __construct(){
		$this->juhui();
	}
	function juhui(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
		$this->table = $this->tablepre.'baoming';
		$this->juhui_table = $this->tablepre.'juhui';
	}
	//
	function addBaoming($aid,$uid){
		$time = time();
		$sql = "insert into {$this->table} (aid,uid,created) values ('$aid','$uid',$time)";
		$this->db->query($sql);
		return $this->db->insert_id();
	}
	function checkBaoming($aid,$uid){
		$sql = "select count(*) from {$this->table} where aid = '$aid' and uid = '$uid' ";
		$rs = $this->db->getOne($sql);
		if($rs > 0){
			return true;
		}
		return false;
	}
	//删除报名
	function delBaoming($id){
		$sql = "delete from {$this->table} where bmis='$id'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//获取列表
	function getBaoming($aid){
		$sql = "select m.realname,m.userid,m.username,bm.created from {$this->table} bm left join {$GLOBALS['tablepre']}member m on m.userid = bm.uid where aid = '$aid' order by bmid desc ";
		if($rows = $this->db->getAll($sql)){
			foreach($rows as $k=>$v){
				$rows[$k]['time'] = date('Y年y月d日',$v['created']);
			}
			return $rows;
		}else{
			return array();
		}
	}
	function getBaomingNum($aid){
		return $this->db->getOne("select count(*) from {$this->table} where aid = '$aid'");
	}
	function getJuhuiList($uid=null,$page=1,$pagesize=10){
		$start = ($page-1)*$pagesize;
		if($uid){
			$sql = "select * from {$this->juhui_table} where uid = '$uid' order by top_flag desc,created desc limit $start,$pagesize ";
		}else{
			$sql = "select * from {$this->juhui_table} order by top_flag desc,created desc limit $start,$pagesize ";
		}
		$result = $this->db->getAll($sql);
		if($result){
			return $result;
		}
		return array();
		
	}
	//获取所有通过的聚会
	function getAllJuhuiList($page=1,$pagesize=10,$switch=1){
		$start = ($page-1)*$pagesize;
		$sql = "select * from {$this->juhui_table} where switch = '$switch'  order by top_flag desc ,created desc limit $start,$pagesize";
		$result = $this->db->getAll($sql);
		$count = $this->db->getOne("select count(juhuiid) as c from {$this->juhui_table} where switch = '$switch' order by top_flag desc,created desc");
		$this->totalRecord = $count;
		if($result){
			return $result;
		}
		return array();
		
	}
	function getJuhuiDetail($aid,$uid = null){
		$aid = intval($aid);
		if($uid){
			$sql = "select jh.* from {$this->juhui_table} jh  where jh.juhuiid = '$aid' and jh.uid = '$uid'";
			$rs =  $this->db->getRow($sql);
		}else{
			$sql = "select jh.* from {$this->juhui_table} as jh where jh.juhuiid = '$aid'";
			$rs = $this->db->getRow($sql);
		}
		if($m_rs = $this->db->getRow("select username from {$GLOBALS['tablepre']}member where userid = ".$rs['uid'])){
			if(empty($m_rs['username'])){
				$rs['username'] = $m_rs['username'];
			}else{
				$rs['username'] = '管理员';
			}
		}else{
			$rs['username'] = '管理员';
		}
		return $rs;
	}
	function delJhui($aid,$uid=null){
		$aid = intval($aid);
		if($uid){
			return $this->db->query("delete from {$this->juhui_table} where juhuiid = '$aid' and uid = '$uid'");
		}
		return $this->db->query("delete from {$this->juhui_table} where juhuiid = '$aid'");
	}
	//获取最新的聚会
	function getLastJuhui($id){
		$sql = "SELECT juhuiid,title,time,clicks FROM {$GLOBALS['tablepre']}juhui where switch = 1 order by top_flag desc,created desc";
		if($result = $this->db->getRow($sql)){
			return $result;
		}
		return array();
		
	}
}
?>