<?php
/**
 * 投票模型
 */
class calling
{
	var $db;
	var $count = 0;
	function __construct(){
		$this->calling();
	}
	
	function calling(){
		$this->db = $GLOBALS['db'];
	}
	
	function getCallingList(){
		$sql = "SELECT * from {$GLOBALS['tablepre']}calling order by listorder asc";	
		$data = $this->db->getAll($sql);
		if($data){
			return $data;
		}
		return array();
	}
	
	function getCallingDetail($pid){
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}calling  WHERE catid = '$pid' ";
		return $this->db->getRow($sql);	
	}
	//给定一个选项数组 计算比例
	/**
	 * Array([0]=>array(1,'dsfd',3))
	 *
	 * @param array $array
	 */
	
	function DelProductById($id){
		$sql = "DELETE FROM {$GLOBALS['tablepre']}calling WHERE catid = '$id'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}
			return false;
	}
	
}