<?php
/**
 * 下载处理模型
 */
class down
{
	var $db;
	var $file_dir = 'upload';
	var $error_msg = '';
	var $totalcount;
 	function __construct(){
		$this->down();
	}
	
	function down(){
		$this->db = $GLOBALS['db'];
	}
	
	function getDownList($catid = null,$order = 'DESC',$page = 1,$pagsize = 10){
		$start = ($page - 1)*$pagsize;
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}down ";
		$count_sql = "SELECT COUNT(downid) AS c FROM {$GLOBALS['tablepre']}down ";
		if(!empty($catid)){
			$sql .= "WHERE catid = '$catid'";
			$count_sql .= "WHERE catid = '$catid'";
		}
		$rc = $this->db->getOne($count_sql);
		$this->totalcount = $rc;
		$sql .= " ORDER BY created $order LIMIT $start,$pagsize";
 		if($rs = $this->db->getAll($sql)){
 			return $rs;		
 		}else{
 			return false;
 		}
	}
	
	function checkDAta($arr,$action = 'add'){
		if(empty($arr['title'])){
			return '标题不能为空!';
		}
		if($action == 'add'){
		if(empty($arr['fileaddress'])){
			return '你还没有上传文件呢!';
		}
		}
		return true;
	}
	//获得某个下载的全部信息
	function getDownInfo($id){
		$sql = "SELECT * FROM {$GLOBALS['tablepre']}down WHERE downid = '$id'";
		if($rs = $this->db->getRow($sql)){
			return $rs;
		}else{
			return false;
		}
	}
	function updatedowtimes($id){
		$this->db->query("UPDATE {$GLOBALS['tablepre']}down SET downtime = downtime + 1 WHERE downid = '$id'");
	}
	//删除指定的下载
	function deleteDownById($id){
		$downinfo = $this->getDownInfo($id);
		if(empty($downinfo['url1']) && !empty($downinfo['file1'])){
			$realfile = ROOT_PATH.$downinfo['file1'];
			if(file_exists($realfile)){
				 unlink($realfile);
			}
		}
		$sql = "DELETE FROM {$GLOBALS['tablepre']}down WHERE downid = '$id'";
		$this->db->query($sql);	
		if($this->db->affected_rows() > 0){
			return true;
		}
		return false;
	}
	function upload_file($upload, $dir = '', $file_name = '')
    {
        /* 没有指定目录默认为根目录 */
        if (empty($dir))
        {
            /* 创建当月目录 */
            $dir = date('Ym');
            $dir = ROOT_PATH . $this->file_dir . '/' . $dir . '/';
        }
        else
        {
            /* 创建目录 */
            $dir = ROOT_PATH . $this->file_dir . '/' . $dir . '/';
            if ($file_name)
            {
                $file_name = $dir . $file_name; // 将文件定位到正确地址
            }
        }

        /* 如果目标目录不存在，则创建它 */
        if (!file_exists($dir))
        {
            if (!make_dir($dir))
            {
                /* 创建目录失败 */
                $this->error_msg = sprintf("%s为只读", $dir);
                return false;
            }
        }

        if (empty($file_name))
        {
            $file_name = date('ymd').time();
            $file_name = $dir . $file_name . $this->get_filetype($upload['name']);
        }

        /* 允许上传的文件类型 */
        $allow_file_types = '|doc|xls|txt|zip|rar|ppt|pdf|wav|GIF|JPG|JEPG|PNG|BMP|SWF|';
        if (!check_file_type($upload['tmp_name'], $file_name, $allow_file_types))
        {
            $this->error_msg = '不允许上传该格式的文件';
            $this->error_no  =  ERR_INVALID_IMAGE_TYPE;
            return false;
        }

        if ($this->move_file($upload, $file_name))
        {
            return str_replace(ROOT_PATH, '', $file_name);
        }
        else
        {
            $this->error_msg = sprintf("%s文件上传失败", $upload['name']);
            return false;
        }
    }
    
   

    /**
     *  返回文件后缀名，如‘.php’
     *
     * @access  public
     * @param
     *
     * @return  string      文件后缀名
     */
    function get_filetype($path)
    {
        $pos = strrpos($path, '.');
        if ($pos !== false)
        {
            return substr($path, $pos);
        }
        else
        {
            return '';
        }
    }
    
    function move_file($upload, $target)
    {
        if (isset($upload['error']) && $upload['error'] > 0)
        {
            return false;
        }

        if (!move_upload_file($upload['tmp_name'], $target))
        {
            return false;
        }

        return true;
    }
	
}