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

        if (!isset($GLOBALS['_CFG']['ecs_version']))
        {
            /* 如果没有版本号则默认为3.0 */
            $GLOBALS['_CFG']['cz_version'] = 'v2.0';
        }

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
		$infobutton = '<div class=pagenum>'.$totalrecored.'/'.$totalpage.'</div>';
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
	//获取后信息状态
	function get_status_header_desc( $code ) {
			$code = absint( $code );
			$header_to_desc = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				102 => 'Processing',
	
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				207 => 'Multi-Status',
				226 => 'IM Used',
	
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				306 => 'Reserved',
				307 => 'Temporary Redirect',
	
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				422 => 'Unprocessable Entity',
				423 => 'Locked',
				424 => 'Failed Dependency',
				426 => 'Upgrade Required',
	
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported',
				506 => 'Variant Also Negotiates',
				507 => 'Insufficient Storage',
				510 => 'Not Extended'
			);
	if ( isset( $header_to_desc[$code] ) )
		return $header_to_desc[$code];
	else
		return '';
}
//设置浏览器禁止缓存
function http_header_nocache(){
				header("CacheControl = no-cache");
				header("Pragma=no-cache");
				header("Expires = -1");
}

function GB(){
	return '丢并乱亘亚夫伫布占并来仑徇侣局俣系侠表伥俩仓个们幸仿伦伟逼侧侦伪杰伧伞备效家佣偬传伛债伤倾偻仅佥仙侨仆伪侥偾雇价仪侬亿侩俭傧俦侪尽偿优储俪傩傥俨凶兑儿兖内两册胄幂净冻凛凯别删刭则锉克刹刚剥剐剀创铲划札剧刘刽刿剑剂劲动务勋胜劳势绩劢勋励劝匀陶匦汇匮区协昂恤却厍厕厌厉厣参丛吴呐吕啕呙员呗念问启哑启衔唤丧乔单哟呛啬吗呜唢哔叹喽呕啧尝唛哗唠啸叽哓呒恶嘘哒哝哕嗳哙喷吨当咛吓哜噜啮呖咙向喾严嘤啭嗫嚣冁呓罗苏嘱囱囵国围园圆图团丘垭执坚垩埚尧报场块茔垲埘涂冢坞埙尘堑垫坠堕坟垦坛压垒圹垆坏垄坜坝壮壶寿够梦夹奂奥奁夺奖奋妆你姗奸侄娱娄妇娅娲妫媪妈妪妩娴妫娆婵娇嫱嫒嬷嫔婴婶娘娈孙学孪宫寝实宁审写宽宠宝将专寻对导尴届屉屡层屦属冈岘岛峡崃昆岗仑峥嵛岚嵝崭岖崂峤峄嵘岭屿岳岿峦巅巯卺帅师帐带帧帏帼帻帜币帮帱开干几库厕厢厩厦厨庙厂庑废广廪庐厅弑吊弪张强弹弥弯汇彦雕佛径从徕复彻汹恒汹耻悦怅闷凄敦恶恼恽恻爱惬悫怆恺忾栗态愠惨惭恸惯悫怄怂虑悭庆戚欲忧惫怜凭愦惮愤悯怃宪忆恳应怿懔怼懑恹惩懒怀悬忏惧慑恋戋戗戬战戏户抛挟舍扪卷扫抡挣挂采拣扬换挥背损摇捣掏抢捂掴掼搂挚抠抟掺捞撑挠捻挢掸拨抚扑揿挞挝捡拥掳择击挡担据挤拟摈拧搁掷扩撷摆擞撸扰摅撵拢拦撄搀撺携摄攒挛摊搅揽考败叙敌数敛毙斓斩断升时晋昼晕晖畅暂晔历昙晓暧旷晒书会胧术东栅杆栀条枭弃枨枣栋栈栖桠匾杨枫桢业极搌杩荣桤盘构枪连椠椁桨桩乐枞梁楼标枢样朴树桦桡桥机椭横檩柽档桧检樯台槟祢柠槛柜橹榈栉椟橼栎橱槠栌枥橥榇栊榉棂樱栏权榄钦叹欧欤欢岁历归殁残殒殇殚僵殓殡歼杀壳壳肴毁殴毵毡氇气氢氩氲氽泛污决没冲况汹浃泾凉凄泪渌净沦渊涞浅涣减涡测浑凑浈涌汤沩准沟温沧灭涤荥沪滞渗卤浒滚满渔沤汉涟渍涨溆渐浆颍泼洁沩潜润浔溃滗涠涩澄浇涝涧渑泽泶浍淀浊浓湿泞蒙济涛滥潍滨溅泺滤滢渎泻渖浏濒泸沥潇潆潴泷濑弥潋澜沣滠洒漓滩湾滦灾为乌烃无炼炜烟茕焕烦炀荧炝热炽烨灯炖烧烫焖营灿毁烛烩熏烬焘烁炉烂争为爷尔墙牍它牵荦犊牺状狭狈狰犹狲呆狱狮奖独狯猃狞获猎犷兽獭献猕猡珏佩现珐珲玮琐瑶莹玛琅琏玑瑷环玺琼珑璎瓒瓯产产苏亩毕画畲异当畴叠痉麻痹疯疡痪瘗疮疟疗痨痫瘅愈疠瘪痴痒疖症癞癣瘿瘾瘫癫发皑皲皱杯盗盏尽监盘卢汤众困睁睐睾眯瞒了睑蒙胧瞩矫炮朱硖砗砚硕砀确码砖碜碛矶硗础碍礴矿砺砾矾砻他佑秘禄祸祯御禅礼祢祷秃税秆禀扁种称谷稣积颖穑秽稳获窝洼穷窑窭窥窜窍窦灶窃竖竞笔笋笕笺筝节范筑箧笃筛筚箦篓箪简篑箫檐签帘篮筹箨籁笼签篱箩吁粤糁粪粮团粝籴纠纪纣约红纡纥纨纫纹纳纽纾纯纰纱纸级纷纭纺扎细绂绁绅绍绀绋绐绌终弦组绊绗结绝绦绞络绚给绒统丝绛绝绢绑绡绠绨绣绥困经综缍绿绸绻线绶维绾纲网绷缀彩纶绺绮绽绰绫绵绲缁紧绯绿绪缃缄缂线缉缎缔缗缘缌编缓缅纬缑缈练缏缇致萦缙缢缒绉缣缚缜缟缛县绦缝缡缩纵缧纤缦絷缕缥总绩绷缫缪缯织缮缭绕绣缋绳绘系茧缳缲缴绎继缤缱缬纩续缠缨纤缆钵罂坛罚骂罢罗罴羁芈羟羡义习翘专耧圣闻联聪声耸聩聂职聍听聋肃巯胁脉胫脱胀肾脶脑肿脚肠腽嗉肤胶腻胆脍脓脸脐膑腊胪脏卧临台与兴举旧衅铺舱舣舰舻艰艳刍苎兹荆豆庄茎荚苋华苌莱万莴扁叶荭苇药荤莳莅苍荪席盖莲苁荜卜蒌蒋葱茑荫荨蒇荞芸莸荛蒉荡芜萧蓣荟蓟芗姜蔷剃莶荐萨荠蓝荩艺药薮蕴苈蔼蔺蕲芦苏蕴苹藓蔹茏兰萝处虚虏号亏虬蛱蜕蚬蚀虾蜗蛳蚂萤蝼蛰蝈虮蝉蛲虫蛏蚁蝇虿蝎蛴蝾蜡蛎蛊蚕蛮杯众炫术卫冲只衮袅补装里制复裤裢褛亵裥裥袄裣裆褴袜衬袭见规觅视觇觋觎亲觊觏觐觑觉览觌观觞觯触订讣计讯讧讨讦训讪讫托记讹讶讼欣诀讷访设许诉诃诊注证诂诋讵诈诒诏评诎诅词咏诩询诣试诗诧诟诡诠诘话该详诜诙诖诔诛诓夸志认诳诶诞诱诮语诚诫诬误诰诵诲说说谁课谇诽谊调谄谆谈诿请诤诹诼谅论谂谀谍谝喧诨谔谛谐谏谕谘讳谙谌讽诸谚谖诺谋谒谓誊诌谎谜谧谑谡谤谦谥讲谢谣谣谟谪谬讴谨谩哗证谲讥撰谮识谯谭谱噪谵毁译议谴护誉读变谗让谶赞谠溪岂竖丰猪猫贝贞负财贡贫货贩贪贯责贮贳赀贰贵贬买贷贶费贴贻贸贺贲赂赁贿赅资贾贼赈赊宾赇周赉赐赏赔赓贤卖贱赋赕质账赌赖赚赙购赛赜贽赘赠赞赝赡赢赆赃赎赝赣赃赶赵趋迹践逾踊跄跸迹蹒踪糟跷趸踌跻跃踯踬蹑躏躯车轧轨军轩轫轭软轸轴轵轺轲轶轼较辂辁载轾辄挽辅轻辆辎辉辋辍辊辇辈轮辑辏输辐辗舆毂辖辕辘转辙轿辚轰辔办辞辫辩农回乃迳这连周进游运过达违遥逊递远适迟迁选遗辽迈还迩边逻逦郏邮郓乡邹邬郧邓郑邻郸邺郐邝郦腌盏酝丑酝医酱酿衅酽释钆钇钌钊钉钋针钓钐扣钏钒钗钍钕钯钫钭钠钝钩钤钣钞钮钧钙钬钛钪铌铈钶铃钴钹铍钰钸铀钿钾钜铊铉刨铋铂钳铆铅钺钵钩钲钼钽铰铒铬铪银铳铜铣铨铢铭铫衔铑铷铱铟铵铥铕铯铐焊锐销锑锉铝锒锌钡铤铗锋锊锓锄锔锇铺锐铖锆锂铽锯钢锞录锖锩锥锕锟锤锱铮锛锬锭钱锦锚锡锢错录锰表铼钔锴锅镀锷铡锻锸锲锹锾键锶锗针锺镁镑锁镉钨蓥镏铠铩锼镐镇镒镍镓镌镞镟链镆镙镝铿锵镗镘镛铲镜镖镂錾铧镤镪锈铙铴镣铹镦镡钟镫镨镄镌镰镯镭铁环铎铛镱铸鉴鉴铄镳钥镶镊锣钻銮凿长门闩闪闫闭开闶闳闰闲闲间闵闸阂阁阀闺闽阃阆闾阅阅阊阉阎阏阍阈阌阒板闱阔阕阑阗阖阙闯关阚阐辟闼陉陕升阵阴陈陆阳堤陧队阶陨际随险隐陇隶只隽虽双雏杂鸡离难云电沾雾霁雳霭灵靓静靥巩秋鞑千鞯韦韧韩韪韬韫韵响页顶顷项顺顸须顼颂颀颃预顽颁顿颇领颌颉颐颏头颊颔颈颓频颓颗题额颚颜颛颜愿颡颠类颟颢顾颤显颦颅风飑飒台刮飓飕飘飙飞饥饨饪饫饬饭饮饴饲饱饰饺饼饷养饵饽馁饿哺馀肴馄饯馅馆饧饩馏馊馍馒馐馑馈馔饥饶飨餍馋马驭冯驮驰驯驳驻驽驹驵驾骀驸驶驼驷骂骈骇驳骆骏骋骓骒骑骐骛骗骞骘骝腾驺骚骟骡蓦骜骖骠骢驱骁骄验惊驿骤驴骥骊肮髅脏体髋发松胡须鬓斗闹哄阋郁魉魇鱼鲁鲂鱿鲐鲍鲋鲒鲞鲕鲔鲛鲑鲜鲧鲠鲩鲤鲨鲻鲭鲞鲷鲱鲵鲲鲳鲸鲮鲰鲶鲫鲽鳇鳅鳄鳆鳃鲥鳏鳎鳐鳍鲢鳗鳔鳖鳝鳜鳞鲎鳄鲈鸟凫鸠凫凤鸣鸢鸩鸨鸦鸵鸳鸲鸱鸪鸯鸭鸸鸹鸿鸽鸺鹃鹆鹁鹈鹅鹄鹉鹌鹏鹎雕鹊鸫鹑鹕鹗鹜莺鹤鹘鹣鹞鸡鹧鸥鸶鹰鹭鹦鹳鸾卤咸鹾碱盐丽麦麸曲面黄黉点党黩黾鼋鼹齐斋齿龀龅龇龃龆龄出龈龊龉龋龌龙庞龚龟蹿后碱碱谰霉啮颧尸瓮艳痈钟才僳脔谫谳莼蓠岽猬余饷阄沈滟灏骅骣骧纣缵栾棂椤轳轹昵腼腭飙齑戆龛镔镧镬鸬鸷鹂鹇鹚鹨鹩鹪鹫鹬疴疱瘘颞笕笾簖粜糇糍趱酾跞跹蹰躜鼍雠鲚鲟鲡鲣鲦鲶鳌鳓鳕鳝鳟鳢髌黪厘余厮庵暗鳌杯膘别策尝扯吃酬助捶棰唇啖当荡捣抵翻旁痱干杠胳个构拐拐罐钎蚝合核呼胡糊冱碱剿浚愧馈捆捆累狸麻菱溜炉橹罗蒙妙蔑闵奶霓袅袅暖刨碰瓶旗强墙襁勤睿膻虱湿薯搜溯酸坛绦偷颓望嘻鹇泄修锈埙咽胭岩演焰雁燕夭野殷淫愈龠咱皂榨棹跖妆兹鬃钻碱僳伙鳖里么链么钟彝锨抬';
}

