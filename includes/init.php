<?php
/**
 * 网站系统
 * 通用匹配初始化检查入口
 */
if (!defined('IN_CRAZY'))
{
    die('Hacking attempt');
}
error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得当前系统所在的根目录 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

//引入路径设置
if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path', '.:' . ROOT_PATH);
}

//引入配置文件
require(ROOT_PATH . 'data/config.php');

//脚本设置
$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);
//当前完整url
$current_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
define('CUR_URL',$current_url);
//来源url
$preurl = empty($_SERVER['HTTP_REFERER']) ? 'index.php' : $_SERVER['HTTP_REFERER'];
define('PRE_URL',$preurl);
/*开始引入需要的基类或者扩展需要的所有类库*/
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/cls_mysql.php');
//引入数据库
$db = new cls_mysql($db_host,$db_user,$db_pass,$db_name,$charset = 'gbk', $pconnect = 0, $quiet = 1);
//引入模板配置
require_once ROOT_PATH.'libs/smarty/smarty.class.php';
//引入后台生成的基本配置文件
$_cfg = load_config();
init_input();
class tpl extends Smarty {
	function tpl(){
		$this->Smarty();
		$this->template_dir = ROOT_PATH.'thems/'.$GLOBALS['_cfg']['them'].'/';
		$this->compile_dir = ROOT_PATH.'data/them_c/';
		$this->config_dir = ROOT_PATH.'libs/smarty/configs/';
		$this->cache_dir = ROOT_PATH.'data/them_cache/';
		$this->left_delimiter = "<{";
		$this->right_delimiter = "}>";
		$this->caching = false;
	}
}
$tpl = new tpl;
//$hostname = 'http://'.$_SERVER['HTTP_HOST'];
$themdir = $hostname.'/thems/'.$_cfg['them'].'/';
$tpl->assign('charset',SITE_CHARSET);
$tpl->assign('hostname',$hostname);
$tpl->assign('themdir',$themdir);
$tpl->assign('sitename',$sitename);
$tpl->assign('baseinfo',$baseinfo);

//确定语言 引入语言包
//require(ROOT_PATH . 'language/'.$_cfg['language'].'/common.php');
if ($_cfg['shop_closed'] == 1)
{
    /* 网站关闭了，输出关闭的消息 */
    header('Content-type: text/html; charset='.SITE_CHARSET);

    die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $_LANG['shop_closed'] . '</p><p>' . $_LANG['close_comment'] . '</p></div>');
}

/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}
//对请求的Q请求 进行默认设置
$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$page = max($page,1);
$pagesize = 20;
$q = isset($_GET['q']) ? trim($_GET['q']) : 'index';
?>