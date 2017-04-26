<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>页面不存在</title>
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
</style>
</head>
<body>
	<div id="container" class="html-content">
		<div class="errortitle">
		   <span class="animate1 bounceIn">4</span>
		   <span class="animate2 bounceIn">0</span>
		   <span class="animate3 bounceIn">4</span>
	    </div>
		<div class="errordesc">您要找的页面不存在！</div>
	</div>
</body>
</html>