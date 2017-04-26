   <script>
       var is_exchange_pwd_exist = '<?php echo $is_exchange_pwd_exist;?>';
   </script>
   
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
                   <div class="level"><?php if($userInfo['level'] == 0){echo '普通会员';}elseif($userInfo['level'] == 1){echo 'VIP';}elseif($userInfo['level'] == 2){echo 'VIP1';}elseif($userInfo['level'] == 11){echo '初级服务中心';}elseif($userInfo['level'] == 12){echo '中级服务中心';}elseif($userInfo['level'] == 13){echo '高级服务中心';}else{echo '--';}?></div>
                </div>
                <div class="middle">
                   <a class="link-a" href="/member/personal">
	                   <p class="mobile"><?php echo $userInfo['mobile'];?></p>
	                   <p class="name"><?php if($userInfo['name']){echo $userInfo['name'];}else{echo '--';}?></p>
                   </a>
                </div>
                <div class="right">
                   <a class="link-a" href="/member/banklist">
	                   <img src="/media/img/wechat_03.png"/>
	                   <div class="level">绑定银行卡</div>
                   </a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="weui-panel__bd relative persion-header-name">
                <div class="weui-media-box weui-media-box_small-appmsg">
                    <div class="weui-cells">
                        <div class="weui-cell weui-cell_access">
                            <div class="left">推荐人</div>
                            <div class="right"><?php if($userInfo['is_system'] == 0){echo '（<a class="telephone" href="tel://'.$userInfo['parent_mobile'].'">'.$userInfo['parent_mobile'].'</a>）';}?><?php echo $userInfo['parent_name'];?></div>
                            <div class="clear"></div>
                        </div>
                        <?php if(!empty($service)):?>
	                        <div class="weui-cell weui-cell_access service_mobile">
	                            <div class="left">服务中心</div>
	                            <div class="right">（<a class="telephone" href="tel://<?php echo $service['mobile']; ?>"><?php echo $service['mobile']; ?></a>）<?php echo $service['name']; ?></div>
	                            <div class="clear"></div>
	                        </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-panel">
            <a class="link-a" href="/point/consume/member">
	            <p class="consume-point-value"><?php echo $userInfo['consume_point'];?></p>
	            <p class="consume-point-name">消费积分</p>
            </a>
        </div>
        <div class="weui-panel">
            <div class="weui-grids">
		        <a href="/point/exchange/member" class="weui-grid">
		            <p class="grid-point-value"><?php echo $userInfo['exchange_point'];?></p>
		            <p class="grid-point-name">购物券</p>
		        </a>
		        <a href="/point/share/member" class="weui-grid">
		            <p class="grid-point-value"><?php echo $userInfo['share_point'];?></p>
		            <p class="grid-point-name">分享积分</p>
		        </a>
		        <a href="/point/useable/member" class="weui-grid">
		            <p class="grid-point-value"><?php echo $userInfo['useable_point'];?></p>
		            <p class="grid-point-name">可用积分</p>
		        </a>
		        <a href="/point/wait/member" class="weui-grid">
		            <p class="grid-point-value"><?php echo $userInfo['wait_point'];?></p>
		            <p class="grid-point-name">待用积分</p>
		        </a>
		        <a href="/member/wallet/member" class="weui-grid">
		            <p class="grid-point-value"><?php echo round($userInfo['commision'], 2);?><span class="cash_unit">元</span></p>
		            <p class="grid-point-name">钱包</p>
		        </a>
		        <a href="/member/student" class="weui-grid">
		            <p class="grid-point-value"><?php echo $directChildrenNum;?></p>
		            <p class="grid-point-name">我的会员</p>
		        </a>
		        <a href="/point/repeatlist" class="weui-grid">
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
                        <a class="weui-cell weui-cell_access first" href="/mall/sharelist">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>我的分享</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/member/personal">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>个人资料</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/member/chpwd">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>密码设置</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/member/secondpwd">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>二级密码</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/member/regist?key=<?php echo $key;?>">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>邀请注册</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <?php if($userInfo['level'] >= 11):?>
                        <a class="weui-cell weui-cell_access" href="javascript:;" id="poster">
                            <div class="weui-cell__bd weui-cell_primary">
                                <p>邀请海报</p>
                            </div>
                            <span class="weui-cell__ft"></span>
                        </a>
                        <?php endif;?>
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
   
   