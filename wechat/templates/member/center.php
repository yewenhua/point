<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>用户中心</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="member-center-page">
   <div class="page__bd">
      <div class="weui-panel">
            <div class="weui-panel__hd relative persion-header">
                <div class="left">
                   <img src="/media/img/wechat_06.png"/>
                   <div class="level">VIP</div>
                </div>
                <div class="middle">
                   <a class="link-a" href="/member/personal">
	                   <p class="mobile">17091952061</p>
	                   <p class="name">唐僧</p>
                   </a>
                </div>
                <div class="right">
                   <img src="/media/img/wechat_03.png"/>
                   <div class="level">绑定银行卡</div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="weui-panel__bd relative persion-header-name">
                <div class="weui-media-box weui-media-box_small-appmsg">
                    <div class="weui-cells">
                        <a class="weui-cell weui-cell_access" href="/member/personal">
                            <div class="left">推荐人</div>
                            <div class="right">（17091952061）郎朗</div>
                            <div class="clear"></div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-panel">
            <a class="link-a" href="/point/consume">
	            <p class="consume-point-value">20000</p>
	            <p class="consume-point-name">消费积分</p>
            </a>
        </div>
        <div class="weui-panel">
            <div class="weui-grids">
		        <a href="/point/exchange" class="weui-grid">
		            <p class="grid-point-value">1000</p>
		            <p class="grid-point-name">购物券</p>
		        </a>
		        <a href="/point/share" class="weui-grid">
		            <p class="grid-point-value">2000</p>
		            <p class="grid-point-name">分享积分</p>
		        </a>
		        <a href="/point/useable" class="weui-grid">
		            <p class="grid-point-value">3000</p>
		            <p class="grid-point-name">可用积分</p>
		        </a>
		        <a href="/point/wait" class="weui-grid">
		            <p class="grid-point-value">50000</p>
		            <p class="grid-point-name">待用积分</p>
		        </a>
		        <a href="/point/repeat" class="weui-grid">
		            <div class="grid-point-img"><img src="/media/img/wechat_14.png"/></div>
		            <p class="grid-point-name">我要复投</p>
		        </a>
		        <a href="/point/consumetransfer" class="weui-grid">
		            <div class="grid-point-img"><img src="/media/img/wechat_11.png"/></div>
		            <p class="grid-point-name">积分转让</p>
		        </a>
		    </div>
        </div>
        
        <div class="weui-panel cneter-footer-item">
            <div class="weui-panel__bd">
                <div class="weui-media-box weui-media-box_small-appmsg">
                    <div class="weui-cells">
                        <a class="weui-cell weui-cell_access first" href="/member/personal">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>个人资料</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access second" href="/member/chpwd">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>密码设置</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access third" href="/member/secondpwd">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>二级密码</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access fouth" href="/member/regist">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>邀请注册</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bottom-btn-area">
            <a href="/member/logout">
               <div class="logout-now">退出登录</div>
            </a>
        </div>
   </div>
   
      
</body>
</html>