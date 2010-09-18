<?php
/**
 * article.php
 * 处理管理员
 */

loadModel('juhui');
$juhui = new juhui();
require_once ROOT_PATH.'includes/cls_image.php';
$img = new cls_image();
$juhuiid = $catid = 45;
switch ($a) {
	case 'ls' :
		$pagesize = 20;
		$juhuilist = $juhui->getAllJuhuiList($page,$pagesize,1);
		if($juhuilist){
			$smarty->assign('juhuilist',$juhuilist);
		}else{
			$smarty->assign('no_juhui',true);
		}
		$checkjuhuilist = $juhui->getAllJuhuiList($page,$pagesize,0);
		if($checkjuhuilist){
			$smarty->assign('checkjuhuilist',$checkjuhuilist);
		}else{
			$smarty->assign('no_checkjuhui',true);
		}
		$total = $juhui->totalRecord;
		$mutipage = mutipage('admin.php?m=juhui&a=ls&page=',$total,$page,$pagesize);
		$smarty->assign('mutipage',$mutipage);
		$smarty->display('juhui_ls.htm');
		break;
	case 'addindex' :
		//$catid = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$time = date('y-m-d H:i:s');
		$smarty->assign('time',$time);
		$smarty->display('juhui_add.htm');
		break;
	case 'add' :
		$check  =true;// $article->checkdata($_POST);
		if($check === true){
		foreach ($_POST as $k=>$v){
			$$k = $v;
		}
		
		$top_flag = isset($top_flag) ? 1 : 0;
		
		$title = trim($title);
		$islink = empty($url) ? 0 : 1;
		$source = trim($source);
		$created = isset($created) ? strtotime($created) : $systime;
		//处理上传的图片
		$content = $content;
			$sql = "INSERT INTO {$tablepre}juhui (`title` ,`description` ,`time`,`created`  ,`switch` ,`top_flag` )
					VALUES ('$title','$content','$created','$systime','$switch','$top_flag') ";
			$db->query($sql);
			if($nid = $db->insert_id()){
				showmsg('聚会添加成功，继续发布聚会吧',PRE_URL,'success');
			}
		}else{
			showmsg($check,PRE_URL);
		}
		break;
	case 'editindex' :
		$articleid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($articleid)){
			showmsg('没有要选择编辑的聚会，操作禁止',PRE_URL);
		}
		
		$rs = $juhui->getJuhuiDetail($articleid,null);
		$rs['created'] = date('y-m-d H:i:s',$rs['created']);
		foreach ($rs as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->display('juhui_edit.htm');
		break;
	case 'edit' :
		$check  =true;// $article->checkdata($_POST);
		if($check === true){
			foreach ($_POST as $k=>$v){
				$$k = $v;
			}
			$juhuiid = intval($juhuiid);
			$top_flag = isset($top_flag) ? 1 : 0;
			$title = trim($title);
			$created  = isset($created) ? strtotime($created) : $systime;
			$sql = "UPDATE {$tablepre}juhui SET `title` = '$title',
					`description` = '$content',`time` = '$created',`switch` = '$switch',`top_flag` = '$top_flag' WHERE juhuiid = '$juhuiid' LIMIT 1";
			$db->query($sql);
			if($db->affected_rows() > 0){
				showmsg('聚会更新成功','?m=juhui&a=ls','success');
			}else{
				showmsg('未做任何修改','?m=juhui&a=ls','success');
			}
		}else{
			showmsg($check,PRE_URL);
		}
		break;
		
	case 'del' :
		$articleid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($articleid)){
			showmsg('没有要选择删除的聚会，操作禁止',PRE_URL);
		}
		if($juhui->delJhui($articleid)){
			showmsg('删除成功',PRE_URL,'success');
		}else{
			showmsg('删除失败，请重新操作',PRE_URL);
		}
		break;
	case 'check' :
		$juhuiid = isset($_GET['id']) ? intval($_GET['id']) : '';
		$db->query("update {$tablepre}juhui set switch=1 where juhuiid = '$juhuiid'");
		showmsg('聚会审核通过',PRE_URL,'success');
		break;
	case 'search' :
		//$catid = isset($_POST['catid']) ? intval($_POST['catid']) : $_GET['catid'];
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$articles = array();
		$s_title = isset($_SESSION['title']) ? $_SESSION['title'] : '';
		$pagesize = 20;
		if(empty($title) || $title == '标题模糊搜索'){
			$articles = $article->getArticles(null,$catid,'DESC',$page,$pagesize);
			$nullmsg = $articles == false ? '该分类还没有记录' : ''; 
			$total = $article->totalcount;
			$mutipage = mutipage('admin.php?m=juhui&a=search&catid='.$catid.'&page=',$total,$page,$pagesize);
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
			$nullmsg = $articles == false ? '没有相匹配的聚会' : ''; 
			$cat_sel = $cat->get_tree_select('catid',1,0,null);
			$smarty->assign('cat_select',$cat_sel);
			$smarty->assign('nullmsg',$nullmsg);
			$smarty->assign('title',$s_title);
			$smarty->assign('arts',$articles);
			$smarty->display('juhui_ls.htm');
		}
		break;
}
?>