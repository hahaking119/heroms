<?php
/**
 * photos
 * 图片管理
 */
loadModel('photos');
$photos = new photos();
require_once ROOT_PATH.'includes/cls_image.php';
$img = new cls_image();
switch ($a) {
	case 'ls' :
		$a_z = strtoupper('abcdefghijklmnopqrstuvwxyz');
		$a_z_arr = str_split($a_z);
		$index_str = '';
		foreach ($a_z_arr as $leter) {
			$index_str .= '<a href="?m=photos&a=ls&sindex='.$leter.'">'.$leter.'</a>';
		}
		$smarty->assign('index_str',$index_str);
		$pagesize = 20;
		$page = isset($input['page']) ? intval($input['page']) : 1;
		$class_id = isset($input['sindex']) ? $input['sindex'] : '';
		$photoslist = $photos->getphotosList($page,$pagesize,$class_id);
		foreach ($photoslist as $k=>$v){
			$pics = explode('|',$v['pics']);
			$photoslist[$k]['picaddress'] = empty($pics) ? 'images/noimage.gif' : $pics[0];
		}
		if($photoslist){
			$smarty->assign('photo_list',$photoslist);
		}else{
			$smarty->assign('nullmsg',"暂时还没有塔罗鉴赏");
		}
		$smarty->display('photos_ls.htm');
	break;
	case 'add' :
		$time = date('Y-m-d');
		$smarty->assign('time',$time);
		$smarty->display('photos_add.htm');
		break;
	case 'save' :
		foreach ($input as $k=>$v){
			$$k = $v;
		}
		if(empty($picaddress)){
			showmsg('您还没有上传案例图片，请重新上传',PRE_URL,'error');
		}
		$db->query("insert into {$tablepre}photos (title_en,sindex,title_cn,description,pics,author,draw,counts,publisher,pubtime,clicks) "
				  ."values ('$title_en','$sindex','$title_cn','$content','$picaddress','$author','$draw','$counts','$publisher','$pubtime',1)");
		if($db->insert_id()){
			showmsg($title_cn.'->塔罗鉴赏添加成功',PRE_URL,'success');
		}else{
			showmsg($title_cn.'->塔罗鉴赏添加失败',PRE_URL,'error');
		}
		
	break;
	case 'editindex' :
		$photosid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($photosid)){
			showmsg('请选择你要编辑的项目，操作禁止',PRE_URL);
		}
		if($photosdetail = $photos->getphotosDetail($photosid)){
			foreach($photosdetail as $k=>$v){	
				$$k = $v;
				$smarty->assign($k,$v);
			}
				$smarty->display('photos_edit.htm');
		}else{
				showmsg('你选择的处理项目不存在，操作禁止',PRE_URL);
		}
	
		break;
	case 'edit' :
		
		$id = $input['photoid'] ? $input['photoid'] : '';
		if(empty($id)){
			showmsg('对不起，你没有选择要处理的项目',PRE_URL,'error');
		}
		foreach ($input as $k=>$v){
			$$k = $v;
		}
		if(empty($picaddress)){
			showmsg('您还没有上传图鉴图片，请重新上传',PRE_URL,'error');
		}
		$description = $content;
		$sql = "UPDATE {$tablepre}photos SET title_en = '$title_en',title_cn = '$title_cn',sindex = '$sindex',pics = '$picaddress',description='$description',author='$author',draw='$draw',counts='$counts' WHERE photoid = '$id'";
		$db->query($sql);
		showmsg($title_cn.'|->图鉴编辑成功',PRE_URL,'success');
		break;
	case 'del' :
		$photosid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($photosid)){
			showmsg('请选择你要处理的项目，操作禁止',PRE_URL);
		}
		if($photos->deletephotosById($photosid)){
			showmsg('|->案例删除成功',PRE_URL,'success');
		}else{
			showmsg('|->案例删除失败',PRE_URL,'error');
		}
		break;
	case 'pukeedit':
		$photosid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($photosid)){
			showmsg('请选择你要编辑的项目，操作禁止',PRE_URL);
		}
		$photosdetail = $photos->getphotosDetail($photosid);
		foreach ($photosdetail as $k=>$v){
			$smarty->assign($k,$v);
		}
		$puke_list = array();
		$puke_list = $photos->getPukeList($photosid,$page,80);
		$count = count($puke_list);
		$start = $count;
		if($count > 78 ){
			$need = 78 - $count;
			$need = $need > 5 ? 5 : $need;
			for($i=1;$i<=$need;$i++){
				$start ++;
				$puke_list[$i]['flag'] = 0;
				$puke_list[$i]['id'] = $i;
				$puke_list[$i]['photoid'] = 0;
				$puke_list[$i]['picaddress'] = 'images/noimage.gif';
				$puke_list[$i]['orderby'] = $start;
			}
		}
		$smarty->assign('puke_list',$puke_list);
		$smarty->display('puke_edit.htm');
		break;
	case 'puke_update':
		break;
	case 'pukeadd':
		$classid = $input['classid'] ? $input['classid'] : '';
		if(empty($input['classid'])){
			showmsg('对不起，你没有选择要处理的项目',PRE_URL,'error');
		}
		if(empty($input['picaddress'])){
			showmsg('您忘记上传塔罗牌图片了',PRE_URL,'error');
		}
		$db->query("insert into {$tablepre}puke (classid,pukenumber,picaddress,orderby,description) values ('".$input['classid']."','".$input['pukenumber']."','".$input['picaddress']."','".$input['orderby']."','".$input['description']."')");
		
		if($db->insert_id()){
			showmsg('塔罗牌添加成功',PRE_URL,'success');
		}else{
			showmsg('塔罗牌添加失败',PRE_URL,'error');
		}
		break;
	case 'pukedel':
		$pukeid = isset($_GET['pukeid']) ? intval($_GET['pukeid']) : '';
		if(empty($pukeid)){
			showmsg('请选择你要处理的项目，操作禁止',PRE_URL);
		}
		if($photos->deletepukeById($pukeid)){
			showmsg('|->牌图片删除成功',PRE_URL,'success');
		}else{
			showmsg('|->牌图片删除失败',PRE_URL,'error');
		}
		break;
	case 'largeupload':
		$pukeid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($pukeid)){
			showmsg('请选择你要处理的项目，操作禁止',PRE_URL);
		}
		$photosdetail = $photos->getphotosDetail($pukeid);
		foreach ($photosdetail as $k=>$v){
			$smarty->assign($k,$v);
		}
		$smarty->display('puke_largeupload.htm');

}