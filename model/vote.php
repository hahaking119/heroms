<?php
/**
 * 投票模型
 */
class vote
{
	var $db;
	function __construct(){
		$this->vote();
	}
	
	function vote(){
		$this->db = $GLOBALS['db'];
	}
	
	function getVoteList($page = 1,$pagesize = 10){
		$startrecored = ($page - 1)*$pagesize;
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}vote ORDER BY voteid DESC";
		$vote_data = $this->db->getAll($sql);
		foreach($vote_data as $k => $v){
			$options = $this->getVoteDetail($v['voteid']);
			$vote_data[$k]['options'] =  $options;
			$vote_data[$k]['options'] = $this->countVote($vote_data[$k]['options']);
		}
		return $vote_data;
	}
	
	function getVoteDetail($voteid){
		$sql = "SELECT optionid,orderby,options,votes FROM {$GLOBALS['tablepre']}vote_option  WHERE voteid = '$voteid' ";
		return $this->db->getAll($sql);	
	}
	//给定一个选项数组 计算比例
	/**
	 * Array([0]=>array(1,'dsfd',3))
	 *
	 * @param array $array
	 */
	function countVote($arr = array()){
		$total = 1;
		foreach($arr as $k=>$v){
			$total += $v['votes'];
		}
		foreach ($arr as $key=> $value){
			$arr[$key]['total'] = $total;
			$propor = round(($value['votes']*100)/$total,3);
			$arr[$key]['scale'] = number_format($propor,0,'%','');
		}
		return $arr;
	}
	//获得某个投票的详细信息
	function getOneVote($id){
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}vote WHERE voteid = '$id'";
		$rs = $this->db->getRow($sql);
		$options = $this->getVoteDetail($id);
		$rs['options'] =  $options;
		$rs['options'] = $this->countVote($rs['options']);
		return $rs;
	}
	//删除一个投票
	function DelVoteById($id){
		$sql = "DELETE FROM {$GLOBALS['tablepre']}vote WHERE voteid = '$id'";
		$sql_option = "DELETE FROM {$GLOBALS['tablepre']}vote_option WHERE voteid = '$id'";
		$this->db->query($sql_option);
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}
			return false;
	}
	//删除一个投票的选项信息
	function DelOptionsById($id){
		$sql_option = "DELETE FROM {$GLOBALS['tablepre']}vote_option WHERE voteid = '$id'";
		$this->db->query($sql_option);
		return true;
	}
	//给某一个投票项投票
	function voteOneOption($optionid){
		$sql = "UPDATE {$GLOBALS['tablepre']}vote_option  SET votes = votes + 1 WHERE optionid  = '$optionid'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	
}