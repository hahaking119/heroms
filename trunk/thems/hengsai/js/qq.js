// JavaScript Document
document.write("<div class='QQbox' id='divQQbox' >");

document.write("<div class='Qlist' id='divOnline' onmouseout='hideMsgBox(event);' style='display : none;'>");

document.write("<div class='t'></div>");

document.write("<div class='con'>");

document.write("<h2>��������</h2>");

document.write("<ul>");

document.write("<li class=odd><a href=' http://wpa.qq.com/msgrd?V=1&amp;Uin=1234567890&amp;Site=�л����޻��&amp;Menu=yes' target='_blank'><img src=' http://wpa.qq.com/pa?p=1:23981044:4'  border='0' alt='QQ' />���޿ͷ�</a></li>");


document.write('<tr><td><li><a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid=�л����޻��&site=�л����޻��&s=1&charset=utf-8" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=�л����޻��&site=�л����޻��&s=1&charset=utf-8" alt="�л����޻��" /></a></li></td></tr>');

document.write("</ul>");document.write("</div>");

document.write("<div class='b'></div>");

document.write("</div>");

document.write("<div id='divMenu' onmouseover='OnlineOver();'><img src='./thems/talor/images/qq_1.png' class='press' alt='��������'></div>");

document.write("</div>");



//<![CDATA[

var tips; var theTop = 40;/*����Ĭ�ϸ߶�,Խ��Խ����*/;
var old = theTop;

function initFloatTips() {

tips = document.getElementById('divQQbox');

moveTips();

};

function moveTips() {

var tt=50;

if (window.innerHeight) {

pos = window.pageYOffset

}

else if (document.documentElement && document.documentElement.scrollTop) {

pos = document.documentElement.scrollTop

}

else if (document.body) {

pos = document.body.scrollTop;

}

pos=pos-tips.offsetTop+theTop;

pos=tips.offsetTop+pos/10;



if (pos < theTop) pos = theTop;

if (pos != old) {

tips.style.top = pos+"px";

tt=10;

//alert(tips.style.top);

}



old = pos;

setTimeout(moveTips,tt);

}

//!]]>

initFloatTips();







function OnlineOver(){

document.getElementById("divMenu").style.display = "none";

document.getElementById("divOnline").style.display = "block";

document.getElementById("divQQbox").style.width = "145px";

}



function OnlineOut(){

document.getElementById("divMenu").style.display = "block";

document.getElementById("divOnline").style.display = "none";



}



function hideMsgBox(theEvent){ //theEvent���������¼���Firefox�ķ�ʽ

�� if (theEvent){

�� var browser=navigator.userAgent; //ȡ�����������

�� if (browser.indexOf("Firefox")>0){ //�����Firefox

���� if (document.getElementById('divOnline').contains(theEvent.relatedTarget)) { //�������Ԫ��

���� return; //������ʽ

} 

} 

if (browser.indexOf("MSIE")>0){ //�����IE

if (document.getElementById('divOnline').contains(event.toElement)) { //�������Ԫ��

return; //������ʽ

}

}

}

/*Ҫִ�еĲ���*/

document.getElementById("divMenu").style.display = "block";

document.getElementById("divOnline").style.display = "none";

}