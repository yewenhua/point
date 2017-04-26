<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>二级密码设置</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">二级密码设置</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
        <div class="input-item-div">
           <span class="input-item-title">原密码</span>
           <input type="text" placeholder="请输入原密码" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">新密码</span>
           <input type="text" placeholder="请输入新密码" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">新密码确认</span>
           <input type="text" placeholder="请输入确认新密码" class="input-item"/>
        </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now">提交</div>
    </div>
    <script src="/media/js/member/secondpwd.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>