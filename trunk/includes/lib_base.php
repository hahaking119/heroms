<?php
/*==================================================================
*公用基本函数库
*-------------------------------------------------------------------
*$filename lib_base.php
*$author:aiway  $email:helloaiway@gmail.com
*$qq:303924813
*edit by aiway lastupdatetime:2009-3-23
====================================================================*/

/**
 * 邮件发送
 *
 * @param: $name[string]        接收人姓名
 * @param: $email[string]       接收人邮件地址
 * @param: $subject[string]     邮件标题
 * @param: $content[string]     邮件内容
 * @param: $type[int]           0 普通邮件， 1 HTML邮件
 * @param: $notification[bool]  true 要求回执， false 不用回执
 * 使用的邮件函数类库 cls_email.php
 * @return boolean
 */
function send_mail($name, $email, $subject, $content, $type = 0, $notification=false)
{
	
}
//初始化输入
function init_input(){
	global $input;
	$input_arr = array($_POST,$_GET);
	foreach ($input_arr as $glob){
		foreach ($glob as $k=>$v){
			$input[$k] = addslashes_deep($v);
		}
	}
}

function cutstr($string, $cutlen, $endtag = '', $coding = 'utf-8')
{
    /*
    +-----------------------------------
    +	Total length
    +-----------------------------------
    */
    $length = strlen($string);
    /*
    +-----------------------------------
    +	Full return
    +-----------------------------------
    */
    if($length <= $cutlen) 
    {
        return $string;
    }
    else
    {
        $startid = 0;
        /*
        +-------------------------------
        +	UTF8 CODE, longer than gbk
        +-------------------------------
        */
        if($coding == 'utf-8') 
        {
            $endid = $ordid = $cuts = 0;
            while ($endid < $length) 
            {
                $ordno = ord($string[$endid]);
                if($ordno == 9 || $ordno == 10 || (32 <= $ordno && $ordno <= 126)) 
                {
                    $ordid = 1; $endid++; $cuts++;
                }
                elseif(194 <= $ordno && $ordno <= 223)
                {
                    $ordid = 2; $endid += 2; $cuts += 2;
                }
                elseif(224 <= $ordno && $ordno < 239)
                {
                    $ordid = 3; $endid += 3; $cuts += 2;
                }
                elseif(240 <= $ordno && $ordno <= 247)
                {
                    $ordid = 4; $endid += 4; $cuts += 2;
                }
                elseif(248 <= $ordno && $ordno <= 251)
                {
                    $ordid = 5; $endid += 5; $cuts += 2;
                }
                elseif($ordno == 252 || $ordno == 253)
                {
                    $ordid = 6; $endid += 6; $cuts += 2;
                }
                else
                {
                    $endid++;
                }
                if ($cuts >= $cutlen) 
                {
                    break;
                }
            }
            if ($cuts >= $cutlen)
            {
                $endid -= $ordid;
            }
        }
        /*
        +-------------------------------
        +	GBK,BIG5 and so on
        +-------------------------------
        */
        else
        {
            $endid = 0;
            for ($i=0;$i<$cutlen-2;$i++)
            {
                if (ord($string[$i]) > 127)
                {
                    $endid += 2;
                    $i++;
                }
                else
                {
                    $endid++;
                }
            }
        }
        /*
        +-------------------------------
        +	Return String;
        +-------------------------------
        */
        return substr($string,$startid,$endid).($endtag == '' ? $endtag : '...');
    }
}

/**
 * 获得当前格林威治时间的时间戳
 *
 * @return  integer
 */
function gmtime()
{
    return (time() - date('Z'));
}
/**
 * 检查文件类型
 *
 * @access      public
 * @param       string      filename            文件名
 * @param       string      realname            真实文件名
 * @param       string      limit_ext_types     允许的文件类型
 * @return      string
 */
