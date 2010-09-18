<?php
class focuspic
{
	var $db;
	var $tablepre;
	var $xmlfile = 'xml/bcastr.xml';
	var $count = 0;
	
	function __construct(){
	$this->focuspic();
	}

	function focuspic(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	
	}
	public function getFocusList($channelid = 1,$catid = null ,$page = 1,$pagesize = 20){
		$start = ($page-1)*$pagesize;
		if($catid == null){
			$sql = "SELECT * FROM {$this->tablepre}focus WHERE channelid = '$channelid' ORDER BY orderby DESC,created DESC LIMIT $start,$pagesize";
			$count_sql = "SELECT COUNT(focusid) FROM {$this->tablepre}focus";
		}else{
			$sql = "SELECT * FROM {$this->tablepre}focus WHERE channelid = '$channelid' and catid = '$catid' ORDER BY orderby DESC,created DESC LIMIT $start,$pagesize";
			$count_sql = "SELECT COUNT(focusid) FROM {$this->tablepre}focus";
		}
		$count_sql = "SELECT COUNT(focusid) FROM {$this->tablepre}focus";
		$this->count = $this->db->getOne($count_sql);
		if($this->count < 1){
			return false;
		}else{
			return $this->db->getAll($sql);
		}
		
	}
	
	public function addFocus($channelid = 1,$catid = 0,$title,$photoaddress = null,$url = null,$order = 1,$state){
		global $systime;
		$sql = "INSERT INTO {$this->tablepre}focus (channelid,catid,title,picaddress,picurl,created,orderby,state) VALUES ('$channelid','$catid','$title','$photoaddress','$url','$systime','$order',$state)";
		$this->db->query($sql);
		if($picid = $this->db->insert_id()){
			return $picid;
		}else{
			return false;
		}
	}
	public function editFocus($id,$channelid = 1,$catid = 0,$title,$picaddress,$url,$order,$state){
		global $systime;
		$sql = "UPDATE {$this->tablepre}focus SET `channelid` = '$channelid',
				`catid` = '$catid',
				`title` = '$title',
				`picaddress` = '$picaddress',
				`picurl` = '$url',
				`created` = '$systime',
				`orderby` = '$order',
				`state` = '$state' WHERE `focusid` = '$id' LIMIT 1" ;
		$this->db->query($sql);
		if($this->db->affected_rows() >= 0){
			return true;
		}else{
			return false;
		}
	}
	public function focusDetail($id){
		$sql = "SELECT * FROM {$this->tablepre}focus WHERE focusid = '$id'";
		if($result = $this->db->getRow($sql)){
			return $result;
		}else{
			return false;
		}
	}
	public function deleteFocusById($id){
		$sql = "DELETE FROM {$this->tablepre}focus WHERE focusid = '$id'";
		$this->db->query($sql);
		if($this->db->affected_rows() >= 0){
			return true;
		}else{
			return false;
		}
	}
	public function getTopicPic($num){
		$sql = "SELECT * FROM {$this->tablepre}focus ORDER BY orderby DESC,created DESC LIMIT $num";
		if($piclist = $this->db->getAll($sql)){
			return $piclist;
		}else{
			return false;
		}
	}
	public function makePicxml($num){
		$pics = $this->getTopicPic($num);
		$outstr = '<?xml version="1.0" encoding="utf-8"?>'."\n".'<bcaster autoPlayTime="3">';
		foreach($pics as $k=>$v){
			$outstr .= '<item item_url="'.$v['picaddress'].'" link="'.$v['picurl'].'" ></item>'."\n";
		}
		$outstr .= "</bcaster>";
		if ($handle = fopen($this->xmlfile,'w')){
			@fputs($handle,$outstr,strlen($outstr));
			@fclose($handle);
			@chmod($this->xmlfile,0777);
		}
	}
	//生成xml文档
	public function makeXml($focus_name = 'upanddown',$num){
		$this->xmlfile = 'xml/'.$focus_name.'.xml'; 
		$xml_template = 'xml/tpl_'.$focus_name.'.xml'; 
		$pics = $this->getTopicPic($num);
		$outstr = '';
		$template_contents = file_get_contents($xml_template);
		foreach($pics as $k=>$v){
			$outstr .= '<item>'."\r\n".
					   '<title>'.$v['title'].'</title>'."\r\n".
					   '<link>'.$v['picurl'].'</link>'."\r\n".
					   '<image>'.$v['picaddress'].'</image>'."\r\n".
					   '<time>'.date('Y-m-d H:i:s',$v['created']).'</time>'."\r\n".
					   "</item>\r\n";
		}
		$template_contents = preg_replace('/{show_data}/',$outstr,$template_contents);
		if ($handle = fopen($this->xmlfile,'w')){
			@fputs($handle,$template_contents,strlen($template_contents));
			@fclose($handle);
			@chmod($this->xmlfile,0777);
		}
	}

}
?>