<?php
/**
 * 投票模型
 */
class product
{
	var $db;
	var $count = 0;
	function __construct(){
		$this->product();
	}
	
	function product(){
		$this->db = $GLOBALS['db'];
	}
	
	function getProductList($catid = null,$page = 1,$pagesize = 10){
		$startrecored = ($page - 1)*$pagesize;
		$sql = "SELECT p.pid,p.title,p.img,p.created,p.updated,p.catid,p.top_flag,p.clicks,p.size,p.config,pc.catname FROM {$GLOBALS['tablepre']}product p left join  {$GLOBALS['tablepre']}calling pc on p.catid = pc.catid where 1=1";
		$sql_c = "select count(pid) as c from {$GLOBALS['tablepre']}product where 1=1";
		if($catid){
			$sql .= " and p.catid = '$catid'";
			$sql_c .= " andcatid = '$catid'";
		}
		
		$sql .= " ORDER BY p.top_flag desc,p.updated DESC limit $startrecored,$pagesize";
		$data = $this->db->getAll($sql);
		$this->count = $this->db->getOne($sql_c);
		if($data){
			return $data;
		}
		return array();
	}
	
	function getProductDetail($voteid){
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}product  WHERE pid = '$pid' ";
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