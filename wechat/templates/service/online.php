<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>在线报单</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">在线报单</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">用户编号</label></div>
                <div class="weui-cell__bd">89757</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">报单金额</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入报单金额"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">消费积分</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入消费积分"/>
                </div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now">提交</div>
    </div>
    
    <script src="/media/js/point/consumetransfer.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>