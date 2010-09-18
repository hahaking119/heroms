<?php
class demo 
{
	var $db;
	var $tablepre;
	var $count;
	function __construct(){
		$this->demo();
	}
	
	function demo(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}
	//获取案例列表
	public function getDemoList($page=1,$pagesize=10){
		$start = ($page-1)*$pagesize;
		$sql = "select * from {$this->tablepre}demo order by orderby desc,created desc limit $start,$pagesize";
		$count_sql = "SELECT COUNT(demoid) FROM {$this->tablepre}demo";
		$this->count = $this->db->getOne($count_sql);
		if($this->count < 1){
			return false;
		}else{
			return $this->db->getAll($sql);
		}
	}
	//获取案例详细信息
	function getDemoDetail($id){
		$sql = "SELECT * FROM {$this->tablepre}demo WHERE demoid = '$id'";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
		
	}
	
	function deleteDemoById($id){
		$this->db->query("delete from {$this->tablepre}demo where demoid = '$id'");
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
}