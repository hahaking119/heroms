<?php
/**
 * category.php
 * 栏目分类处理类
 */
class category
{
	var $db;
	var $cache_file_name;
	var $html_dir = 'html';
	
 	function __construct(){
 		$this->category();
 		$this->cache_file_name = ROOT_PATH.'data/cat_cache.php';
 		$this->html_dir = isset($_GLOBAL['html_dir']) ? $_GLOBAL['html_dir'] : $this->html_dir;
 	}
 	
	function category(){
		$this->db = $GLOBALS['db'];
	}
	
	//检查是否是叶子
	function checkHasChildren($id){
		$sql = "SELECT COUNT(catid) as c FROM {$GLOBALS['tablepre']}category WHERE parentid = '$id'";
		$rs = $this->db->getOne($sql);
		if($rs > 0){
			return true;
		}
		return false;
	}
	//检查是否有文章
	function checkHasArticles($id){
		$sql = "SELECT COUNT(articleid) as c FROM {$GLOBALS['tablepre']}article WHERE catid = '$id'";
		$rs = $this->db->getOne($sql);
		if($rs > 0){
			return true;
		}
		return false;
	}
	function delCategory($id){
		$sql = "DELETE FROM {$GLOBALS['tablepre']}category WHERE catid = '$id'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}
		return false;
	}
	//获得某个栏目的详细信息
	function getCatInfoById($id){
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}category WHERE catid = '$id'";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
	}
	//清空该栏目下的所有文章
	function clearArticleByCat($id){
		$sql = "DELETE FROM {$GLOBALS['tablepre']}article WHERE catid = '$id'";
		$this->db->query($sql);
		if($this->db->affected_rows() > 0){
			return true;
		}
		return false;
	}
	//获得某个频道下的栏目数组
	function getarr_by_channel($channelid = 1){
		$cat_arr = array();
		$sql = "select c.* , COUNT(a.catid) as haschilds ".
				"from {$GLOBALS['tablepre']}category as c ".
				"LEFT JOIN {$GLOBALS['tablepre']}category as a ON a.parentid = c.catid ".
				"WHERE c.channelid = {$channelid} ".
				"GROUP BY c.catid ".
				"ORDER BY parentid, sort DESC";
		if($data = $this->db->getAll($sql)){
			foreach ($data as $k=>$value){
				$cat_arr[$value['catid']] = $value;
			}
		}
		return $cat_arr;
	}
	//获得栏目下所有子栏目
	function getChildren($node,$catid){
		$children = array();
		static $i = 0;
		if(key_exists($catid,$node)){
			$catinfo = $node[$catid];
			foreach($node as $k=>$v){
				if($v['parentid'] == $catid){
					array_push($children,$v);
				}
			}	
			return $children;
		}else{
			return false;
		}
	}
	//获得某个栏目的路径信息
	function getCatPath($node,$catid,$channelid = 1){
		if(empty($node)){
			$node = $this->getarr_by_channel($channelid);
		}
		$path = array();
		static $i = 0;
		if(key_exists($catid,$node)){
			$catinfo = $node[$catid];
			$path[$i]['catid'] = $catinfo['catid'];
			$path[$i]['catname'] = $catinfo['catname'];
			$path[$i]['catdir'] = $catinfo['catdir'];
			if($parent = $this->getCatPath($node,$catinfo['parentid'])){
				$i ++;
				$path[$i]['catid'] = $parent[0]['catid'];
				$path[$i]['catname'] = $parent[0]['catname'];		
				$path[$i]['catdir'] = $parent[0]['catdir'];		
			}
			return array_reverse($path);
		}else{
			return false;
		}
		
	}
	//将路径信息封装成连接
	public function makeAddress($node,$catid){
		$path = $this->getCatPath($node,$catid);
		$address = '';
		$spilid = "-&gt;";
		$length = count($path) - 1;
		foreach ($path as $k=>$v){
			$address .= $k == $length ? ' <a href="article.php?q=list&id='.$v['catid'].'">'.$v['catname'].'</a>' : '<a href="article.php?q=group&id='.$v['catid'].'">'.$v['catname'].'</a>'.$spilid;
		}
		return $address;
	}
	function getCategoryPathById($catid){
		if($cat_info = $this->getCatInfoById($catid)){
			return $cat_info['catpath'];
		}else{
			return false;
		}
		
	}
	//获得某个栏目的路径信息
	function getCategoryDirPath($node,$catid){
		if($catid == 0) return false;
		$path = $this->getCatPath($node,$catid);
		$rerurn = implode('/',$path);
		return $rerurn.'/';
	}
	//生成某个栏目的完整路径信息
	function getFullDirPath($node,$catid,$use_hostname = false){
		$catpath = $this->getCategoryDirPath($node,$catid);
		$solute_path =  $this->html_dir.'/'.$catpath;
		if(!defined('HOSTNAME')) $use_hostname = false;
		return $use_hostname ? HOSTNAME.'/'.$solute_path : $solute_path ;
	}
	
	/**
	 *
	 * @param unknown_type $id 栏目编号为id的下级栏目
	 * @param unknown_type $node
	 * @param unknown_type $depth
	 * @return unknown
	 */
