<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>支付失败</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
</head>
<body id="personal-page">
    <div class="page">
	    <div class="weui-msg">
	        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
	        <div class="weui-msg__text-area">
	            <h2 class="weui-msg__title">操作失败</h2>
	            <?php if(isset($message) && $message):?>
	               <p class="weui-msg__desc"><?php echo $message;?></p>
	            <?php endif;?>
	        </div>
	        <div>
	            <p class="weui-btn-area" style="margin-bottom:0px;">
	                <a href="/service/center" class="weui-btn weui-btn_default">首页</a>
	            </p>
	        </div>
	        <div class="weui-msg__extra-area">
	            <div class="weui-footer">
	                <p class="weui-footer__links">
	                    <a href="javascript:void(0);" class="weui-footer__link">微剑客云商</a>
	                </p>
	            </div>
	        </div>
	    </div>
	</div>
</body>
</html>