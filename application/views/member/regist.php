<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>注册</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
   <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="title">注册</div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">推荐人手机号</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="parent_mobile" value="<?php echo $mobile;?>" placeholder="请输入推荐人手机号码"/>
                </div>
            </div>
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd"><label class="weui-label">随机验证码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="rand_code" placeholder="请输入随机验证码"/>
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
                    <input class="weui-input" type="number" id="mobile" placeholder="请输入手机号">
                </div>
                <div class="weui-cell__ft" id="get_mobile_code_div">
                    <a href="javascript:;" class="weui-vcode-btn" id="get_mobile_code">手机验证码</a>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">手机验证码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="mobile_code" placeholder="请输入手机验证码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">密码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="password" placeholder="请输入密码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">密码确认</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="re_password" placeholder="请再次输入密码"/>
                </div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">注册</div>
    </div>
    <script src="/media/js/member/regist.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>

<script type='text/javascript'>
    var key = '<?php echo $key;?>';
    var url = 'http://www.ziyivip.com/member/regist';
    
    if(typeof WeixinJSBridge == "undefined"){
        if(document.addEventListener){
             document.addEventListener('WeixinJSBridgeReady', shareAppMessage, false);
        } 
        else if(document.attachEvent){
             document.attachEvent('WeixinJSBridgeReady', shareAppMessage);
             document.attachEvent('onWeixinJSBridgeReady', shareAppMessage);
        }
    } 
    else{
        shareAppMessage();
    }
         
    function shareAppMessage(){
        wx.config({
                 debug:false,
                 signature: '<?php echo $signature;?>',
                 appId: '<?php echo $appId;?>',
                 timestamp: '<?php echo $timeStamp;?>',
                 nonceStr: '<?php echo $nonceStr;?>',
                 jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline'],
		});
        wx.ready(function(){
            wx.onMenuShareAppMessage({
                 title: '久赢五洲会员注册',
                 desc: '一次消费，多次收益',
                 link: url + '?key=' + key,
                 imgUrl: '<?php echo $share_img;?>',
                 type: '', // 分享类型,music、video或link，不填默认为link
                 dataUrl: '', //如果type是music或video，则要提供数据链接，默认为空
                 success: function(){
            		  // 用户确认分享后执行的回调函数
                      console.log('SUCCESS');
                 },
                 cancel: function(){
                      // 用户取消分享后执行的回调函数
                      console.log('FAIL');
                 }
            });

            wx.onMenuShareTimeline({
            	 title: '久赢五洲会员注册',
                 desc: '一次消费，多次收益',
                 link: url + '?key=' + key, // 分享链接
                 imgUrl: '<?php echo $share_img;?>', // 分享图标
                 success: function(){
                	 // 用户确认分享后执行的回调函数
                	 console.log('SUCCESS');
                 },
                 cancel: function(){
                     // 用户取消分享后执行的回调函数
                     console.log('FAIL');
                 }
            });
        });
    }
</script>