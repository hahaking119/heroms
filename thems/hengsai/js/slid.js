<SCRIPT language=javascript>
	//ͼƬ����չʾ Start
	var counts = 3;
	//��ͼ
	img1 = new Image();
	img1.src = 'http://images1.www.net.cn/static/images/index_ad091225.jpg';
	img2 = new Image();
	img2.src = 'http://images1.www.net.cn/static/images/index_ad100118.jpg';
	img3 = new Image();
	img3.src = 'http://images1.www.net.cn/static/images/index_ad091230.jpg';

	var smallImg = new Array();
	//Сͼ
	smallImg[0] = 'http://images1.www.net.cn/static/images/index_adb1.gif';
	smallImg[1] = 'http://images1.www.net.cn/static/images/index_adb2.gif';
	smallImg[2] = 'http://images1.www.net.cn/static/images/index_adb3.gif';

	//���ӵ�ַ
	url1 = new Image();
	url1.src = 'http://www.net.cn/static/discount/newyear_sale.asp?aid=ad01_091225l';
	url2 = new Image();
	url2.src = 'http://www.net.cn/static/gongyidns/haidi.asp?aid=ad02_100118l';
	url3 = new Image();
	url3.src = 'http://www.net.cn/static/discount/discount12.asp?aid=ad03_091201y';
	//altֵ
	alt1 = new Image();
	alt1.alt = '��վ�ռ��籩��ʥ��Ԫ��������';
	alt2 = new Image();
	alt2.alt = '�����ĺ��أ�����İ���-����Ͱͼ����򺣵�������200��Ԫ';
	alt3 = new Image();
	alt3.alt = '����ů�� ���˫н';
	//
	var nn = 1;
	var key = 0;
	function change_img() {
		if (key == 0) {
			key = 1;
		} else if (document.all) {
			document.getElementById("pic").filters[0].Apply();
			document.getElementById("pic").filters[0].Play(duration = 2);
		}
		eval('document.getElementById("pic").src=img' + nn + '.src');
		eval('document.getElementById("url").href=url' + nn + '.src');
		eval('document.getElementById("pic").alt=alt' + nn + '.alt');
		if (nn == 1) {
			document.getElementById("url").target = "_blank";
			document.getElementById("url").style.cursor = "pointer";
		} else {
			document.getElementById("url").target = "_blank"
			document.getElementById("url").style.cursor = "pointer"
		}

		for ( var i = 1; i <= counts; i++) {
			document.getElementById("xxjdjj" + i).className = 'axx';
		}
		document.getElementById("xxjdjj" + nn).className = 'bxx';
		nn++;
		if (nn > counts) {
			nn = 1;
		}
		tt = setTimeout('change_img()', 7000);
	}
	function changeimg(n) {
		nn = n;
		window.clearInterval(tt);
		change_img();
	}
	function ImageShow() {
		document.write('<div class="picshow_main">');
		document.write('<div><a id="url"><img id="pic" class="imgbig" /></a></div>');
		document.write('<div class="picshow_change">');
		for ( var i = 0; i < counts; i++) {
			document.write('<a href="javascript:changeimg(' + (i + 1)
					+ ');" id="xxjdjj' + (i + 1)
					+ '" class="axx" target="_self"><img src="' + smallImg[i]
					+ '"></a>');
		}
		document.write('</div></div>');
		change_img();
	}
	//ͼƬ����չʾ End
</SCRIPT>
<SCRIPT language=javascript type=text/javascript>
	ImageShow();
</SCRIPT>