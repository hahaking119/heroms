<?php
/**
 * category.php
 * 处理管理员
 */
loadModel('category');
$cat = new category;
switch ($a) {
	case 'ls' :
		$cats_data = $cat->get_tree_child();
		loadModel('article');
		$art = new article();
		foreach($cats_data as $k=>$v){
			$cats_data[$k]['hasarts'] = $art->getArticleCount($v['catid']);
			$cats_data[$k]['depth_line'] = repeatString('―',$v['depth']);//str_pad('',$v['depth'],'_',STR_PAD_BOTH); 
		}
		$parent_sl = $cat->get_tree_select('parentid',1,0,null);
		$smarty->assign('parentls',$parent_sl);
		$smarty->assign('cats',$cats_data);
		$smarty->display('category_ls.htm');
		break;	
	case 'add' :
		$parentid = intval($_POST['parentid']);
		$channelid = 1;
		$catname = trim($_POST['catname']);
		$keywords = trim($_POST['keywords']);
		$description = $_POST['description'];
		$catdir = trim($_POST['catdir']);
		if( empty($catname) || empty($catdir) ){
			showmsg('栏目名称或者栏目目录不能为空!',PRE_URL,'error');
		}
		if(strpos('/',$catdir)) showmsg('栏目名称不合法，请去掉“/”!',PRE_URL,'error');
		$parent_path = '';
		if($parentid == 0){
			 $cat->make_cat_dir($catdir);
		}else{
			$parent_path = $cat->getCategoryPathById($parentid);
			$cat->make_cat_dir($parent_path.$catdir);
		}
		$catpath = $parent_path.$catdir.'/';
		$sort = intval($_POST['sort']);
		$sql = "INSERT INTO {$tablepre}category (parentid,channelid,catname,keywords,description,sort,created,updated,catdir,catpath) ".
			   "VALUES ('$parentid','$channelid','$catname','$keywords','$description','$sort','$systime','$systime','$catdir','$catpath')";
		$db->query($sql);
		if($db->insert_id()){
			$cat->make_node_cache();
			showmsg('添加栏目成功!',PRE_URL,'success');
		}
		break;
	case 'editindex' :
		$catid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($catid)){
			showmsg('请选择要编辑的栏目，操作禁止',PRE_URL);
		}
		$catdetail = $cat->getCatInfoById($catid);
		$parent_sl = $cat->get_tree_select('parentid',1,$catdetail['parentid'],null);
		foreach($catdetail as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->assign('parentls',$parent_sl);
		$smarty->display('category_edit.htm');
		break;
	case 'edit' :
		$catid = intval($_POST['catid']);
		$parentid = intval($_POST['parentid']);
		$catname = trim($_POST['catname']);
		$keywords = trim($_POST['keywords']);
		$description = $_POST['description'];
		$sort = intval($_POST['sort']);
		$sql = "UPDATE {$tablepre}category SET parentid = '$parentid' ,catname = '$catname',".
				"keywords = '$keywords',description = '$description',sort = '$sort',updated = '$systime' WHERE catid = '$catid'";
		$db->query($sql);
		$cat->make_node_cache();
		showmsg($catname.'栏目更新成功!','?m=category&a=ls','success');
		break;
	case 'del' :
		$catid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($catid)){
			showmsg('没有选择栏目，操作禁止',PRE_URL);
		}
		//检查是否是叶子节点
		if($cat->checkHasChildren($catid)){
			showmsg('该栏目下还有子栏目，先删除子栏目',PRE_URL);
		}
		//检查是否有文章
		if($cat->checkHasArticles($catid)){
			showmsg('该栏目下有文章，请先清空该栏目下文章',PRE_URL);
		}
		if($cat->delCategory($catid)){
			showmsg('栏目删除成功',PRE_URL,'success');
		}else{
			showmsg('栏目删除失败，请确定该栏目存在',PRE_URL);
		}
		break;
	case 'clear' :
		$catid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($catid)){
			showmsg('没有选择栏目，操作禁止',PRE_URL);
		}
		if($cat->clearArticleByCat($catid)){
			showmsg('已经清空该栏目下所有文章',PRE_URL,'success');
		}else{
			showmsg('对不起，清空失败',PRE_URL,'error');
		}
		
}
?>