
   <div class="page__bd">
      <div class="weui-panel">
            <div class="weui-panel__hd relative persion-header">
                <div class="left">
                   <?php if($userInfo['level'] == 0):?>
                       <img src="/media/img/VIP0.png"/>
                   <?php elseif($userInfo['level'] == 1 || $userInfo['level'] == 2):?>
                       <img src="/media/img/VIP.png"/>
                   <?php elseif($userInfo['level'] == 11 || $userInfo['level'] == 12 || $userInfo['level'] == 13):?>
                       <img src="/media/img/service_03.png"/>
                   <?php else:?>
                       <img src="/media/img/VIP0.png"/>
                   <?php endif;?>
                   <div class="level"><?php if($userInfo['level'] == 11){echo '初级服务中心';}elseif($userInfo['level'] == 12){echo '中级服务中心';}elseif($userInfo['level'] == 13){echo '高级服务中心';}else{echo '--';}?></div>
                </div>
                <div class="middle">
                   <a class="link-a" href="/service/personal">
	                   <p class="mobile"><?php echo $userInfo['mobile'];?></p>
	                   <p class="name"><?php if($userInfo['name']){echo $userInfo['name'];}else{echo '--';}?></p>
                   </a>
                </div>
                <div class="right">
                   <a class="link-a" href="/service/bank">
	                   <img src="/media/img/wechat_03.png"/>
	                   <div class="level">绑定银行卡</div>
                   </a>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="weui-panel">
            <a class="link-a" href="/member/wallet/service">
	            <p class="consume-point-value"><?php echo round($userInfo['commision']);?><span class="cash_unit">元</span></p>
	            <p class="consume-point-name">钱包</p>
            </a>
        </div>
        <div class="weui-panel">
            <div class="weui-panel__bd service-center">
		        <div class="left">
		           <a class="link-a" href="/point/consume/service">
			           <div class="value">
			              <div class="value"><?php echo $userInfo['consume_point'];?></div>
			              <div class="title">消费积分</div>
			           </div>
		           </a>
		        </div>
		        <div class="right">
		           <a class="link-a" href="/point/share/service">
			           <div class="value">
			              <div class="value"><?php echo $userInfo['share_point'];?></div>
			              <div class="title">分享积分</div>
			           </div>
		           </a>
		        </div>
		        <div class="clear"></div>
		    </div>
        </div>
        
        <div class="weui-panel cneter-footer-item">
            <div class="weui-panel__bd">
                <div class="weui-media-box weui-media-box_small-appmsg">
                    <div class="weui-cells">
                        <a class="weui-cell weui-cell_access first" href="/service/online">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>在线报单</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/service/record">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>报单记录</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access third" href="/service/chpwd">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>密码设置</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/service/secondpwd">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>二级密码</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/service/personal">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>个人资料</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bottom-btn-area">
            <a href="/service/logout">
               <div class="logout-now">退出登录</div>
            </a>
        </div>
   </div>
