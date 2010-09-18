<?php
/*==================================================================
*基本配置文件
*-------------------------------------------------------------------
*$filename config.php
*$author:aiway  $email:helloaiway@gmail.com
*$qq:303924813
*edit by aiway lastupdatetime:2009-3-23
====================================================================*/

// database host
$db_host   = "localhost";
// database name
$db_name   = "hengsai";
// database username
$db_user   = "root";
// database password
$db_pass   = "123456";
// table prefix
$tablepre    = "jp_";
$timezone    = "PRC";
$cookie_path    = "/";
$cookie_domain    = "xjtunj.com";
$admin_dir = "admin";
$html_dir = 'html';//定义静态文件的存放路径
$hostname = 'http://127.0.0.1/hengsai';
define('USE_STATIC',false);
if(!defined('HTML_DIR')) define('HTML_DIR',$html_dir);//静态化目录
if(!defined('HOSTNAME')) define('HOSTNAME',$hostname);//网站地址
if ( !defined('ABSPATH') ) define('ABSPATH', dirname(__FILE__) . '/');
define('USE_MULTIPLE_DB',FALSE);

$admincp = "陕西恒赛科技网络技术公司管理系统";
$sitename = "陕西恒赛科技网络技术公司";
$default_ext_title = '陕西恒赛科技网络技术公司';
$default_keywords = '陕西恒赛,恒赛,恒赛网络';
$default_description = '陕西恒赛科技网络技术公司';
$baseinfo = array(
	'corpname'	=>$sitename,
	'address'	=>'西安市',
	'tel'		=>'123454501',
	'mobile'	=>'123455',
	'fax'		=>'999999',
	'linkman'	=>'',
);
$session = "1440";
define('CZ_CHARSET','utf8');
define('SITE_CHARSET','utf-8');
define('UC_CONNECT', 'mysql');
define('UC_DBHOST', '121.15.220.145');
define('UC_DBUSER', 's461474db0');
define('UC_DBPW', 'mi234ma');
define('UC_DBNAME', 's461474db0');
define('UC_DBCHARSET', 'utf8');
define('UC_DBTABLEPRE', '`s461474db0`.uc_');
define('UC_DBCONNECT', '0');
define('UC_KEY', '123456789');
define('UC_API', 'http://xjtunj.com/center');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '121.15.220.145');
define('UC_APPID', '2');
define('UC_PPP', '20');
/*
define('CZ_CHARSET','utf8');
define('SITE_CHARSET','utf-8');


define('UC_CONNECT', 'mysql');				// 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()
							// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql

//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
define('UC_DBHOST', $db_host);			// UCenter 数据库主机
define('UC_DBUSER', 's461474db0');				// UCenter 数据库用户名
define('UC_DBPW', 'mi234ma');					// UCenter 数据库密码
define('UC_DBNAME', $db_name);				// UCenter 数据库名称
define('UC_DBCHARSET', 'gbk');				// UCenter 数据库字符集
define('UC_DBTABLEPRE', 'uc_');			// UCenter 数据库表前缀
define('UC_DBCONNECT', 0);

//通信相关
define('UC_KEY', '123456789');				// 与 UCenter 的通信密钥, 要与 UCenter 保持一致
define('UC_API', 'http://www.xjtunj.com/center');	// UCenter 的 URL 地址, 在调用头像时依赖此常量
define('UC_CHARSET', 'utf8');				// UCenter 的字符集
define('UC_IP', '');					// UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析域名有问题时, 请设置此值
define('UC_APPID', 2);					// 当前应用的 ID
define('UC_PPP', '20');
*/
?>