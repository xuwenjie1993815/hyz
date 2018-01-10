var pulicHttp="http://101.201.65.154/";
(function(){
	var header=document.querySelector("header");
	if(header){
		var myclass=header.className;
		var myindex=header.getAttribute("data-index");
		var hed='';
		if(myclass=="back-fff"){
			hed+='<header class="mui-bar mui-bar-nav back-fff color-org liingight-45">'
		}else{
			hed+='<header class="mui-bar mui-bar-nav mui-bar-top color-fff">'
		}
	    hed+='<div class="flex just-bween ali-item">'
	    	+'<div class="fontsize-12"><span class="mui-icon mui-icon-arrowleft"></span></div>'
			+'<div class="header_title">'+header.title+'</div>'
		if(myindex==2){
			hed+='<div style="width:24px;"></div>'
		}else{
			var city=localStorage.city||"重庆";
			hed+='<a href="location.html"><div class="fontsize-12"><div class="dingwei"><span class="mui-icon mui-icon mui-icon-location"></span></div>'+city+'</div></a>'
		}
	    hed+='</div></header>';
		header.innerHTML=hed;
	}
	var footer=document.querySelector("footer");
	if(footer){
		//创建footer菜单追加到页尾
		var nav='<nav class="mui-bar mui-bar-tab">'
	    +'<a class="mui-tab-item foot_nav foot_false" href="../home/index.html">'
	        +'<span class="mui-icon iconfont icon-shouye"></span>'
	        +'<span class="mui-tab-label">首页</span>'
	    +'</a>'
	    +'<a class="mui-tab-item foot_nav foot_false" href="../special/index.html">'
	        +'<span class="mui-icon iconfont icon-baobei"></span>'
	        +'<span class="mui-tab-label">专场</span>'
	    +'</a>'
	    +'<a class="mui-tab-item foot_nav foot_true" href="../shopping/index.html">'
	        +'<span class="mui-icon iconfont icon-gouwuche1"></span>'
	        +'<span class="mui-tab-label">购物车</span>'
	    +'</a>'
	    +'<a class="mui-tab-item foot_nav foot_true" href="../my/index.html">'
	        +'<span class="mui-icon iconfont icon-user"></span>'
	        +'<span class="mui-tab-label">我的好运</span>'
	    +'</a></nav>';
		footer.innerHTML=nav;
		if(footer.title){
			var ins=footer.title;
			document.getElementsByClassName("foot_nav")[ins].className="mui-tab-item mui-active";
		}else{
			document.getElementsByClassName("foot_nav")[0].className="mui-tab-item mui-active";
		}
	}else{
		document.querySelector(".mui-content").style.paddingBottom="0px"
	}
	//正常页面跳转
	mui('body').on('tap','a',function(e){
		e.stopPropagation()
		var href=this.href;
		mui.openWindow({
		    url:href,
		    id:href,
		    extras:{},
		    createNew:true,//是否重复创建同样id的webview，默认为false:不重复创建，直接显示
		    show:{
		      autoShow:true,//页面loaded事件发生后自动显示，默认为true
		      aniShow:"pop-in",//页面显示动画，默认为”slide-in-right“；
		    },
		})
	});
	//菜单跳转
	mui('nav').on('tap','.foot_true',function(e){
		e.stopPropagation()
		var href=this.href;
		mui.openWindow({
		    url:href,
		    id:href,
		    extras:{
		      //自定义扩展参数，可以用来处理页面间传值
		    },
		    createNew:true,//是否重复创建同样id的webview，默认为false:不重复创建，直接显示
		    show:{
		      autoShow:true,//页面loaded事件发生后自动显示，默认为true
		      aniShow:"none",//页面显示动画，默认为”slide-in-right“；
		    },
		})
		
	});
	mui('nav').on('tap','.foot_false',function(e){
		e.stopPropagation()
		var href=this.href;
		mui.openWindow({
		    url:href,
		    id:href,
		    extras:{
		      //自定义扩展参数，可以用来处理页面间传值
		    },
		    createNew:false,//是否重复创建同样id的webview，默认为false:不重复创建，直接显示
		    show:{
		      autoShow:true,//页面loaded事件发生后自动显示，默认为true
		      aniShow:"none",//页面显示动画，默认为”slide-in-right“；
		    },
		})
		
	});
	//返回按钮关闭页面
	mui("header,div").on("tap",".mui-icon-arrowleft",function(){
		mui.back();
	})
	//启用右滑关闭功能
	mui.init({
		swipeBack:true 
	});
	//flick 减速系数，系数越大，滚动速度越慢，滚动距离越小，默认值0.0006
	mui('.mui-scroll-wrapper').scroll({
		deceleration: 0.0005 
	});
})()
//时间戳转换
function timestamp(val){
	return new Date(parseInt(val)).toLocaleString().replace(/:\d{1,2}$/,' ');   
}
//登录
function login(){
	
	location.href="../login/login.html"
//	plus.webview.create('../login/login.html').show('slide-in-right', 300);
//	mui.openWindow({
//	    url:'../login/login.html',
//	    id:'../login/login.html',
//	    extras:{
//	      //自定义扩展参数，可以用来处理页面间传值
//	    },
//	    createNew:false,//是否重复创建同样id的webview，默认为false:不重复创建，直接显示
//	    show:{
//	      autoShow:true,//页面loaded事件发生后自动显示，默认为true
//	      aniShow:"pop-in",//页面显示动画，默认为”slide-in-right“；
//	    },
//	})
}
function checkLogin(){
	if(localStorage.userPhone==''||localStorage.userPhone==undefined||localStorage.userPhone==null){
		return true;
	}else{
		return false;
	}
}

function myOpen(id){
	if(id==undefined){
		mui.toast("无效的跳转");
		return;
	}
	mui.openWindow({
	    url:id,
	    id:id,
	    extras:{},
	    createNew:true,//是否重复创建同样id的webview，默认为false:不重复创建，直接显示
	    show:{
	      autoShow:true,//页面loaded事件发生后自动显示，默认为true
	      aniShow:"pop-in",//页面显示动画，默认为”slide-in-right“；
	    },
	})
}
