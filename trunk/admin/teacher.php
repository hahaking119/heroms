<?php
/**
 * 教师
 */
loadModel('teacher');
$teacher = new teacher();
switch ($a) {
	case 'ls' :
		$pagesize = 20;
		$teacherlist = $teacher->getTeacherList($page,$pagesize);
		//print_r($teacherlist);
		if($teacherlist){
			$smarty->assign('teacher',$teacherlist);
		}else{
			$smarty->assign('nullmsg',"暂时还没有教师信息");
		}
		$smarty->display('teacher_ls.htm');
		break;
	case 'add':
		$time = date('Y-m-d');
		$smarty->assign('time',$time);
		$smarty->display('teacher_add.htm');
		break;
	case 'edit':
		$tid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($tid)){
			showmsg('请选择你要编辑的项目，操作禁止',PRE_URL);
		}
		if($teacher_detail = $teacher->getTeacherDetail($tid)){
			foreach($teacher_detail as $k=>$v){	
				$$k = $v;
				$smarty->assign($k,$v);
			}
			if(!file_exists(ROOT_PATH.$photo)){
				$image = 'images/noimage.gif';
				$smarty->assign('noimage',$image);
				$smarty->assign('photo','');
			}
			
			$created = date('Y-m-d',$created);
			$smarty->assign('created',$created);
			$smarty->display('teacher_edit.htm');
		}
		else{
				showmsg('你选择的处理项目不存在，操作禁止',PRE_URL);
		}
	
		break;
	case 'del':
		$teacherid = isset($_GET['id']) ? intval($_GET['id']) : '';
		if(empty($teacherid)){
			showmsg('请选择你要处理的项目，操作禁止',PRE_URL);
		}
		if($teacher->deleteTeacherById($teacherid)){
			showmsg('|->教师信息删除成功',PRE_URL,'success');
		}else{
			showmsg('|->教师信息删除失败',PRE_URL,'error');
		}
		break;
	case 'update':
		$tid = intval($_POST['teacherid']);
		if(empty($tid)){
			showmsg('对不起，你没有选择要处理的项目',PRE_URL,'error');
		}
		foreach($_POST AS $k=>$v){
			$v = trim($v);
			$$k = addslashes($v);
		}
		$sql = "UPDATE {$tablepre}teachers SET `teacher_name` = '$teacher_name',
				`gender` = '$gender',
				`orderby` = '$orderby',
				`birthday` = '$birthday',
				`photo` = '$picaddress',
				`degree` = '$degree',
				`profession` = '$profession',
				`college` = '$college',
				`duty` = '$duty',
				`searching` = '$searching',
				`telphone` = '$telphone',
				`fax` = '$fax',
				`address` = '$address',
				`email` = '$email',
				`patch` = '$patch',
				`teach_info` = '$teach_info',
				`search_info` = '$search_info',
				`groupid`	= '$groupid',
				`homepage`	= '$homepage'
				WHERE `tid` = $tid LIMIT 1";
		$db->query($sql);
		if($db->affected_rows() >0){
			showmsg($teacher_name.'|->教师信息编辑成功',PRE_URL,'success');
		}else{
			showmsg($teacher_name.'|->教师信息编辑失败',PRE_URL,'error');
		}
		
		break;
	case 'save':
		foreach($_POST AS $k=>$v){
			$v = trim($v);
			$$k = addslashes($v);
		}
		$created = $updated = $systime;
		$sql = "INSERT INTO {$tablepre}teachers (
				`teacher_name` ,
				`gender` ,
				`orderby` ,
				`birthday` ,
				`photo` ,
				`degree` ,
				`duty` ,
				`searching` ,
				`profession` ,
				`college` ,
				`telphone` ,
				`fax` ,
				`address` ,
				`email` ,
				`patch` ,
				`teach_info` ,
				`search_info` ,
				`created` ,
				`updated` ,
				`groupid`,
				`homepage`
				)
				VALUES (
				'$teacher_name', '$gender', '$orderby', '$birthday', '$picaddress', '$degree', '$profession', '$college',',$duty','$searching', '$telphone', '$fax', '$address', '$email','$patch', '$teach_info', '$search_info', '$created', '$updated','$groupid','$homepage'
				)";
		$db->query($sql);
		if($db->insert_id() > 0){
			showmsg("添加教师成功",PRE_URL,'success');
		}else{
			showmsg('添加失败',PRE_URL,'error');
		}
		break;
}
?>