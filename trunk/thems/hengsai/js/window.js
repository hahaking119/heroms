(function($){
	$.viewport=function(){
		var w = (window.innerWidth) ? window.innerWidth : (document.documentElement && document.documentElement.clientWidth) ? document.documentElement.clientWidth : document.body.offsetWidth;
		var h = (window.innerHeight) ? window.innerHeight : (document.documentElement && document.documentElement.clientHeight) ? document.documentElement.clientHeight : document.body.offsetHeight;
		return {width:w,height:h};
	};
	$.fn.open=function(from){
		var self=$(this);
		if(!self.hasClass("window")){
			return self;
		}
		var cw=$.viewport().width;
		var ch=$.viewport().height;
		var dw=$(document).width();
		var dh=$(document).height();
		var st=$(document).scrollTop();
		var sl=$(document).scrollLeft();
		var w=self.width();
		var h=self.height();
		var t=(ch-h)/2>0?(ch-h)/2:0;
		var l=(cw-w)/2>0?(cw-w)/2:0;
		if(from){
			from.css({'z-index':99});
		}else{
			$("#mask").css({width:dw,height:dh,opacity:0.6}).show();
		}
		if($.browser.msie&&$.browser.version<=6){
			$("select").css("visibility","hidden");
		}
		return self.css({top:st+t,left:sl+l,'z-index':101}).fadeIn(300,function(){
			$(this).find("select").css("visibility","")
		});
	}
	$.fn.close=function(from){
		var self=$(this);
		if(!self.hasClass("window")){
			return self;
		}
		return self.find("select").css("visibility","hidden").end().fadeOut(300,function(){
			if($.browser.msie&&$.browser.version<=6){
				$("select").not(self.find("select")).css("visibility","");
			}
			if(from){
				from.css({'z-index':101});
			}else{
				$("#mask").hide();
			}
		});
	};
	$.dialog=function(arg){
		var op=$.extend({},$.dialog.def,arg);
		function init(){
			return $('<div id="dialog" class="window"><h2><strong></strong><span><a href="javascript:void(0)">X</a></span></h2><div class="body"><table style="width:100%"><tbody><tr><td class="icon"><img src="" /></td><td class="info"></td></tr><tr><td class="bar" colspan="2"></td></tr></tbody></table></div></div>').appendTo("body");
		}
		var dialog=$("#dialog").length==0?init():$("#dialog");
		dialog.css("width",op.width);
		dialog.find("h2 strong").text(op.title);
		dialog.find("h2 span a").unbind("click").bind("click",op.close);
		dialog.find(".icon img").attr("src",op.icon);
		dialog.find(".info").text(op.text);
		dialog.find(".bar").html("");
		$.each(op.btn,function(i,n){
			$("<button>"+n.text+"</button>").click(function(){
				n.handle();
				$("#dialog").close();
			}).appendTo(dialog.find(".bar"));
		});
		return dialog;
	};
	$.dialog.def={
		title:"新窗口",
		icon:"",
		text:"提示信息",
		width:200,
		btn:[{
			text:"OK",
			handle:function(){}
		}],
		close:function(){
			$("#dialog").close();
		}
	};
})(jQuery);
;(function($){
	$.fn.tab=function(arg){
		var self=$(this);
		var op=$.extend({},$.tab.def,arg);
		var head=self.find("h3");
		var tabs=head.find("a");
		var bodys=self.find("dl").hide();
		var curIndex=op.cur?op.cur:0;
		var curTab=tabs.eq(curIndex).addClass("current");
		var curBody=bodys.eq(curIndex).show();
		tabs.bind("click",function(){
			var tab=$(this);
			if(tab.hasClass("current")){				
				return;
			}
			curTab.removeClass("current");
			curTab=tab.addClass("current");
			curBody.slideUp(200);
			curBody=bodys.eq(tabs.index(tab)).slideDown(200);
		});
	};
	$.tab={
		def:{
			
		}
	};
})(jQuery);
$(function(){
	$(".main_menu").find("dt").click(function(){
		$(this).toggleClass("close").next("dd").slideToggle(250);
	});
	$("dt span.switch").parent().next("dd").hide();
	$(".more_service").find("dt span.switch").click(function(){
		$(this).text(function(){
			if($(this).parent().hasClass("close")){
				return "展开";
			}else{
				return "收起";
			}
		}).parent().toggleClass("close").next("dd").slideToggle(400);
	});
	$(".warm_tips").find("h2 span").click(function(){
		$(".warm_tips").slideToggle(400);
	});
});