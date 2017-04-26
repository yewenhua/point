
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
   <div class="list-header-sort">
      <ul>
         <li class="active" data-sort="1"><span>默认</span></li>
         <li data-sort="2"><span>销量</span></li>
         <li data-sort="3"><span>价格</span></li>
         <div class="clear"></div>
      </ul>
   </div>
   
   <div class="list-content">
      <div class="weui-grids" id="list">
	        
	  </div>
   </div>
   <input type="hidden" id="path" value="<?php echo $path;?>"/>
   
   <div class="load-more" style="display:none; text-align:center;">
      <div class="la-fire la-dark" style="margin:0 auto;">
	      <div></div>
	      <div></div>
	      <div></div>
	  </div>
   </div>
   <div class="list-bottom-last" style="display:none;">我是有底线的</div>
   
   <script>
      var wd = '<?php echo $wd;?>';
   </script>
   
   <script>
	    var url = '<?php echo $url;?>';
	    var category = '<?php echo $category;?>';
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
	                 title: '商品分类-' + category,
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
	            	 title: '商品分类-' + category + '-' + site_name,
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
