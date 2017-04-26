<!DOCTYPE html>
<html lang="en" data-ng-app="app">
<head>
  <meta charset="utf-8" />
  <title><?php echo $title;?></title>
  <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <?php if($debug == 'dev'):?>
	  <link rel="stylesheet" href="/backend/css/bootstrap.css" type="text/css" />
	  <link rel="stylesheet" href="/backend/css/animate.css" type="text/css" />
	  <link rel="stylesheet" href="/backend/css/font-awesome.min.css" type="text/css" />
	  <link rel="stylesheet" href="/backend/css/simple-line-icons.css" type="text/css" />
	  <link rel="stylesheet" href="/backend/css/font.css" type="text/css" />
	  <link rel="stylesheet" href="/backend/vendor/modules/angularjs-toaster/toaster.css" type="text/css" />
	  <link rel="stylesheet" href="/backend/css/app.css?v=3.0" type="text/css" />
  <?php else:?>
      <link rel="stylesheet" href="/backend/dist/css/all.min.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
  <?php endif;?>
  <script>
    var navName = getNavgationName();
	if(navName == 'ie'){
		var version = getIEVer();
		if(version < 10){
			alert("您的浏览器不支持该网站，请使用chrome、Firefox或者升级到IE9以上版本等现代浏览器！")
		}
	}
	else if(navName != 'ie11' && navName != 'opera' && navName != 'safari' && navName != 'firefox' && navName != 'chrome'){
		console.log(navName);
	}

	
	//获取IE浏览器的版本号
	//返回数值，显示IE的主版本号
	function getIEVer() {
		var ua = navigator.userAgent;//获取用户端信息
		var b = ua.indexOf("MSIE");//检测特殊字符串"MSIE "的位置
		if (b < 0) {
		  return ua;
		}
		return parseFloat(ua.substring(b + 5, ua.indexOf(";", b)));//截取版本号字符串，并转换为数值
	}
	
	function getNavgationName(){
		var ua = navigator.userAgent.toLowerCase();  //获取用户端信息
		var info = {
		  ie: /msie/.test(ua) && !/opera/.test(ua), //匹配IE浏览器
		  ie11: /trident/.test(ua) && /rv:11.0/.test(ua), //匹配IE浏览器
		  op: /opera/.test(ua), //匹配Opera浏览器
		  sa: /version.*safari/.test(ua), //匹配Safari浏览器
		  ch: /chrome/.test(ua), //匹配Chrome浏览器
		  ff: /gecko/.test(ua) && !/webkit/.test(ua) //匹配Firefox浏览器
		};
		
		if(info.ie){
			return "ie";
		}
		if(info.ie11){
			return "ie11";
		}
		else if(info.op){
			return "opera";
		}
		else if(info.sa){
			return "safari";
		}
		else if(info.ff){
			return "firefox";
		}
		else if(info.ch){
			return "chrome";
		}
	}
	<?php if($debug == 'dev'):?>
        path = '/backend/';
	<?php else:?>
	    path = '/backend/dist/';
	<?php endif;?>
  </script>

