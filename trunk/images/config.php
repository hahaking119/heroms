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
$db_name   = "tarotdiscuz";
// database username
$db_user   = "tarot";
// database password
$db_pass   = "tarot20070413";
// table prefix
$tablepre    = "jp_";
$timezone    = "PRC";
$cookie_path    = "/";
$cookie_domain    = "";
$admin_dir = "admin";
$html_dir = 'html';//定义静态文件的存放路径
$hostname = 'http://www.cntarot.com';
define('USE_STATIC',false);
if(!defined('HTML_DIR')) define('HTML_DIR',$html_dir);//静态化目录
if(!defined('HOSTNAME')) define('HOSTNAME',$hostname);//网站地址
if ( !defined('ABSPATH') ) define('ABSPATH', dirname(__FILE__) . '/');
define('USE_MULTIPLE_DB',FALSE);
$admincp = "塔罗会馆-信息管理系统";
$sitename = "中华塔罗会馆(China Tarot Assoication)";
$default_ext_title = '最专业的塔罗网站——开启生命的智慧！塔罗,星座,生命数字,水晶,灵摆,心灵成长,身心灵';
$default_keywords = '中华塔罗会馆 塔罗,星座,生命数字,水晶,灵摆,心灵成长,身心灵';
$default_description = '中华塔罗会馆论坛(A China Tarot Association)——中国最专业的塔罗牌学院,致力于为喜爱塔罗、星座、生命数字、水晶、灵摆、心灵成长、身心灵健康的初学者、爱好者和研究者提供全面的塔罗学习资讯和心灵成长服务。';
$session = "1440";
define('CZ_CHARSET','utf8');
define('SITE_CHARSET','utf-8');

?>