function check_file_type($filename, $realname = '', $limit_ext_types = '')
{
    if ($realname)
    {
        $extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
    }
    else
    {
        $extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false)
    {
        return '';
    }

    $str = $format = '';

    $file = @fopen($filename, 'rb');
    if ($file)
    {
        $str = @fread($file, 0x400); // 读取前 1024 个字节
        @fclose($file);
    }
    else
    {
        if (stristr($filename, ROOT_PATH) === false)
        {
            if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
                $extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
                $extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
                $extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert')
            {
                $format = $extname;
            }
        }
        else
        {
            return '';
        }
    }

    if ($format == '' && strlen($str) >= 2 )
    {
        if (substr($str, 0, 4) == 'MThd' && $extname != 'txt')
        {
            $format = 'mid';
        }
        elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav')
        {
            $format = 'wav';
        }
        elseif (substr($str ,0, 3) == "\xFF\xD8\xFF")
        {
            $format = 'jpg';
        }
        elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt')
        {
            $format = 'gif';
        }
        elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A")
        {
            $format = 'png';
        }
        elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt')
        {
            $format = 'bmp';
        }
        elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt')
        {
            $format = 'swf';
        }
        elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0")
        {   // D0CF11E == DOCFILE == Microsoft Office Document
            if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc')
            {
                $format = 'doc';
            }
            elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls')
            {
                $format = 'xls';
            } elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt')
            {
                $format = 'ppt';
            }
        } elseif (substr($str ,0, 4) == "PK\x03\x04")
        {
            $format = 'zip';
        } elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt')
        {
            $format = 'rar';
        } elseif (substr($str ,0, 4) == "\x25PDF")
        {
            $format = 'pdf';
        } elseif (substr($str ,0, 3) == "\x30\x82\x0A")
        {
            $format = 'cert';
        } elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt')
        {
            $format = 'chm';
        } elseif (substr($str ,0, 4) == "\x2ERMF")
        {
            $format = 'rm';
        } elseif ($extname == 'sql')
        {
            $format = 'sql';
        } elseif ($extname == 'txt')
        {
            $format = 'txt';
        }
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false)
    {
        $format = '';
    }

    return $format;
}
function multLink($currentPage, $totalRecords, $url, $pageSize = 10){
//global $func_message;
if ($totalRecords <= $pageSize) return '';
$mult = '';
$totalPages = ceil($totalRecords / $pageSize);
$mult .= '<div class="pager">';
if ($currentPage > 1)
{
  $mult .= '<a href="'.$url.'page='.($currentPage - 1).'">上一页</a>';
}
if ($totalPages < 13)
{
  for ($counter = 1; $counter <= $totalPages; $counter++)
  {
   if ($counter == $currentPage)
   {
    $mult .= '<span class="current">'.$counter.'</span>'; 
   }
   else
   {
    $mult .= '<a href="'.$url.'page='.$counter.'">'.$counter.'</a>';
   }
  }
}
elseif ($totalPages > 11)
{
  if($currentPage < 7)  
  {
   for ($counter = 1; $counter < 10; $counter++)
   {
    if ($counter == $currentPage)
    {
     $mult .= '<span class="current">'.$counter.'</span>';
    }
    else
    {
     $mult .= '<a href="'.$url.'page='.$counter.'">'.$counter.'</a>';
    } 
   }
   $mult .= '<span>…</span><a href="'.$url.'page='.($totalPages-1).'">'.($totalPages-1).'</a><a href="'.$url.'page='.$totalPages.'">'.$totalPages.'</a>'; 
  }
  elseif($totalPages - 6 > $currentPage && $currentPage > 6)
  {
   $mult .= '<a href="'.$url.'page=1">1</a><a href="'.$url.'page=2">2</a><span>…</span>';
   for ($counter = $currentPage - 3; $counter <= $currentPage + 3; $counter++)
   {
    if ($counter == $currentPage)
    {
     $mult .= '<span class="current">'.$counter.'</span>'; 
    }
    else
    {
     $mult .= '<a href="'.$url.'page='.$counter.'">'.$counter.'</a>';
    }     
   }
   $mult .= '<span>…</span><a href="'.$url.'page='.($totalPages-1).'">'.($totalPages-1).'</a><a href="'.$url.'page='.$totalPages.'">'.$totalPages.'</a>';  
  }
  else
  {
   $mult .= '<a href="'.$url.'page=1">1</a><a href="'.$url.'page=2">2</a><span>…</span>';
   for ($counter = $totalPages - 8; $counter <= $totalPages; $counter++)
   {
    if ($counter == $currentPage)
    {
     $mult .= '<span class="current">'.$counter.'</span>'; 
    }
    else
    {
     $mult .= '<a href="'.$url.'page='.$counter.'">'.$counter.'</a>';
    }
   }
  }
}
if ($currentPage < $counter - 1)
{
  $mult .= '<a href="'.$url.'page='.($currentPage + 1).'" class="nextprev">下一页</a>';
}
else
{
  //$mult .= '<span class="nextprev">下一页</span>';
}
$mult .= '</div>';
return $mult;
}
/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder)
{
    $reval = false;

    if (!file_exists($folder))
    {
        /* 如果目录不存在则尝试创建该目录 */
        @umask(0);

        /* 将目录路径拆分成数组 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

        /* 如果第一个字符为/则当作物理路径处理 */
        $base = ($atmp[0][0] == '/') ? '/' : '';

        /* 遍历包含路径信息的数组 */
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;

                if ('..' == $val || '.' == $val)
                {
                    /* 如果目录为.或者..则直接补/继续下一个循环 */
                    $base .= '/';

                    continue;
                }
            }
            else
            {
                continue;
            }

            $base .= '/';

            if (!file_exists($base))
            {
                /* 尝试创建目录，如果创建失败则继续循环 */
                if (@mkdir(rtrim($base, '/'), 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        /* 路径已经存在。返回该路径是不是一个目录 */
        $reval = is_dir($folder);
    }

    clearstatcache();

    return $reval;
}
/**
 * 将上传文件转移到指定位置
 *
 * @param string $file_name
 * @param string $target_name
 * @return blog
 */
function move_upload_file($file_name, $target_name = '')
{
    if (function_exists("move_uploaded_file"))
    {
        if (move_uploaded_file($file_name, $target_name))
        {
            @chmod($target_name,0755);
            return true;
        }
        else if (copy($file_name, $target_name))
        {
            @chmod($target_name,0755);
            return true;
        }
    }
    elseif (copy($file_name, $target_name))
    {
        @chmod($target_name,0755);
        return true;
    }
    return false;
}
/**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function real_ip()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}

/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    }
}

/**
 * 获取服务器的ip
 *
 * @access      public
 *
 * @return string
 **/
function real_server_ip()
{
    static $serverip = NULL;

    if ($serverip !== NULL)
    {
        return $serverip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['SERVER_ADDR']))
        {
            $serverip = $_SERVER['SERVER_ADDR'];
        }
        else
        {
            $serverip = '0.0.0.0';
        }
    }
    else
    {
        $serverip = getenv('SERVER_ADDR');
    }

    return $serverip;
}
/**
 * 导入某个模型
 * @param $model
 */
