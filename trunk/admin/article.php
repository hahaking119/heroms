<?php
/**
 * article.php
 * 处理管理员
 */
loadModel('article');
$article = new article();
loadModel('category');
$cat = new category();
require_once ROOT_PATH.'includes/cls_image.php';
$img = new cls_image();
switch ($a) {
	case 'ls' :
		$pagesize = 20;
		$arts_data = $article->getArticles(null,null,'DESC',$page,$pagesize);
		$nullmsg = $arts_data == false ? '暂时还没有记录' : ''; 
		$total = $article->totalcount;
		$mutipage = mutipage('admin.php?m=article&a=ls&page=',$total,$page,$pagesize);
		$cat_sel = $cat->get_tree_select('catid',1,0,null);
		$smarty->assign('cat_select',$cat_sel);
		$smarty->assign('nullmsg',$nullmsg);
		$smarty->assign('arts',$arts_data);
		$smarty->assign('mutipage',$mutipage);
		$smarty->display('article_ls.htm');
		break;
	case 'addindex' :
		$catid = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$cat_sel = $cat->get_tree_select('catid',1,$catid,null);
		$time = date('y-m-d H:i:s');
		$smarty->assign('time',$time);
		$smarty->assign('cat_select',$cat_sel);
		$smarty->display('article_add.htm');
		break;
	case 'add' :
		$check  = $article->checkdata($_POST);
		if($check === true){
		foreach ($_POST as $k=>$v){
			$$k = $v;
		}
		$is_commend = isset($is_commend) ? 1 : 0;
		$top_flag = isset($top_flag) ? 1 : 0;
		$url = trim($url);
		$title = deal_with_title($title);
		$content = deal_with_content($content);
		$attachment = '';
		$islink = empty($url) ? 0 : 1;
		$source = trim($source);
		$username = $_SESSION['username'];
		$created = isset($created) ? strtotime($created) : $systime;
		//处理上传的图片
		$content = $content.$exts;
			$sql = "INSERT INTO {$tablepre}article (
					`channelid` ,`catid` ,`typeid` ,`title` ,`tcolor` ,`thumb` ,`author` ,`source` ,`keywords` ,`summary` ,`content` ,
					`url` ,`username` ,`created` ,`updated` ,`status` ,`islink` ,`is_commend` ,`top_flag` )
					VALUES ('$channelid','$catid','$typeid','$title','$tcolor','$picaddress','$author','$source','$keywords','$summary',
					'$content','$url','$username','$created','$systime','$status','$islink','$is_commend','$top_flag') ";
			$db->query($sql);
			if($nid = $db->insert_id()){
				$static_name = $cat->getCategoryPathById($catid);
				$article->makeStaticContent($nid,$static_name,$created);
				showmsg('文章添加成功，继续发布文章吧',PRE_URL,'success');
			}
		}else{
			showmsg($check,PRE_URL);
		}
		break;
	case 'editindex' :
		$articleid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($articleid)){
			showmsg('没有要选择编辑的文章，操作禁止',PRE_URL);
		}
		if(!$article->checkExist($articleid)){
			showmsg('该文章不存在，操作禁止',PRE_URL);
		}
		$rs = $article->getDetailById($articleid);
		$rs['created'] = date('y-m-d H:i:s',$rs['created']);
		foreach ($rs as $k=>$v){
			$smarty->assign($k,$v);
		}
		$cat_sel = $cat->get_tree_select('catid',1,$rs['catid'],null);
		$smarty->assign('cat_select',$cat_sel);
		$smarty->display('article_edit.htm');
		break;
	case 'edit' :
		$check  = $article->checkdata($_POST);
		if($check === true){
		foreach ($_POST as $k=>$v){
			$$k = $v;
		}
		$articleid = intval($articleid);
		$is_commend = isset($is_commend) ? 1 : 0;
		$top_flag = isset($top_flag) ? 1 : 0;
		$url = trim($url);
		$title = trim($title);
		$islink = empty($url) ? 0 : 1;
		$source = trim($source);
		$content = $content.$exts;
		$created  = isset($created) ? strtotime($created) : $systime;
		$sql = "UPDATE {$tablepre}article SET `channelid` = '$channelid',`catid` = '$catid',`typeid` = '$typeid',`title` = '$title',
				`tcolor` = '$tcolor',`thumb` = '$picaddress',`author` = '$author',`source` = '$source',`keywords` = '$keywords',`summary` = '$summary',
				`content` = '$content',`url` = '$url',`created` = '$created',`updated` = '$systime',`status` = '$status',`islink` = '$islink',`is_commend` = '$is_commend',
				`top_flag` = '$top_flag' WHERE articleid = '$articleid' LIMIT 1";
		$db->query($sql);
		$category_path = $cat->getCategoryPathById($catid);
		$article->makeStaticContent($articleid,$category_path,$created);
		if($db->affected_rows() > 0){
			showmsg('文章更新成功','?m=article&a=ls','success');
		}
		}else{
			showmsg($check,PRE_URL);
		}
		break;
		
	case 'del' :
		$articleid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($articleid)){
			showmsg('没有要选择删除的文章，操作禁止',PRE_URL);
		}
		if($article->deletById($articleid)){
			showmsg('删除成功',PRE_URL,'success');
		}else{
			showmsg('删除失败，请重新操作',PRE_URL);
		}
		break;
	case 'search' :
		$catid = isset($_POST['catid']) ? intval($_POST['catid']) : $_GET['catid'];
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$articles = array();
		$s_title = isset($_SESSION['title']) ? $_SESSION['title'] : '';
		$pagesize = 20;
		if(empty($title) || $title == '标题模糊搜索'){
			$articles = $article->getArticles(null,$catid,'DESC',$page,$pagesize);
			$nullmsg = $articles == false ? '该分类还没有记录' : ''; 
			$total = $article->totalcount;
			$mutipage = mutipage('admin.php?m=article&a=search&catid='.$catid.'&page=',$total,$page,$pagesize);
			$cat_sel = $cat->get_tree_select('catid',1,0,null);
			$smarty->assign('cat_select',$cat_sel);
			$smarty->assign('nullmsg',$nullmsg);
			$smarty->assign('arts',$articles);
			$smarty->assign('title',$s_title);
			$smarty->assign('mutipage',$mutipage);
			$smarty->display('article_ls.htm');
		}else {
			$_SESSION['title'] = $title;
			$articles = $article->searchArticle($catid,$title);
			$nullmsg = $articles == false ? '没有相匹配的文章' : ''; 
			$cat_sel = $cat->get_tree_select('catid',1,0,null);
			$smarty->assign('cat_select',$cat_sel);
			$smarty->assign('nullmsg',$nullmsg);
			$smarty->assign('title',$s_title);
			$smarty->assign('arts',$articles);
			$smarty->display('article_ls.htm');
		}
		break;
}
?>