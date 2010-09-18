<?php
//评论模型
class comment {
	var $db;
	var $table;
	function __construct(){
		$this->comment();
	}
	function comment(){
		$this->db = $GLOBALS['db'];
		$this->table = $GLOBALS['tablepre'].'comment';
	}
	//插入一条评论
	function insertComment($articleid,$catid,$content,$uid = '',$username = '',$email = ''){	
		if(check_filter($content)){
			$flag = 1;
		}
		$data = array(
			'channelid'	=>	1,
			'catid'		=>	$catid,
			'object_id'	=>	$articleid,
			'uid'		=>	$uid,
			'username'	=>	$username,
			'email'		=>	$email,
			'content'	=>	$content,
			'created'	=>	time(),
			'del_flag'	=>	$flag,			
		);
		if($insert_id = $this->db->insert($this->table,$data)){
			return $insert_id;
		}
		return false;
	}
	//获取某个文章的所有评论
	function getArticleComment($articleid,$page = 1,$pagesize = 100,$flag = true){
		$startid = ($page - 1)*$pagesize;
		$result = array();
		$sql = "select uid,username,content,created where object_id = {$articleid} and del = 0 order by comment_id desc ";
		if($flag){
			$sql .= "limit $startid,$pagesize";
		}
		if($result = $this->db->findAll()){
			return $result;
		}
	}
	//删除某个文章下的所有评论
	function delArticleComment($articleid,$action='del'){
		if($action == 'keep'){
			$this->db->query("update {$this->table} set del_flag = 1 where object_id = {$articleid}");
		}else{
			$this->db->query("delete from {$this->table} where object_id = {$articleid}");
		}
	}
	//获取所有评论
	function getAllComments($channel = null,$catid = null,$page = 1,$pagsize = 10){
		$category_table = $GLOBALS['tablepre'].'category';
		$startid = ($page - 1)*$pagesize;
		$result = array();
		$sql = "select c.catname,c,catid,cm.content,cm.created from {$this->table} cm left join {$category_table} on c.catid = cm.catid where 1=1 ";	
		if($catid){
			$sql .= "cm.catid = {$catid} ";
		}
		$sql .= "order by commentid desc limit $startid,$pagesize";
		$result = $this->db->findAll();
		return $result;
	}
}