function GBK(){
	return '丟並亂亙亞伕佇佈佔併來侖侚侶侷俁係俠俵倀倆倉個們倖倣倫偉偪側偵偽傑傖傘備傚傢傭傯傳傴債傷傾僂僅僉僊僑僕僞僥僨僱價儀儂億儈儉儐儔儕儘償優儲儷儺儻儼兇兌兒兗內兩冊冑冪凈凍凜凱別刪剄則剉剋剎剛剝剮剴創剷劃劄劇劉劊劌劍劑勁動務勛勝勞勢勣勱勳勵勸勻匋匭匯匱區協卬卹卻厙厠厭厲厴參叢吳吶呂咷咼員唄唸問啓啞啟啣喚喪喬單喲嗆嗇嗎嗚嗩嗶嘆嘍嘔嘖嘗嘜嘩嘮嘯嘰嘵嘸噁噓噠噥噦噯噲噴噸噹嚀嚇嚌嚕嚙嚦嚨嚮嚳嚴嚶囀囁囂囅囈囉囌囑囪圇國圍園圓圖團坵埡執堅堊堝堯報場塊塋塏塒塗塚塢塤塵塹墊墜墮墳墾壇壓壘壙壚壞壟壢壩壯壺壽夠夢夾奐奧奩奪奬奮妝妳姍姦姪娛婁婦婭媧媯媼媽嫗嫵嫻嬀嬈嬋嬌嬙嬡嬤嬪嬰嬸孃孌孫學孿宮寢實寧審寫寬寵寶將專尋對導尷屆屜屢層屨屬岡峴島峽崍崑崗崙崢崳嵐嶁嶄嶇嶗嶠嶧嶸嶺嶼嶽巋巒巔巰巹帥師帳帶幀幃幗幘幟幣幫幬幵幹幾庫廁廂廄廈廚廟廠廡廢廣廩廬廳弒弔弳張強彈彌彎彙彥彫彿徑從徠復徹忷恆恟恥悅悵悶悽惇惡惱惲惻愛愜愨愴愷愾慄態慍慘慚慟慣慤慪慫慮慳慶慼慾憂憊憐憑憒憚憤憫憮憲憶懇應懌懍懟懣懨懲懶懷懸懺懼懾戀戔戧戩戰戲戶拋挾捨捫捲掃掄掙掛採揀揚換揮揹損搖搗搯搶摀摑摜摟摯摳摶摻撈撐撓撚撟撣撥撫撲撳撻撾撿擁擄擇擊擋擔據擠擬擯擰擱擲擴擷擺擻擼擾攄攆攏攔攖攙攛攜攝攢攣攤攪攬攷敗敘敵數斂斃斕斬斷昇時晉晝暈暉暢暫曄曆曇曉曖曠曬書會朧朮東柵桿梔條梟棄棖棗棟棧棲椏楄楊楓楨業極榐榪榮榿槃構槍槤槧槨槳樁樂樅樑樓標樞樣樸樹樺橈橋機橢橫檁檉檔檜檢檣檯檳檷檸檻櫃櫓櫚櫛櫝櫞櫟櫥櫧櫨櫪櫫櫬櫳櫸櫺櫻欄權欖欽歎歐歟歡歲歷歸歿殘殞殤殫殭殮殯殲殺殻殼殽毀毆毿氈氌氣氫氬氳汆汎汙決沒沖況洶浹涇涼淒淚淥淨淪淵淶淺渙減渦測渾湊湞湧湯溈準溝溫滄滅滌滎滬滯滲滷滸滾滿漁漚漢漣漬漲漵漸漿潁潑潔潙潛潤潯潰潷潿澀澂澆澇澗澠澤澩澮澱濁濃濕濘濛濟濤濫濰濱濺濼濾瀅瀆瀉瀋瀏瀕瀘瀝瀟瀠瀦瀧瀨瀰瀲瀾灃灄灑灕灘灣灤災為烏烴無煉煒煙煢煥煩煬熒熗熱熾燁燈燉燒燙燜營燦燬燭燴燻燼燾爍爐爛爭爲爺爾牆牘牠牽犖犢犧狀狹狽猙猶猻獃獄獅獎獨獪獫獰獲獵獷獸獺獻獼玀玨珮現琺琿瑋瑣瑤瑩瑪瑯璉璣璦環璽瓊瓏瓔瓚甌產産甦畝畢畫畬異當疇疊痙痲痺瘋瘍瘓瘞瘡瘧療癆癇癉癒癘癟癡癢癤癥癩癬癭癮癱癲發皚皸皺盃盜盞盡監盤盧盪眾睏睜睞睪瞇瞞瞭瞼矇矓矚矯砲硃硤硨硯碩碭確碼磚磣磧磯磽礎礙礡礦礪礫礬礱祂祐祕祿禍禎禦禪禮禰禱禿稅稈稟稨種稱穀穌積穎穡穢穩穫窩窪窮窯窶窺竄竅竇竈竊竪競筆筍筧箋箏節範築篋篤篩篳簀簍簞簡簣簫簷簽簾籃籌籜籟籠籤籬籮籲粵糝糞糧糰糲糴糾紀紂約紅紆紇紈紉紋納紐紓純紕紗紙級紛紜紡紮細紱紲紳紹紺紼紿絀終絃組絆絎結絕絛絞絡絢給絨統絲絳絶絹綁綃綆綈綉綏綑經綜綞綠綢綣綫綬維綰綱網綳綴綵綸綹綺綻綽綾綿緄緇緊緋緑緒緗緘緙線緝緞締緡緣緦編緩緬緯緱緲練緶緹緻縈縉縊縋縐縑縛縝縞縟縣縧縫縭縮縱縲縴縵縶縷縹總績繃繅繆繒織繕繚繞繡繢繩繪繫繭繯繰繳繹繼繽繾纈纊續纏纓纖纜缽罌罎罰罵罷羅羆羈羋羥羨義習翹耑耬聖聞聯聰聲聳聵聶職聹聽聾肅胇脅脈脛脫脹腎腡腦腫腳腸膃膆膚膠膩膽膾膿臉臍臏臘臚臟臥臨臺與興舉舊舋舖艙艤艦艫艱艷芻苧茲荊荳莊莖莢莧華萇萊萬萵萹葉葒葦葯葷蒔蒞蒼蓀蓆蓋蓮蓯蓽蔔蔞蔣蔥蔦蔭蕁蕆蕎蕓蕕蕘蕢蕩蕪蕭蕷薈薊薌薑薔薙薟薦薩薺藍藎藝藥藪藴藶藹藺蘄蘆蘇蘊蘋蘚蘞蘢蘭蘿處虛虜號虧虯蛺蛻蜆蝕蝦蝸螄螞螢螻蟄蟈蟣蟬蟯蟲蟶蟻蠅蠆蠍蠐蠑蠟蠣蠱蠶蠻衃衆衒術衛衝衹袞裊補裝裡製複褲褳褸褻襇襉襖襝襠襤襪襯襲見規覓視覘覡覦親覬覯覲覷覺覽覿觀觴觶觸訂訃計訊訌討訐訓訕訖託記訛訝訟訢訣訥訪設許訴訶診註証詁詆詎詐詒詔評詘詛詞詠詡詢詣試詩詫詬詭詮詰話該詳詵詼詿誄誅誆誇誌認誑誒誕誘誚語誠誡誣誤誥誦誨說説誰課誶誹誼調諂諄談諉請諍諏諑諒論諗諛諜諞諠諢諤諦諧諫諭諮諱諳諶諷諸諺諼諾謀謁謂謄謅謊謎謐謔謖謗謙謚講謝謠謡謨謫謬謳謹謾譁證譎譏譔譖識譙譚譜譟譫譭譯議譴護譽讀變讒讓讖讚讜谿豈豎豐豬貓貝貞負財貢貧貨販貪貫責貯貰貲貳貴貶買貸貺費貼貽貿賀賁賂賃賄賅資賈賊賑賒賓賕賙賚賜賞賠賡賢賣賤賦賧質賬賭賴賺賻購賽賾贄贅贈贊贋贍贏贐贓贖贗贛贜趕趙趨跡踐踰踴蹌蹕蹟蹣蹤蹧蹺躉躊躋躍躑躓躡躪軀車軋軌軍軒軔軛軟軫軸軹軺軻軼軾較輅輇載輊輒輓輔輕輛輜輝輞輟輥輦輩輪輯輳輸輻輾輿轂轄轅轆轉轍轎轔轟轡辦辭辮辯農迴迺逕這連週進遊運過達違遙遜遞遠適遲遷選遺遼邁還邇邊邏邐郟郵鄆鄉鄒鄔鄖鄧鄭鄰鄲鄴鄶鄺酈醃醆醖醜醞醫醬釀釁釅釋釓釔釕釗釘釙針釣釤釦釧釩釵釷釹鈀鈁鈄鈉鈍鈎鈐鈑鈔鈕鈞鈣鈥鈦鈧鈮鈰鈳鈴鈷鈸鈹鈺鈽鈾鈿鉀鉅鉈鉉鉋鉍鉑鉗鉚鉛鉞鉢鉤鉦鉬鉭鉸鉺鉻鉿銀銃銅銑銓銖銘銚銜銠銣銥銦銨銩銪銫銬銲銳銷銻銼鋁鋃鋅鋇鋌鋏鋒鋝鋟鋤鋦鋨鋪鋭鋮鋯鋰鋱鋸鋼錁錄錆錈錐錒錕錘錙錚錛錟錠錢錦錨錫錮錯録錳錶錸鍆鍇鍋鍍鍔鍘鍛鍤鍥鍬鍰鍵鍶鍺鍼鍾鎂鎊鎖鎘鎢鎣鎦鎧鎩鎪鎬鎮鎰鎳鎵鎸鏃鏇鏈鏌鏍鏑鏗鏘鏜鏝鏞鏟鏡鏢鏤鏨鏵鏷鏹鏽鐃鐋鐐鐒鐓鐔鐘鐙鐠鐨鐫鐮鐲鐳鐵鐶鐸鐺鐿鑄鑑鑒鑠鑣鑰鑲鑷鑼鑽鑾鑿長門閂閃閆閉開閌閎閏閑閒間閔閘閡閣閥閨閩閫閬閭閱閲閶閹閻閼閽閾閿闃闆闈闊闋闌闐闔闕闖關闞闡闢闥陘陝陞陣陰陳陸陽隄隉隊階隕際隨險隱隴隸隻雋雖雙雛雜雞離難雲電霑霧霽靂靄靈靚靜靨鞏鞦韃韆韉韋韌韓韙韜韞韻響頁頂頃項順頇須頊頌頎頏預頑頒頓頗領頜頡頤頦頭頰頷頸頹頻頽顆題額顎顏顓顔願顙顛類顢顥顧顫顯顰顱風颮颯颱颳颶颼飄飆飛飢飩飪飫飭飯飲飴飼飽飾餃餅餉養餌餑餒餓餔餘餚餛餞餡館餳餼餾餿饃饅饈饉饋饌饑饒饗饜饞馬馭馮馱馳馴駁駐駑駒駔駕駘駙駛駝駟駡駢駭駮駱駿騁騅騍騎騏騖騙騫騭騮騰騶騷騸騾驀驁驂驃驄驅驍驕驗驚驛驟驢驥驪骯髏髒體髖髮鬆鬍鬚鬢鬥鬧鬨鬩鬱魎魘魚魯魴魷鮐鮑鮒鮚鮝鮞鮪鮫鮭鮮鯀鯁鯇鯉鯊鯔鯖鯗鯛鯡鯢鯤鯧鯨鯪鯫鯰鯽鰈鰉鰍鰐鰒鰓鰣鰥鰨鰩鰭鰱鰻鰾鱉鱔鱖鱗鱟鱷鱸鳥鳧鳩鳬鳳鳴鳶鴆鴇鴉鴕鴛鴝鴟鴣鴦鴨鴯鴰鴻鴿鵂鵑鵒鵓鵜鵝鵠鵡鵪鵬鵯鵰鵲鶇鶉鶘鶚鶩鶯鶴鶻鶼鷂鷄鷓鷗鷥鷹鷺鸚鸛鸞鹵鹹鹺鹼鹽麗麥麩麯麵黃黌點黨黷黽黿鼴齊齋齒齔齙齜齟齠齡齣齦齪齬齲齷龍龐龔龜躥後堿鹼讕黴齧顴屍甕豔癰鍾纔傈臠譾讞蓴蘺崠蝟餘饟鬮瀋灩灝驊驏驤紂纘欒欞欏轤轢暱靦齶飆齏戇龕鑌鑭鑊鸕鷙鸝鷴鶿鷚鷯鷦鷲鷸痾皰瘺顳筧籩籪糶餱餈趲釃躒躚躕躦鼉讎鱭鱘鱺鰹鰷鯰鰲鰳鱈鱔鱒鱧髕黲釐餘廝菴闇鼇桮臕彆筴嚐撦喫詶耡搥箠脣啗儅盪擣觝繙徬疿榦槓肐箇搆枴柺鑵釬蠔閤覈謼衚餬沍硷勦濬媿餽梱綑纍貍痳蔆霤鑪艣儸懞玅衊湣嬭蜺嫋嬝煖鑤踫缾旂彊墻繈懃叡羶蝨溼藷蒐泝痠罈縚媮穨朢譆鷳洩脩銹壎嚥臙巖縯燄鴈鷰殀埜慇婬瘉籥偺皁搾櫂蹠粧玆騣鉆硷傈夥鼈裏麽鍊麼锺彜鍁擡';
}

function gbk2gb($str){
	$gb  = str_split(GB(),3);
	$gbk = str_split(GBK(),3);
	return str_replace($gbk,$gb,$str);
}
//处理添加的文章头信息
function deal_with_title($str){
		$str = gbk2gb(full2half($str));
		$str = strip_tags($str);
		$str = trim($str);
		return $str;		
}
//处理文章内容
function deal_with_content($str){
		$str = gbk2gb(full2half($str));
		$str = trim($str);
		return "&nbsp;&nbsp;".$str;		
}
//
