<?php
class friendlink 
{
	var $db;
	var $tablepre;
	var $count;
	var $table;
	var $totalRecord=0;
	function __construct(){
		$this->friendlink();
	}
	function friendlink(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
		$this->table = $this->tablepre.'friendlink';
	}
	//添加链接
	function addLink($sitename,$siteurl,$sitedesc,$orderby=1){
		$sql = "insert into {$this->table} (sitename,siteurl,sitedesc,orderby) values ('$sitename','$siteurl','$sitedesc','$orderby')";
		$this->db->query($sql);
		return $this->db->insert_id();
	}
	//修改链接
	function updateLink($linkid,$sitename,$siteurl,$sitedesc,$orderby=1){
		$sql = "update {$this->table} set sitename = '$sitename' ,siteurl = '$siteurl',sitedesc = '$sitedesc',orderby = '$orderby' where linkid='$linkid'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//删除链接
	function delLink($linkid){
		$sql = "delete from {$this->table} where linkid='$linkid'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//获取列表
	function getLinks($nums = 6){
		$sql = "select * from {$this->table} order by orderby desc limit 0,$nums";
		if($rows = $this->db->getAll($sql)){
			return $rows;
		}else{
			return array();
		}
	}
	
	function getLinkDetail($linkid){
		$linkid = (int)$linkid;
		$sql = "SELECT * FROM {$this->table}  WHERE linkid = '$linkid'";
		if($detail = $this->db->getRow($sql)){
			return $detail;
		}else{
			return false;
		}
	}
}
?>