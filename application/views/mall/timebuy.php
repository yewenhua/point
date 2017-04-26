
   <div id="box">
	   <div class="list-content timebuy" style="margin-top:0px;">
	      <div class="weui-grids" id="list" v-show="hasData">
		      <div class="weui-panel weui-panel_access" v-for="item in arr">
		          <div class="weui-panel__bd">
		              <a :href="item.link_url" v-cloak class="weui-media-box weui-media-box_appmsg">
		                  <div class="weui-media-box__hd">
		                      <img class="weui-media-box__thumb" v-cloak :src="item.img_url" alt="">
		                  </div>
		                  <div class="weui-media-box__bd relative">
		                      <h4 class="weui-media-box__title" v-cloak>{{item.name}}</h4>
		                      <p class="weui-media-box__desc" v-cloak>{{item.exchange}}</p>
		                      <div class="timebuy-btn-content" v-cloak v-if="!item.sale_over">
		                          <span class="buy-btn" v-cloak v-if="item.is_over">马上抢购</span>
		                          <span class="buy-btn over" v-cloak v-if="!item.is_over">马上抢购</span>
		                      </div>
		                      <div class="timebuy-btn-content" v-cloak v-if="item.sale_over">
		                          <span class="buy-btn sale_over">马上抢购</span>
		                      </div>
		                      <div class="timebuy-bottom">
		                          <div class="left relative">
		                             <div class="rect">
		                                  <div class="rect-value" v-cloak :style="{width: item.percent}"></div>
		                              </div>
		                              <div class="max">库存：<span v-cloak>{{item.kucun}}</span></div>
		                              <div class="now">已卖：<span v-cloak>{{item.sales_volume}}</span></div>
		                          </div>
		                          <div class="right relative">
		                              <div class="time-cont" v-cloak v-if="!item.is_over">
		                                  <div class="time-title">距开始还剩</div>
		                                  <div class="time-detail">
		                                     <span class="point day" v-cloak>{{item.day_html}}</span>
		                                     <span class="maohao">:</span>
		                                     <span class="point hour" v-cloak>{{item.hour_html}}</span>
		                                     <span class="maohao">:</span>
		                                     <span class="point minute" v-cloak>{{item.minute_html}}</span>
		                                     <span class="maohao">:</span>
		                                     <span class="point second" v-cloak>{{item.second_html}}</span>
		                                  </div>
		                              </div>
		                              <div class="time-title-over" v-cloak v-if="item.is_over">{{item.sale_over ? '已结束' : '已开始'}}</div>
		                           </div>
		                           <div class="clear"></div>
		                        </div>
		                    </div>
		                </a>
		            </div>
		       </div>
		  </div>
	   </div>
	   
	   <div class="load-more" style="text-align:center;" v-cloak v-show="loadMore">
	      <div class="la-fire la-dark" style="margin:0 auto;">
		      <div></div>
		      <div></div>
		      <div></div>
		  </div>
	   </div>
	   <div class="list-bottom-last" v-cloak v-show="isBottom">我是有底线的</div>
	   <div class="nodata" v-cloak v-show="!hasData">没有您要找的商品</div>
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
	                 title: '限时抢购',
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
	            	 title: '限时抢购' + '-' + site_name,
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
