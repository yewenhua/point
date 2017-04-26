<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>没有权限</title>
<script src="/backend/vendor/jquery/jquery.min.js"></script>
<script src="/media/js/alert.js"></script>
<link rel="stylesheet" href="/media/css/alert.css" type="text/css" />
<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}
/*animate 404 begin*/
.html-content {
    width: 100%;
    height: 100%;
    position: relative;
    text-align: center;
    padding-top:100px;
}
.errortitle span {
    display: inline-block;
    font-size: 120px;
    background: none repeat scroll 0% 0% #333;
    color: #FFF;
    line-height: normal;
    padding: 10px 30px;
    margin-left: 7px;
}
.animate1{
   	-webkit-animation-duration: 0.8s;
	-webkit-animation-delay: 0.2s;
	-webkit-animation-timing-function: ease;
	-webkit-animation-fill-mode: both;
	-moz-animation-duration: 0.8s;
	-moz-animation-delay: 0.2s;
	-moz-animation-timing-function: ease;
	-moz-animation-fill-mode: both;
	-ms-animation-duration: 0.8s;
	-ms-animation-delay: 0.2s;
	-ms-animation-timing-function: ease;
	-ms-animation-fill-mode: both;
	animation-duration: 0.8s;
	animation-delay: 0.2s;
	animation-timing-function: ease;
	animation-fill-mode: both;	          
} 

.animate2{
   	-webkit-animation-duration: .8s;
	-webkit-animation-delay: .4s;
	-webkit-animation-timing-function: ease;
	-webkit-animation-fill-mode: both;
	-moz-animation-duration: .8s;
	-moz-animation-delay: .4s;
	-moz-animation-timing-function: ease;
	-moz-animation-fill-mode: both;
	-ms-animation-duration: .8s;
	-ms-animation-delay: .4s;
	-ms-animation-timing-function: ease;
	-ms-animation-fill-mode: both;
	animation-duration: .8s;
	animation-delay: .4s;
	animation-timing-function: ease;
	animation-fill-mode: both;	          
} 

