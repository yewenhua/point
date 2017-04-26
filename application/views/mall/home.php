    <style>
        html{
           font-size: 62.5%;
           background-color: #efefef;
        }
    </style>
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
    
    <div class="home-nav">
        <ul>
           <li>
              <a href="<?php echo base_url('mall/timebuy');?>">
	              <img src="/media/img/new_home_03.png"/>
	              <div class="home-nav-title">限时抢购</div>
              </a>
           </li>
           <li>
              <a href="<?php echo base_url('mall/goodslist?key=kpSYZg==');?>">
	              <img src="/media/img/new_home_11.png"/>
	              <div class="home-nav-title">购物券区</div>
              </a>
           </li>
           <li>
              <a href="<?php echo base_url('mall/goodslist?key=all');?>">
	              <img src="/media/img/new_home_06.png"/>
	              <div class="home-nav-title">新品推荐</div>
              </a>
           </li>
           <li>
              <a href="/article/news">
	              <img src="/media/img/new_home_08.png"/>
	              <div class="home-nav-title">最新公告</div>
              </a>
           </li>
        </ul>
        <div class="clear"></div>
    </div>
    
    <div class="home-reacommend">
        <ul>
            <?php foreach($recommend_goods as $item):?>
            <li>
                <div>
                   <a href="/mall/goodslist?key=<?php echo $item['key']; ?>">
                      <img src="<?php echo $item['face_img']; ?>" class="face"/>
                   </a>
                </div>
                <div class="home-reacommend-detail">
                   <ul>
                      <?php foreach($item['data'] as $detail):?>
                      <li>
                         <a href="/mall/detail?key=<?php echo $detail['key']; ?>">
	                         <div>
	                            <img src="<?php echo '/backend/uploads/'.$detail['face']; ?>" class="detail-face"/>
	                         </div>
	                         <div class="goods-desc-home"><?php echo $detail['name']; ?></div>
	                         <div class="goods-desc-home goods-price"><?php if($detail['model'] == 1){echo '购物券'.$detail['point_price'].'分';}elseif($detail['model'] == 2){echo '消费积分'.$detail['point_price'].'分';}else{echo '消费积分'.$detail['point_price'].'分 + 现金'.round($detail['cash_price'], 2).'元';}?></div>
	                     </a>
                      </li>
                      <?php endforeach;?>
                      <div class="clear"></div>
                   </ul>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
    
    <script>
	    (function(doc, win) {
	        var docEl = doc.documentElement,
	            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	            recalc = function() {
	                var clientWidth = docEl.clientWidth;
	                if (!clientWidth) return;
	                if(clientWidth == 375){
	                    docEl.style.fontSize = '100px';
	                }else{
	                    docEl.style.fontSize = 100 * (clientWidth / 375) + 'px';
	                }
	            };
	        if (!doc.addEventListener) return;
	        win.addEventListener(resizeEvt, recalc, false);
	        doc.addEventListener('DOMContentLoaded', recalc, false);
	    })(document, window);
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