<?php
/**
 * article.php
 */
class article
{
	var $db;
	var $totalcount;
	var $table;

	function __construct(){
		$this->article();
	}
	
	function article(){
		$this->db = $GLOBALS['db'];
		$this->table = $GLOBALS['tablepre'].'article';
	}
	//检查文章是否存在
	function checkExist($id){
		$sql = "SELECT COUNT(articleid) as c FROM {$GLOBALS['tablepre']}article WHERE articleid = '$id'";
		$rs = $this->db->getOne($sql);
		if($rs['c'] > 0){
			return true;
		}
		return false;
	}
	//获得文章的详细
	function getDetailById($id){
		$sql = "SELECT a.*,c.catname,c.catdir,c.catpath FROM {$GLOBALS['tablepre']}article a left join {$GLOBALS['tablepre']}category c on c.catid = a.catid WHERE articleid = '$id'";
		$rs = $this->db->getRow($sql);
		return $rs;
	}
	//删除文章通过编号
	function deletById($id){
		if(!$this->checkExist($id)){
			return false;
		}
		$sql = "DELETE FROM {$GLOBALS['tablepre']}article WHERE articleid = '$id'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//搜索文章
	function searchArticle($catid = null,$title = null,$status = 2){
		$where = "";
		if(empty($catid)){
			$where = " WHERE a.title like '%".$title."%' ORDER BY a.created DESC";
		}else{
			$where = " WHERE a.catid = '$catid' AND a.title like '%".$title."%' ORDER BY a.created DESC";
		}
		$count_sql = "SELECT COUNT(articleid) as c FROM {$GLOBALS['tablepre']}article ";
		$sql = "SELECT a.articleid,a.channelid,a.catid,a.typeid,a.title,a.tcolor,a.thumb,a.author,a.url,a.username,a.created,a.updated,a.status,a.clicks,a.islink,a.is_commend,a.top_flag,c.catname,c.catdir,c.catpath".
			   " FROM {$GLOBALS['tablepre']}article as a LEFT JOIN  {$GLOBALS['tablepre']}category as c ON a.catid = c.catid";
		$sql .= $where;	   
		if($result = $this->db->getAll($sql)){
			foreach ($result as $k=>$v){
				$static_url = $this->getArticleUrl($v['articleid'],$v['catpath'],$v['created'],1);
				$result[$k]['article_url'] = $static_url;
				$result[$k]['link_url']  = defined('USE_STATIC') ? $static_url : 'article.php?q=d&id='.$v['articleid'];
			}
			return $result;
		}else{
			return false;
		}
			   
	}
	//获取某顶级分类下子分类下的文章
	function getArticleByGroup($cids,$nums = 5){
		$result = array();
		$cids = is_array($cids) ? $cids : array($cids);
		$cat_str = implode(',',$cids);
		$sql = "select a.articleid,a.catid,a.typeid,a.title,a.tcolor,a.thumb,a.url,a.updated,a.clicks,a.islink,a.is_commend,a.top_flag,c.catname,c.catdir,c.catpath".
			   " FROM {$GLOBALS['tablepre']}article as a LEFT JOIN  {$GLOBALS['tablepre']}category as c ON a.catid = c.catid".
			   " where a.status = 1 and a.catid in ($cat_str) order by a.top_flag desc,a.is_commend desc,a.pubtime desc limit $nums";
		$result = $this->db->getAll($sql);
		return $result;	
	}
	//获得文章列表
	function getArticles($channel = null,$catid = null,$order = 'DESC',$page = 1,$pagsize = 10,$hot = false,$status = 2,$extend_field = '',$except=0){
		$start = ($page - 1)*$pagsize;
		$where = '';
		$cwhere = '';
		$fileds = '';
		$except = empty($except) ? 0 : $except;
		if(!empty($channel) && !empty($catid)){
			$where .= "a.channelid = {$channel} AND a.catid = {$catid} ";
			$cwhere .= "channelid = {$channel} AND catid = {$catid} ";
		}elseif(!empty($channel)){
			$where .= " a.channelid = {$channel} "; 
			$cwhere .= " channelid = {$channel} "; 
		}elseif(!empty($catid)){
			$where .=" a.catid = {$catid} "; 
			$cwhere .=" catid = {$catid} "; 
		}
		if (!empty($where) && $status !== 2){
			$where .= " and a.status = '$status' ";
		}
		$count_sql = "SELECT COUNT(articleid) as c FROM {$GLOBALS['tablepre']}article ";
		
		if(!empty($extend_field)){
			$extend_field = is_array($extend_field) ? $extend_field : array($extend_field);
			$fileds = implode(",",$extend_field);
			$fileds .= ',';
		}
		$sql = "SELECT a.articleid,a.channelid,a.catid,a.typeid,a.title,a.tcolor,a.thumb,a.author,a.url,a.username,a.created,a.updated,a.status,{$fileds}a.clicks,a.islink,a.is_commend,a.top_flag,c.catname,c.catdir,c.catpath".
			   " FROM {$GLOBALS['tablepre']}article as a LEFT JOIN  {$GLOBALS['tablepre']}category as c ON a.catid = c.catid";
		if(!empty($where)){
			$sql .= " WHERE $where" ." and a.articleid != $except ";
			$count_sql .= " WHERE $cwhere" . " and articleid != $except ";
		}else{
			
			$sql .= " WHERE a.articleid != $except ";
			$count_sql .= " WHERE articleid != $except ";
		}
		if(!$hot){
		$sql .= " ORDER BY a.created {$order} limit $start,$pagsize";
		}else{
		$sql .= " ORDER BY a.clicks DESC ,a.created {$order} limit $start,$pagsize";
		}
		$this->totalcount =  $this->db->getOne($count_sql);
		if($result = $this->db->getAll($sql)){
			foreach ($result as $k=>$v){
				$result[$k]['article_url'] = $this->getArticleUrl($v['articleid'],$v['catpath'],$v['created'],1);
				$result[$k]['link_url']  = defined('USE_STATIC') && USE_STATIC ? $result[$k]['article_url'] : 'article.php?q=d&id='.$v['articleid'];
			}
			return $result;
		}else{
			return false;
		}
	}
	//取得图片标题
	function getArticleByPic($catid = null,$order = '',$is_commend = 0,$count = 6){
		$order = empty($order) ? 'created DESC' : $order;
		$sql = "SELECT articleid,catid,typeid,title,tcolor,thumb,url,content,created,clicks,islink,top_flag FROM {$GLOBALS['tablepre']}article ";
		$where = "WHERE thumb <> '' and status = 1 and  is_commend = '$is_commend' ";
		if(!empty($catid)){
			$where .= "and catid = '$catid' ";
		}
		$where .= "ORDER BY {$order} LIMIT $count";
		$sql .= $where; 
		if ($RS = $this->db->getAll($sql)){
			return $RS;
		}else {
			return false;
		}
		
	}
	//获得文章所有数量
	function getArticleCount($catid = null){
		if(empty($catid)){
			$sql = "SELECT COUNT(articleid) as c FROM {$GLOBALS['tablepre']}article";
		}else{
			$sql = "SELECT COUNT(articleid) as c FROM {$GLOBALS['tablepre']}article WHERE catid = '$catid'";
		}
		$rs = $this->db->getOne($sql);
		return $rs;
	}
	function checkdata($arr){
		if(empty($arr['catid']) || $arr['catid'] == 0 ){
			return '请选择所在的栏目!';
		}elseif(empty($arr['title'])){
			return '标题不能为空!';
		}else{
			return true;
		}
	}
	//生成静态文件的路径和文件信息 并检查路径是否存在
	function makeContentFilename($article_id,$static_dir,$created = ''){
		global $hostname,$html_dir;
		$time_stamp = isset($created) ? $created : time();
		$date_dir = date('Y',$time_stamp).date('m',$time_stamp).'/'.date('d',$time_stamp);
		$content_basename = str_pad($article_id,6,'0',STR_PAD_LEFT);
		$file_dir = ROOT_PATH.$html_dir.'/'.$static_dir.'/'.$date_dir;
		if(!file_exists($file_dir)){
			make_dir($file_dir);
		}
		return $static_dir.'/'.$date_dir.'/'.$content_basename.'.html';
	}
	//生成静态文件
	function makeStaticContent($article_id,$static_dir,$created = ''){
		global $hostname,$html_dir;
		$created = empty($created) ? time() : $created;
		$file_url = $hostname.'/article.php?q=d&id='.$article_id;
		$html_content = file_get_contents($file_url);
		$static_filename = $this->makeContentFilename($article_id,$static_dir,$created);
		$static_name = ROOT_PATH.$html_dir.'/'.$static_filename;
		fopen($static_name,'a');
		file_put_contents($static_name,$html_content);
		return true;
	}
	//获取相邻文章标题
	function getNearstArticle($article_id,$flag='pre'){
		$sql = "select a.articleid,a.title,a.created,c.catdir,c.catpath from {$this->table} a left join {$GLOBALS['tablepre']}category c on a.catid = c.catid ";
		if($flag == 'pre'){
			$sql .= " where articleid < $article_id order by articleid DESC limit 1";
		}else{
			$sql .= " where articleid > $article_id order by articleid ASC limit 1";
		}
		if($result = $this->db->getRow($sql)){
				$result['article_url'] = $this->getArticleUrl($result['articleid'],$result['catpath'],$result['created'],1);
			return $result;
		}else{
			return null;
		}
	}
		//前端获取静态地址 1 绝对的 2相对的
	function getArticleUrl($article_id,$catpath = '',$time_stamp,$flag=1){
		global $hostname,$html_dir;
			if(defined('USE_STATIC')){
			$date_dir = date('Y',$time_stamp).date('m',$time_stamp).'/'.date('d',$time_stamp);
			if($catpath){
				$artilce_url = $html_dir.'/'.$catpath.$date_dir;
			}else{
				$artilce_url = $html_dir.'/'.$date_dir;
			}
			$content_basename = str_pad($article_id,6,'0',STR_PAD_LEFT);
			$artilce_url = $artilce_url.'/'.$content_basename.'.html';
			if($flag == 1){
				return $hostname.'/'.$artilce_url;
			}else{
				return $artilce_url;
			}
		}else{
			return 'article.php?q=d&id='.$article_id;
		}		
	}
	
	function getLastJuhui($id){
		$sql = "SELECT articleid,title,content,created,clicks FROM {$GLOBALS['tablepre']}article where status = 1 and catid='$id' order by is_commend,updated desc";
		if($result = $this->db->getRow($sql)){
			return $result;
		}
		return array();
		
	}
	
}