function loadModel($model){
	if(!defined('ROOT_PATH')){
		die(-1);
	}
	$model = is_array($model) ? $model : array($model);
	foreach ($model as $m){
		$filepath = ROOT_PATH.'model/'.$m.'.php';
		if(!@file_exists($filepath)){
			die('['.$m.']模式文件不存在,请检查文件是否被损坏');
		}
		include_once($filepath);
	}
}

/**
 * 读取缓存文件
 *
 * @param unknown_type $cache_name 缓存的文件名字
 * @return unknown
 */
function read_static_cache($cache_name)
{
	if(!defined('ROOT_PATH')){
		die(-1);
	}
    static $result = array();
    if (!empty($result[$cache_name]))
    {
        return $result[$cache_name];
    }
    $cache_file_path = ROOT_PATH . '/data/' . $cache_name . '.php';
    if (file_exists($cache_file_path))
    {
        include_once($cache_file_path);
        $result[$cache_name] = $data;
        return $result[$cache_name];
    }
    else
    {
        return false;
    }
}

//显示信息
function showmsg($message, $url = '', $type = '') {
	extract($GLOBALS, EXTR_SKIP);
	switch($type) {
		case 'succeed': $classname = 'infotitle2';break;
		case 'error': $classname = 'infotitle3';break;
		case 'loading': $classname = 'infotitle1';break;
		default: $classname = 'infotitle2 normal';break;

	}
	$GLOBALS['smarty']->assign('classname', $classname);
    $GLOBALS['smarty']->assign('message',  $message);
    $GLOBALS['smarty']->assign('url',  $url);
    $GLOBALS['smarty']->assign('type',  $type);
    $GLOBALS['smarty']->display('message.htm');
	exit;
}

/**
 * 写缓存
 */

function write_static_cache($cache_name,$data){
	if(!defined('ROOT_PATH')){
		die(-1);
	}
	$cache_file = ROOT_PATH.'/data/'.$cache_file.'.php';
	$content = "<?php\r\n";
	$content .= "\$data = " .var_export($data).";\r\n";
	$content .= "?>";
	file_put_contents($cache_file,$content,LOCK_EX);
}

/**
 * Turn register globals off.
 * 关掉全局变量
 * @access private
 * @return null Will return null if register_globals PHP directive was disabled
 */
function _unregister_GLOBALS() {
	if ( !ini_get('register_globals') )
		return;

	if ( isset($_REQUEST['GLOBALS']) )
		die('GLOBALS overwrite attempt detected');

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ( $input as $k => $v )
		if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
}
function repeatString($spetor,$length=5){
	$str = '';
	for($i=0;$i<$length;$i++){
		$str .= $spetor;
	}
	return $str;
}

//全角半角转换
function full2half($str){
		//$full_str = "０１２３ＡＢＣＤＦＷＳ＼＂，．？＜＞｛｝［］＊＆＾％＃＠！～（）＋－｜：；";     
		return preg_replace('/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)', $str);
}

/**
 * 将需要被和谐的词加入到lib/denywords.php文件中
 * 判断用户发布的产品是否合法
 * @param String $string 待检查的字符串
 * @param Array $denywords 危险的关键词
 * @param String $replace 是否进行替换
 * @param String $replace_word 替换的文字
 * @return true:合法 string:替代后的字符串或相应的非法词
 */
function check_filter ( $string,$replace = true, $replace_word = '**' ) {
	include_once './denywords.php';
	$deny      = '';                                 //一个需要被屏蔽的关键词
	$res       = '';                                 //返回的被合谐的关键词
	//将提交上来的非UTF-8转换成UTF-8的编码
	if ( 'UTF-8' != mb_check_encoding( $string ) ) { $string = mb_convert_encoding( $string, 'UTF-8' );	}
	if ( $replace === true ) {                       //对输入的词进行替换
		$string = str_ireplace( $denywords, $replace_word, $string );
		return $string;
	} else {    
		$string = preg_replace('/\s/','',$string); //不进行替换则全部检查，遇到第一个不合格的词则返回该词
		foreach ( $denywords as $deny ) {            //遍历和谐词表
			if ( stristr ( $string, $deny ) != false ) {
				$res .= $deny . ',';
			}
		}
		if ( empty( $res ) ) {
			return false;                             //没有不合法的词
		} else{ 
			return $res; 
		} 
	}
}