.animate3{
   	-webkit-animation-duration: .8s;
	-webkit-animation-delay: .6s;
	-webkit-animation-timing-function: ease;
	-webkit-animation-fill-mode: both;
	-moz-animation-duration: .8s;
	-moz-animation-delay: .6s;
	-moz-animation-timing-function: ease;
	-moz-animation-fill-mode: both;
	-ms-animation-duration: .8s;
	-ms-animation-delay: .6s;
	-ms-animation-timing-function: ease;
	-ms-animation-fill-mode: both;
	animation-duration: .8s;
	animation-delay: .6s;
	animation-timing-function: ease;
	animation-fill-mode: both;	          
}
.animate4{
   	-webkit-animation-duration: .8s;
	-webkit-animation-delay: .8s;
	-webkit-animation-timing-function: ease;
	-webkit-animation-fill-mode: both;
	-moz-animation-duration: .8s;
	-moz-animation-delay: .8s;
	-moz-animation-timing-function: ease;
	-moz-animation-fill-mode: both;
	-ms-animation-duration: .8s;
	-ms-animation-delay: .8s;
	-ms-animation-timing-function: ease;
	-ms-animation-fill-mode: both;
	animation-duration: .8s;
	animation-delay: .8s;
	animation-timing-function: ease;
	animation-fill-mode: both;	          
}  
.bounceIn {
    -webkit-animation-name: bounceIn;
    -moz-animation-name: bounceIn;
    -ms-animation-name: bounceIn;
    animation-name: bounceIn;
}
.fadeInUp {
	-webkit-animation-name: fadeInUp;
    -moz-animation-name: fadeInUp;
    -ms-animation-name: fadeInUp;
    animation-name: fadeInUp;
}
@-webkit-keyframes bounceIn{
	0%{opacity:0;-webkit-transform:scale(.3)}
	50%{opacity:1;-webkit-transform:scale(1.05)}
	70%{-webkit-transform:scale(.9)}
	100%{-webkit-transform:scale(1)}
}
@-moz-keyframes bounceIn{
	0%{opacity:0;-moz-transform:scale(0.3)}
	50%{opacity:1;-moz-transform:scale(1.05)}
	70%{-moz-transform:scale(0.9)}
	100%{-moz-transform:scale(1)}
}
@-o-keyframes bounceIn{
	0%{opacity:0;-o-transform:scale(.3)}
	50%{opacity:1;-o-transform:scale(1.05)}
	70%{-o-transform:scale(.9)}
	100%{-o-transform:scale(1)}
}
@keyframes bounceIn{
	0%{opacity:0;transform:scale(.3)}
	50%{opacity:1;transform:scale(1.05)}
	70%{transform:scale(.9)}
	100%{transform:scale(1)}
}
@-webkit-keyframes fadeInUp{
	0%{opacity:0;-webkit-transform:translateY(20px)}
	100%{opacity:1;-webkit-transform:translateY(0)}
}
@-moz-keyframes fadeInUp{
	0%{opacity:0;-moz-transform:translateY(20px)}
	100%{opacity:1;-moz-transform:translateY(0)}
}
@-o-keyframes fadeInUp{
	0%{opacity:0;-o-transform:translateY(20px)}
	100%{opacity:1;-o-transform:translateY(0)}
}
@keyframes fadeInUp{
	0%{opacity:0;transform:translateY(20px)}
	100%{opacity:1;transform:translateY(0)}
}
.btn {
    display: inline-block;
    box-shadow: none;
    border-color: #BBB;
    margin-bottom: 5px;
    font-size: 13px;
	padding: 9px 15px;
	background: none repeat scroll 0% 0% #EEE;
	text-shadow: none;
	cursor:pointer;
}
.btn-primary{
    background: none repeat scroll 0% 0% #0866C6;
    border-color: #0A6BCE;
    color: #FFF;
}
.btn-primary:hover{
	background: none repeat scroll 0% 0% #04C;
}
.errordesc{
    font-size: 30px;
    margin-top:80px;
}
/*animate 404 end*/
.back{
    width: 200px;
    height: 44px;
    line-height: 44px;
    background: #e4126d;
    color: #fff;
    font-size: 18px;
    text-align:center;
    margin:100px auto 10px;
}
.back:hover{
    background: #ba0051;
    cursor:pointer;
}
.fadeInUp{
   	-webkit-animation-duration: .8s;
	-webkit-animation-delay: .8s;
	-webkit-animation-timing-function: ease;
	-webkit-animation-fill-mode: both;
	-moz-animation-duration: .8s;
	-moz-animation-delay: .8s;
	-moz-animation-timing-function: ease;
	-moz-animation-fill-mode: both;
	-ms-animation-duration: .8s;
	-ms-animation-delay: .8s;
	-ms-animation-timing-function: ease;
	-ms-animation-fill-mode: both;
	animation-duration: .8s;
	animation-delay: .8s;
	animation-timing-function: ease;
	animation-fill-mode: both;	          
	-webkit-animation-name: fadeInUp;
    -moz-animation-name: fadeInUp;
    -ms-animation-name: fadeInUp;
    animation-name: fadeInUp;
}
@-webkit-keyframes fadeInUp{
	0%{opacity:0;-webkit-transform:translateY(30px)}
	100%{opacity:1;-webkit-transform:translateY(0)}
}
@-moz-keyframes fadeInUp{
	0%{opacity:0;-moz-transform:translateY(30px)}
	100%{opacity:1;-moz-transform:translateY(0)}
}
@-o-keyframes fadeInUp{
	0%{opacity:0;-o-transform:translateY(30px)}
	100%{opacity:1;-o-transform:translateY(0)}
}
@keyframes fadeInUp{
	0%{opacity:0;transform:translateY(30px)}
	100%{opacity:1;transform:translateY(0)}
}
.code-input{
    outline:0;
    height: 46px;
    line-height: 46px;
    width: 360px;
    padding-left: 16px;
    border: 0;
    color: #999999;
    font-size:20px;
    border: 1px solid #d3d3d3;
}
.code-button{
    outline:0;
    height: 50px;
    line-height: 50px;
    width: 120px;
    border: 1px solid #e4126d;
    background: #e4126d;
    color:white;
    font-size:24px;
    position:relative;
    top:2px;
    cursor:pointer;
}
.code-bg{
    margin-top:50px;
}
.alert_content{
    margin-top:-15px;
    font-size: 18px;
}
</style>

</head>
<body>
	<div id="container" class="html-content">
		<div class="errortitle">
		   <span class="animate1 bounceIn">没</span>
		   <span class="animate2 bounceIn">有</span>
		   <span class="animate3 bounceIn">权</span>
		   <span class="animate4 bounceIn">限</span>
	    </div>
		<div class="errordesc fadeInUp">
		   <div>抱歉，您的授权码不对，没有权限访问当前系统！</div>
		   <div class="code-bg">
		      <input type="text" placeholder="请输入授权码" class="code-input" id="code"/>
		      <button class="code-button" type="button" id="submit">提交</button>
		   </div>
		</div>
	</div>
</body>
</html>
<script>
$(function(){
	$("#code").keypress(function(e){
    	if(e.which == 13){
    		validate_privilege();
    	}
	});
	
	$("#submit").click(function(){
		validate_privilege();
	});
	
	function validate_privilege(){
		var code = $("#code").val();
		if(typeof(code) == "undefined" || !code){
			var message = $("#code").attr('placeholder');
			$.alertMessage({"content":message});
			return false;
		}
		
		$.ajax({
			url: '/index.php/system/validate',
			type: 'POST',
			data: {
			    code: code,
			},
			dataType: 'json',
			cache: false,
			beforeSend: function() {
				
			},
			success: function(response){
				if(response.code == 0){
					var message = "验证成功，跳转中…！";
					$.alertMessage({"content":message});
					setTimeout('gotoUrl()', 1000);
				}
				else{
					var message = "验证失败！";
					$.alertMessage({"content":message});
				}
				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				var message = "验证出错！";
				$.alertMessage({"content":message});
			}
		});
	}
});

function gotoUrl(){
	var href = "/admin";
	location.href = href;
}
</script>