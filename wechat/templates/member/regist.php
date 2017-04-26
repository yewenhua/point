<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>注册</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">注册</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">推荐人ID</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入推荐人ID"/>
                </div>
            </div>
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd"><label class="weui-label">验证码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入验证码"/>
                </div>
                <div class="weui-cell__ft">
                    <span class="captcha-img"></span>
                </div>
            </div>
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd">
                    <label class="weui-label">手机号</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="tel" placeholder="请输入手机号">
                </div>
                <div class="weui-cell__ft">
                    <a href="javascript:;" class="weui-vcode-btn">获取验证码</a>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">手机验证码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入手机验证码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">密码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入密码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">密码确认</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请再次输入密码"/>
                </div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now">注册</div>
    </div>
    <script src="/media/js/member/regist.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>