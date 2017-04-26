    <div class="category-search">
        <div class="weui-search-bar" id="searchBar">
            <div class="weui-search-bar__form">
                <div class="weui-search-bar__box">
                    <i class="weui-icon-search"></i>
                    <form method="post" id="form" action="#">
                        <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索" required/>
                    </form>
                    <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
                </div>
                <label class="weui-search-bar__label" id="searchText">
                    <i class="weui-icon-search"></i>
                    <span>搜索</span>
                </label>
            </div>
            <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchSure">搜索</a>
        </div>
    </div>

    <div class="alternate-content">
        <div class="alternate-content-detail" id="alternate-content-detail">
            <ul>
                <?php if(!empty($bannerlist)):?>
	                <?php foreach ($bannerlist as $key=>$item):?>
	                    <a href="<?php echo $item['link'];?>">
			               <li style="background: transparent url('<?php echo $item['url'];?>') no-repeat scroll center center; background-size:auto 100%;" data-order="<?php echo $key;?>"></li>
		                </a>
	                <?php endforeach;?>
	            <?php endif;?>
                <div class="clear"></div>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="count-area">
            <ul>
                <?php if(!empty($bannerlist)):?>
	                <?php foreach ($bannerlist as $key=>$item):?>
	                   <li class="<?php if($key == 0){echo 'active';}?>" data-order="<?php echo $key;?>"></li>
	                <?php endforeach;?>
	            <?php endif;?>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    
    <div class="menu-content">
        <ul>
            <li>
               <a href="/mall/goodslist?key=kpSV">
	               <img src="/media/img/cate_07.png"/>
	               <div class="menu-item-name">购物商城</div>
               </a>
            </li>
            <li>
               <a href="/mall/goodslist?key=kpSU">
	               <img src="/media/img/cate_10.png"/>
	               <div class="menu-item-name">积分商城</div>
               </a>
            </li>
            <li>
               <a href="/mall/goodslist?key=kpST">
	               <img src="/media/img/cate_13.png"/>
	               <div class="menu-item-name">兑换商城</div>
               </a>
            </li>
            <li>
               <a href="/member/center">
	               <img src="/media/img/cate_16.png"/>
	               <div class="menu-item-name">个人中心</div>
               </a>
            </li>
            <div class="clear"></div>
        </ul>
    </div>

    <div class="hot-recommend">热门推荐</div>
    <div class="weui-grids" id="list">
        <?php foreach($list['data'] as $item):?>
	        <a href="/mall/detail?key=<?php echo $item['key'];?>" class="weui-grid">
	            <div class="weui-grid__icon">
	                <img src="/backend/uploads/<?php echo $item['face'];?>" alt="">
	            </div>
	            <p class="weui-grid__label center-title"><?php echo $item['name'];?></p>
	            <p class="weui-grid__label center-price"><?php if($item['model'] == 1){echo '购物券'.$item['point_price'].'分';}elseif($item['model'] == 2){echo '消费积分'.$item['point_price'].'分';}else{echo '消费积分'.$item['point_price'].'分 + 现金'.$item['cash_price'].'元';}?></p>
	        </a>
        <?php endforeach;?>
    </div>
    <div class="load-more" style="display:<?php if($totalPage > 1){echo 'block';}else{echo 'none';}?>; text-align:center;">
       <div class="la-fire la-dark" style="margin:0 auto;">
	      <div></div>
	      <div></div>
	      <div></div>
	   </div>
    </div>
    <div class="list-bottom-last" style="display:<?php if(count($list['data']) == 10){echo 'none';}else{echo 'block';}?>;">我是有底线的</div>
    
    <script>
       var totalPage = <?php echo $totalPage;?>;
    </script>
    
    <script>
	    var url = '<?php echo $url;?>';
	    var site_name = '<?php echo $systemInfo['site_name'];?>';
	    
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
	                 title: '商城首页',
	                 desc: site_name,
	                 link: url,
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
	            	 title: '商城首页-' + site_name,
	                 link: url, // 分享链接
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