</head>
<body ng-controller="AppCtrl">
  <div class="app" id="app" ng-class="{'app-header-fixed':app.settings.headerFixed, 'app-aside-fixed':app.settings.asideFixed, 'app-aside-folded':app.settings.asideFolded, 'app-aside-dock':app.settings.asideDock, 'container':app.settings.container}" ui-view></div>

  <?php if($debug == 'dev'):?>
	  <!-- jQuery -->
	  <script src="/backend/vendor/jquery/jquery.min.js"></script>

	  <!-- Angular -->
	  <script src="/backend/vendor/angular/angular.js"></script>
	  
	  <!-- highcharts -->
	  <script src="/backend/vendor/Highcharts/highcharts.js"></script>
	  <script src="/backend/vendor/Highcharts/highcharts-ng.js"></script>
	  
	  <!-- umeditor -->
	  <!--
	  <script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
	  <script type="text/javascript" src="/ueditor/ueditor.all.min.js"></script>
	  <script type="text/javascript" src="/ueditor/angular-ueditor.js"></script>
	  <script type="text/javascript" src="/ueditor/lang/zh-cn/zh-cn.js"></script>
	  -->
	  <script src="/ueditor/editor.min.js"></script>
	  
	  <script src="/backend/vendor/angular/angular-animate/angular-animate.js"></script>
	  <!-- <script src="/backend/vendor/angular/angular-aria/angular-aria.js"></script>-->
	  <script src="/backend/vendor/angular/angular-cookies/angular-cookies.js"></script>
	  <!-- <script src="/backend/vendor/angular/angular-resource/angular-resource.js"></script>-->
	  <!-- <script src="/backend/vendor/angular/angular-sanitize/angular-sanitize.js"></script>-->
	  <script src="/backend/vendor/angular/angular-touch/angular-touch.js"></script>
	  <!-- ngMaterial -->
	  <!-- <script src="/backend/vendor/angular/angular-material/angular-material.js"></script>-->
	  
	  <!-- Vendor -->
	  <script src="/backend/vendor/angular/angular-ui-router/angular-ui-router.js"></script> 
	  <script src="/backend/vendor/angular/ngstorage/ngStorage.js"></script>
	
	  <!-- bootstrap -->
	  <script src="/backend/vendor/angular/angular-bootstrap/ui-bootstrap-tpls.js"></script>
	  <!-- lazyload -->
	  <script src="/backend/vendor/angular/oclazyload/ocLazyLoad.js"></script>
	  <!-- translate -->
	  <script src="/backend/vendor/angular/angular-translate/angular-translate.js"></script>
	  <script src="/backend/vendor/angular/angular-translate/loader-static-files.js"></script>
	  <script src="/backend/vendor/angular/angular-translate/storage-cookie.js"></script>
	  <script src="/backend/vendor/angular/angular-translate/storage-local.js"></script>
	  <script src="/backend/vendor/modules/angularjs-toaster/toaster.js"></script>
	  <script src="/backend/vendor/angular/angular-md5/angular-md5.js"></script>
  <?php else:?>
      <script src="/backend/dist/js/angular.lib.min.js"></script>
      <script src="/ueditor/editor.min.js"></script>
  <?php endif;?>


  <?php if($debug == 'dev'):?>
	  <!-- App -->
	  <script src="/backend/js/app.js"></script>
	  <script src="/backend/js/config.js"></script>
	  <script src="/backend/js/config.lazyload.js"></script>
	  <script src="/backend/js/config.router.js"></script>
	  <script src="/backend/js/main.js"></script>
  <?php else:?>
      <script src="/backend/dist/js/app.min.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>
  <?php endif;?>
  
  <?php if($debug == 'dev'):?>
      <!-- directives -->
	  <script src="/backend/js/services/ui-load.js"></script>
	  <!-- <script src="/backend/js/filters/fromNow.js"></script>-->
	  <script src="/backend/js/directives/setnganimate.js"></script>
	  <script src="/backend/js/directives/ui-butterbar.js"></script>
	  <script src="/backend/js/directives/ui-focus.js"></script>
	  <script src="/backend/js/directives/ui-fullscreen.js"></script>
	  <script src="/backend/js/directives/ui-jq.js"></script>
	  <script src="/backend/js/directives/ui-module.js"></script>
	  <script src="/backend/js/directives/ui-nav.js"></script>
	  <script src="/backend/js/directives/ui-scroll.js"></script>
	  <script src="/backend/js/directives/ui-shift.js"></script>
	  <script src="/backend/js/directives/ui-toggleclass.js"></script>
	  <!--<script src="/backend/js/directives/ui-validate.js"></script>-->
	  <script src="/backend/js/controllers/bootstrap.js"></script>
  
	  <!-- services -->
	  <script src="/backend/js/services/requestManager.js"></script>
	  <script src="/backend/js/services/data-load.js"></script>
	  <script src="/backend/js/services/divpage.js"></script>
	  <script src="/backend/js/services/commonFunction.js"></script>
	  <script src="/backend/js/services/interceptor.js"></script>
	  
	  <!-- single-date -->
	  <link rel="stylesheet" href="/backend/js/date-single-picker/single-date.css" type="text/css" />
	  <script src="/backend/js/date-single-picker/jquery-ui.min.js"></script>
	  <script src="/backend/js/date-single-picker/moment.min.js"></script>
	  <script src="/backend/js/date-single-picker/jquery.ui.datepicker-zh-CN.js"></script>
	  <script src="/backend/js/date-single-picker/single-date.js"></script>
	  
	  <link rel="stylesheet" href="/backend/js/date-single-picker/bootstrap-datetimepicker.min.css" type="text/css" />
	  <script src="/backend/js/date-single-picker/bootstrap-datetimepicker.min.js"></script>
	  <script src="/backend/js/date-single-picker/datetimepicker.js"></script>
	  
	  <!-- range-date -->
	  <link rel="stylesheet" href="/backend/js/date-range-picker/daterangepicker-bs3.css" type="text/css" />
	  <script src="/backend/js/date-range-picker/bootstrap.min.js"></script>
	  <script src="/backend/js/date-range-picker/daterangepicker.js"></script>
	  <script src="/backend/js/date-range-picker/ng-bs-daterangepicker.js"></script>
  <?php else:?>
      <script src="/backend/dist/js/directive.min.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>
      <script src="/backend/dist/js/single.date.min.js"></script>
      <script src="/backend/dist/js/service.min.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>
      <script src="/backend/dist/js/range.date.min.js"></script>
  <?php endif;?>
  
  <script src="/backend/vendor/layer/2.4/layer.js"></script>
</body>
</html>