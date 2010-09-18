<?php
/**
 *aboutus.php
 * 关于系统
 */
$content = "版权说明：不允许对该系统进行二次发布，<br>制作：王恩伟 <br>联系电话：15934837618<br>E-MAIL:helloaiway@gmail.com<br>QQ:303924813";
$smarty->assign('about',$content);
$smarty->display('aboutus_index.htm');
