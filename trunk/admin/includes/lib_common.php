<?php
/**
 * 载入配置信息
 *
 * @access  public
 * @return  array
 */
function load_config()
{
    $arr = array();
    $data = read_static_cache('setting_config');
    if ($data === false)
    {
        $sql = 'SELECT code, value FROM {$tablepre}site_config WHERE parent_id > 0';
        $res = $GLOBALS['db']->getAll($sql);
        foreach ($res AS $row)
        {
            $arr[$row['code']] = $row['value'];
        }

        /* 对数值型设置处理 */
        $arr['watermark_alpha']      = intval($arr['watermark_alpha']);
        $arr['market_price_rate']    = floatval($arr['market_price_rate']);
        $arr['integral_scale']       = floatval($arr['integral_scale']);
        //$arr['integral_percent']     = floatval($arr['integral_percent']);
        $arr['cache_time']           = intval($arr['cache_time']);
        $arr['thumb_width']          = intval($arr['thumb_width']);
        $arr['thumb_height']         = intval($arr['thumb_height']);
        $arr['image_width']          = intval($arr['image_width']);
        $arr['image_height']         = intval($arr['image_height']);
        $arr['best_number']          = !empty($arr['best_number']) && intval($arr['best_number']) > 0 ? intval($arr['best_number'])     : 3;
        $arr['new_number']           = !empty($arr['new_number']) && intval($arr['new_number']) > 0 ? intval($arr['new_number'])      : 3;
        $arr['hot_number']           = !empty($arr['hot_number']) && intval($arr['hot_number']) > 0 ? intval($arr['hot_number'])      : 3;
        $arr['promote_number']       = !empty($arr['promote_number']) && intval($arr['promote_number']) > 0 ? intval($arr['promote_number'])  : 3;
        $arr['top_number']           = intval($arr['top_number'])      > 0 ? intval($arr['top_number'])      : 10;
        $arr['history_number']       = intval($arr['history_number'])  > 0 ? intval($arr['history_number'])  : 5;
        $arr['comments_number']      = intval($arr['comments_number']) > 0 ? intval($arr['comments_number']) : 5;
        $arr['article_number']       = intval($arr['article_number'])  > 0 ? intval($arr['article_number'])  : 5;
        $arr['page_size']            = intval($arr['page_size'])       > 0 ? intval($arr['page_size'])       : 10;
        $arr['bought_goods']         = intval($arr['bought_goods']);
        $arr['goods_name_length']    = intval($arr['goods_name_length']);
        $arr['top10_time']           = intval($arr['top10_time']);
        $arr['goods_gallery_number'] = intval($arr['goods_gallery_number']) ? intval($arr['goods_gallery_number']) : 5;
        $arr['no_picture']           = !empty($arr['no_picture']) ? str_replace('../', './', $arr['no_picture']) : 'images/no_picture.gif'; // 修改默认商品图片的路径
        $arr['qq']                   = !empty($arr['qq']) ? $arr['qq'] : '';
        $arr['ww']                   = !empty($arr['ww']) ? $arr['ww'] : '';
        $arr['default_storage']      = isset($arr['default_storage']) ? intval($arr['default_storage']) : 1;
        $arr['min_goods_amount']     = isset($arr['min_goods_amount']) ? floatval($arr['min_goods_amount']) : 0;
        $arr['one_step_buy']         = empty($arr['one_step_buy']) ? 0 : 1;
        $arr['invoice_type']         = empty($arr['invoice_type']) ? array('type' => array(), 'rate' => array()) : unserialize($arr['invoice_type']);
        $arr['show_order_type']      = isset($arr['show_order_type']) ? $arr['show_order_type'] : 0;    // 显示方式默认为列表方式
        $arr['help_open']            = isset($arr['help_open']) ? $arr['help_open'] : 1;    // 显示方式默认为列表方式
        //限定语言项
        $lang_array = array('zh_cn', 'zh_tw');
        if (empty($arr['lang']) || !in_array($arr['lang'], $lang_array))
        {
            $arr['lang'] = 'zh_cn'; // 默认语言为简体中文
        }
        write_static_cache('setting_config', $arr);
    }
    else
    {
        $arr = $data;
    }

    return $arr;
}

function gotoerror($url){
	$url = empty($url) ? 'index' : $url;
	header("location:$url");
}

//来个分页函数吧
function mutipage($url = 'index.php?page=',$totalrecored,$currentpage,$perpagenum){
		$totalpage = ceil($totalrecored/$perpagenum);
		$prepage = $currentpage - 1;
		$nextpage = $currentpage + 1;
		$dispage = '';
		$infobutton = '<div class=pagenum_a>'.$totalrecored.'/'.$totalpage.'</div>';
		$firstbutton = '<div class=prepage><a href="'.$url.'1">首页</a></div>';
		$endbutton = '<div class=prepage><a href="'.$url.$totalpage.'">尾页</a></div>';
		$urlprebutton = $infobutton.$firstbutton.'<div class=prepage><a href="'.$url.$prepage.'">上一页</a></div>';
		$prebutton = $infobutton.$firstbutton.'<div class=graypage>上一页</div>';
		$urlnextbutton = '<div class=prepage><a href="'.$url.$nextpage.'">下一页</a></div>'.$endbutton;
		$nextbutton = '<div class=graypage>下一页</div>'.$endbutton;
			if($totalpage == 1){
			$dispage .= $prebutton.'<div class=pagenum>1</div>'.$nextbutton;
			}elseif($totalpage > 1 && $totalpage <= 10){
				$startpage = 1;
				$endpage = $totalpage;
				$dispage .= $currentpage == 1 ? $prebutton : $urlprebutton;
					for($i = $startpage;$i <= $endpage;$i ++){
						$dispage .= $currentpage == $i ? '<div class=currentnum>'.$i.'</div>' : '<div class=pagenum><a href="'.$url.$i.'">'.$i.'</a></div>';
					}
				$dispage .= $currentpage == $totalpage ? $nextbutton : $urlnextbutton;
			}elseif($totalpage > 10){
				$dispage .= $currentpage == 1 ? $prebutton : $urlprebutton;
				if($currentpage <= 10){
					for($i = 1;$i <=10;$i++){
						$dispage .= $currentpage == $i ? '<div class=currentnum>'.$i.'</div>' : '<div class=pagenum><a href="'.$url.$i.'">'.$i.'</a></div>';
					}
				}else{
				$endpage = $currentpage + 5 < $totalpage ? $currentpage + 5 : $totalpage;
				$startpage = $endpage - 10;
					for($i = $startpage;$i <= $endpage;$i ++){
						$dispage .= $currentpage == $i ? '<div class=currentnum>'.$i.'</div>' : '<div class=pagenum><a href="'.$url.$i.'">'.$i.'</a></div>';
					}
				}
				$dispage .= $currentpage == $totalpage ? $nextbutton : $urlnextbutton;
			}
			return '<div class=mutipage>'.$dispage.'</div>';
	}