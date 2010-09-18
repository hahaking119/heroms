<?php
class abulm 
{
	var $db;
	var $tablepre;
	var $count;
	var $table;
	var $abulm_class;
	var $totalRecord=0;
	function __construct(){
		$this->abulm();
	}
	function abulm(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
		$this->table = $this->tablepre.'photos';
		$this->abulm_class = $this->tablepre.'photos_class';
	}
	//获取某个成员全部相册信息
	function getMemberAbulm($userid,$page,$pagesize){
		return $this->getAbulm($userid,null,$page,$pagesize);
	}
	//获取某个人成员某分类相册
	function getMemberAbulmByClass($classid,$page,$pagesize){
		return $this->getAbulm(null,$classid,$page,$pagesize);
	}
	//获取所有相册
	function getAllAbulm($page,$pagesize){
		return $this->getAbulm(null,null,$page,$pagesize);
	}
	//根据条件获取所有的相册
	function getAbulm($userid = null,$classid=null,$page=1,$pagesize=15){
		if($userid == null){
			if($classid == null){
				$sql = "select * from {$this->table}";
				$sql_c = "select count(*) from {$this->table}";
			}else{
				$sql = "select * from {$this->table} where classid='$classid'";
				$sql_c = "select count(*) from {$this->table} where classid='$classid'";
			}
		}else{
			if($classid == null){
				$sql = "select * from {$this->table} where userid = '$userid'";
				$sql_c = "select count(*) from {$this->table} where userid = '$userid'";
			}else{
				$sql = "select * from {$this->table} where userid = '$userid' and classid='$classid'";
				$sql_c = "select count(*) from{$this->table} where userid = '$userid' and classid='$classid'";
			}
		}
		$start = ($page -1)*$pagesize; 
		$sql .= " order by sindex desc,pubtime desc limit $start,$pagesize";
		$this->totalRecord = $this->db->getOne($sql_c);
		if($this->totalRecord > 0){
			$result = $this->db->getAll($sql);
			foreach($result as $k=>$rows){
				if(!file_exists(ROOT_PATH.$result[$k]['pics'])){
					$result[$k]['pics'] = 'upload/nopic.gif';
				}
			}
			return $result;
		}else{
			return false;
		}	
	}
	//检查是否有分类
	function hasClass($uid){
		$sql = "select count(*) from {$this->abulm_class} where userid = $uid";
		$counter = $this->db->getOne($sql);
		if($counter > 0){
			return true;
		}
		return false;
	}
	//添加相册分类
	function addClass($uid,$classname,$orderby=1){
		$sql = "insert into {$this->abulm_class} (userid,classname,orderby) values ('$uid','$classname','$orderby')";
		$this->db->query($sql);
		return $this->db->insert_id();
	}
	//修改相册分类
	function updateClass($uid,$classid,$classname,$orderby=1){
		$sql = "update {$this->abulm_class} set classname = '$classname' ,orderby = '$orderby' where userid = $uid and classid='$classid'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//删除相册分类
	function delClass($uid,$classid){
		$sql = "delete from {$this->abulm_class} where userid = $uid and classid='$classid'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//删除某个相片
	function delAbulm($uid = null,$photoid){
		if($detail = $this->getAbulmDetail($photoid)){
			@unlink(ROOT_PATH.$detail['pics']);
		}else{
			return false;
		}
		if($uid){
			$sql = "delete from {$this->table} where userid = $uid and photoid='$photoid'";
		}else{
			$sql = "delete from {$this->table} where photoid='$photoid'";
		}
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//推荐到首页
	function commendToIndex($id,$flag = 1){
		if($flag == 1){
			$sql = "update {$this->table} set sindex = 1 where photoid = '$id'";
		}else{
			$sql = "update {$this->table} set sindex = 0 where photoid = '$id'";
		}
		$this->db->query($sql);
		return true;
	}
	//获取下一张照片
	function getNextPhoto($uid,$classid,$photoid,$flag=1){
		if($flag == 2){
			$sql = "select photoid from {$this->table} where userid = '$uid' and classid='$classid' and photoid > $photoid order by photoid asc";
		}else{		
			$sql = "select photoid from {$this->table} where userid = '$uid' and classid='$classid' and photoid < $photoid order by photoid desc";
		}
		if($id = $this->db->getOne($sql)){
			return $id;
		}
		return false;
		
	}
	//获取某个相片的详细信息
	function getAbulmDetail($photoid){
		$sql = "select p.* ,m.realname,m.username from {$this->table} p left join {$this->tablepre}member m on p.userid = m.userid where p.photoid = $photoid ";
		if($row = $this->db->getRow($sql)){
			if(!file_exists(ROOT_PATH.$row['pics'])){
					$row['pics'] = 'upload/nopic.gif';
				}
				if(empty($row['realname'])){
					$row['realname'] = $row['username'];
				}
				$class_arr = array(
					1=>'个人风采',
					2=>'校友聚会',
					3=>'校友会大事',
					4=>'母校风光',
					5=>'同城岁月',
				);
			$row['classname'] = isset($class_arr[$row['classid']]) ? $class_arr[$row['classid']] : $class_arr[1]; 
			return $row;
		}else{
			return false;
		}
		
	}
	//获取某个分类的详细信息
	function getClassDetail($classid){
		$sql = "select c.*,m.realname from {$this->abulm_class} c left join {$this->tablepre}member m on c.userid = m.userid  where c.classid = '$classid'";
		if($row = $this->db->getRow($sql)){
			return $row;
		}else{
			return false;
		}
		
	}
	//获取相册分类列表
	function getClassList($uid){
		$sql = "select * from {$this->abulm_class} where userid = '$uid'";
		if($row = $this->db->getAll($sql)){
			return $row;
		}else{
			return array();
		}
	}
	function getClassById($id,$page=1,$pagesize=15){
		return $this->getAbulm(null,$id,$page,$pagesize);
	}
	//添加相片
	function savepic($uid,$set){
		
	}
	function getUserRealname($uid){
		$rs = $this->db->getRow("select username,realname from {$this->tablepre}member where userid = $uid");
		if(empty($rs['realname'])){
			return $rs['username'];
		}else{
			return $rs['realname'];
		}
	}
	
}