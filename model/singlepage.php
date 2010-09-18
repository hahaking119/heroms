<?php
class singlePage
{
	var $db;
	var $tablepre;
	private $tpl_dir = 'singlepage';
	function __construct(){
		$this->singlePage();
	}
	function singlePage(){
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}
	
	function getSingleList(){
		$sql = "select * from {$this->tablepre}singlepage order by singleid desc";
		if($rs = $this->db->getAll($sql)){
			return $rs;
		}else {
			return false;
		}
		
	}
	//获得网页的详细信息
	function getSingleDetail($id){
		$sql = "SELECT * FROM {$this->tablepre}singlepage WHERE singleid = '$id'";
		if($detail = $this->db->getRow($sql)){
			return $detail;
		}else{
			return false;
		}
		
	}
	//shanchu
	function deleteSinglePage($id){
		$sql = "DELETE FROM {$this->tablepre}singlepage WHERE singleid = '$id'";
		$this->db->query($sql);
		//同时删除生成的相应文件
		
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	//获取单页面的模板名称列表
	function getTplList(){
		global $_cfg;	
		$tpl_list = array("default");	
		$tpl_dir = './thems/'.$_cfg['them'].'/'.$this->tpl_dir;
		chdir($tpl_dir);
		$files = glob('*.*');
		foreach($files as $file){
			$file = basename($file,".html");
			if(strpos($file,'_')){			
				$filename_arr = explode('_',$file);
				$tpl_list[] = iconv('GBK','UTF-8',$filename_arr[1]);
			}
		}
		return $tpl_list;
	}
}