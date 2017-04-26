<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
   <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>服务中心登录</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
   
   <script type="text/javascript" src="/media/vendor/react/react.js"></script>
   <script type="text/javascript" src="/media/vendor/react/react-dom.js"></script>
   <script type="text/javascript" src="/media/vendor/react/browser.min.js"></script>
</head>
<body id="loginpage">
    <div class="list-header">
       <div class="title">服务中心登录</div>
    </div>
    <div id="login-content-top"></div>
    <div id="login-content">
    
    </div>
    <a href="/member/findpwd/service"><div style="margin:12px auto; text-align:right;font-size:13px; color:#333; padding-right:15px;">忘记密码？</div></a>
    
    <script type="text/babel" src="/media/js/service/login.js"></script>
</body>
</html>