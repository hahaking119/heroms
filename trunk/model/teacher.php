<?php
class teacher 
{
	var $db;
	var $tablepre;
	var $count;
	function __construct(){
		$this->teacher();
	}
	
	function teacher(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}
	//获取教师列表
	public function getTeacherList($page=1,$pagesize=10,$group=''){
		$start = ($page-1)*$pagesize;
		if(empty($group)){
			$sql = "select * from {$this->tablepre}teachers order by orderby desc,created desc limit $start,$pagesize";
		}else{
			$sql = "select * from {$this->tablepre}teachers where groupid = '$group' order by orderby desc,created desc limit $start,$pagesize";
		}
		$count_sql = "SELECT COUNT(tid) FROM {$this->tablepre}teachers";
		$this->count = $this->db->getOne($count_sql);
		if($this->count < 1){
			return false;
		}else{
			return $this->db->getAll($sql);
		}
	}
	//获取教师详细信息
	function getTeacherDetail($id){
		$sql = "SELECT * FROM {$this->tablepre}teachers WHERE tid = '$id'";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
		
	}
	
	function deleteTeacherById($id){
		$this->db->query("delete from {$this->tablepre}teachers where tid = '$id'");
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
}