<?php
class photos 
{
	var $db;
	var $tablepre;
	var $count;
	function __construct(){
		$this->photos();
	}
	
	function photos(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}
	//获取案例列表
	public function getphotosList($page=1,$pagesize=10,$class_id = ''){
		$start = ($page-1)*$pagesize;
		$sql = "select * from {$this->tablepre}photos ";
		$c_sql = "select count(*) as c from {$this->tablepre}photos ";
		if($class_id){
			$class_id = strtoupper($class_id);
			$sql .= "where sindex = '$class_id' ";
			$c_sql .= "where sindex = '$class_id' ";
		}
		$sql .= "order by photoid desc limit $start,$pagesize";
		$this->count = $this->db->getOne($c_sql);
		if($this->count < 1){
			return false;
		}else{
			return $this->db->getAll($sql);
		}
	}
	//获取详细信息
	function getphotosDetail($id){
		$sql = "SELECT * FROM {$this->tablepre}photos WHERE photoid = '$id'";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
		
	}
	//
	function getPukeList($photoid,$page = 1,$pagesize = 20){
		$startid = ( $page -1 )*$pagesize;
		$sql = "SELECT * FROM {$this->tablepre}puke WHERE classid = '$photoid' order by orderby asc,pukeid asc limit $startid,$pagesize";
		$count_sql = "SELECT COUNT(classid) FROM {$this->tablepre}puke WHERE classid = '$photoid'";
		$this->count = $this->db->getOne($count_sql);
		if($this->count < 1){
			return false;
		}else{
			return $this->db->getAll($sql);
		}
	}
 	
	function deletephotosById($id){
		$this->db->query("delete from {$this->tablepre}photos where photoid = '$id'");
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	
	function deletepukeById($id){
		$this->db->query("delete from {$this->tablepre}puke where pukeid = '$id'");
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}

	function getPukeNumberById($id){
		$sql = "select count(*) as ct from {$this->tablepre}puke where photoid = '$id'";
		return $this->db->getOne($sql);
	}
	
}