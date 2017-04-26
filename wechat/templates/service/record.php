<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>报单记录</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">报单记录</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <p class="wait-point-value">20000</p>
       <p class="wait-point-name">余额</p>
    </div>
    
    <?php for($i=0; $i<10; $i++):?>
    <div class="weui-panel">
       <div class="weui-panel__bd">
           <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
               <div class="left">编号：17091952061</div>
               <div class="right">2016-12-09 10:12:56</div>
               <div class="clear"></div>
           </div>
           <div class="weui-media-box weui-media-box_text record-item">
               <div class="left">
                  <div class="value">报单金额<span class="money">&yen; 1000</span></div>
               </div>
               <div class="right">
                  <div class="name">报单人：周星驰</div>
               </div>
               <div class="clear"></div>
           </div>
       </div>
    </div>
    <?php endfor;?>
    <div class="list-bottom-last">我是有底线的</div>
    
    <script src="/media/js/service/record.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>