//找出分类父id为$id的子栏目数组，组织为标有深度的数组
	function get_tree_child($channelid = 1,$id = 0,$node = null,$depth = 0){
		if(empty($node)){
		$node = $this->getarr_by_channel($channelid);
		}
		$depth++;
		$tree_list = array();
		if(is_array($node)){
			foreach ($node as $leap){
				if ($leap['parentid'] == $id){
					$leap['depth'] = $depth - 1;
					$tree_list[] = $leap;
					if($children = $this->get_tree_child($channelid,$leap['catid'],$node,$depth)){
						$tree_list = array_merge($tree_list,$children);
					}
				}
			}
		}
		return $tree_list;
		
	}
	/**
	 * Enter 生成带有下拉列表的树形
	 *
	 * @param unknown_type $selectname
	 * @param unknown_type $channelid
	 * @param unknown_type $id 选中的id
	 * @param unknown_type $node
	 * @param unknown_type $head_name
	 * @return unknown
	 */
	function get_tree_select($selectname,$channelid = 1,$id = 0,$node = null,$head_name = '父级栏目'){
		$tree_array = $this->get_tree_child($channelid,0,$node,0);
		$select = '';
		$select .= "<select name='".$selectname."' class='required'>\n";
		$select .= "<option value=''>---".$head_name."---</option>\n";
		$last = "┝-";
		foreach ($tree_array as $key=>$val) {
			$select .= "<option value='".$val['catid']."'";
			$item = str_repeat( " | ",$val['depth']);
			if($val['catid'] == $id){
				$select .= " selected ";
			}
			$select .= ">".$item.$last.$val['catname']."</option>\n";
			$item = '';
		}
		return $select;
		
	}
	//生成缓存
	function make_node_cache($chanarr = array(1)) {
		$node_array = array();
		if(empty($chanarr)){
			$chanarr = array(1);
		}
		foreach($chanarr as $k){
		$node_array[$k] = $this->getarr_by_channel($k);
		}
		$file_string = "<?php \n \$data = array(\n";
		foreach($node_array as $key=>$value){
			$file_string .= $key." => array(\n";
			foreach($value as $k=>$v){
			$file_string .= $v['catid']." => array(  'catid' => '".$v['catid']."',\n";
			$file_string .= "       		'parentid' => '".$v['parentid']."',\n";
			$file_string .= "       		'catname' => '".$v['catname']."',\n";
			$file_string .= "      		'keywords' => '".$v['keywords']."',\n";
			$file_string .= "      		'description' => '".$v['description']."',\n";
			$file_string .= "      		'sort' => '".$v['sort']."',\n";
			$file_string .= "       		'haschilds' => '".$v['haschilds']."',\n";
			$file_string .= "       		'catdir' => '".$v['catdir']."',\n";
			$file_string .= "       		'catpath' => '".$v['catpath']."',\n";
			$file_string .= "),\n";
			}
			$file_string .= "),\n";
		}
		$file_string .= ");\n\n\n?>";
		if ($handle = fopen($this->cache_file_name,'w')){
			fputs($handle,$file_string,strlen($file_string));
			fclose($handle);
			chmod($this->cache_file_name,0777);
		}else{
			echo "无法写文件权限不够".$this->cache_file_name;
			exit;
		}
	}
	//生成静态目录
	function make_cat_dir($dirname){
		if(empty($dirname)){
			return;
		}else{
			$abs_cat_dir = ROOT_PATH.$this->html_dir.'/'.$dirname;
			if(file_exists($abs_cat_dir)) return ;
			else 
			return make_dir($abs_cat_dir);
		}
	}

}
?>