
    <div class="list-header">
      <div class="left goback">返回</div>
      <div class="title">商品详情</div>
      <div class="right"></div>
    </div>
    <div class="alternate-content">
        <div class="alternate-content-detail" id="alternate-content-detail">
            <ul>
                <?php foreach($imglist as $key=>$item):?>
                <li style="background: transparent url('<?php echo '/backend/uploads/'.$item['file']; ?>') no-repeat scroll center center; background-size:100% 100%;" data-order="<?php echo $key;?>"></li>
                <?php endforeach;?>
                <div class="clear"></div>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="count-area">
            <ul>
                <?php foreach($imglist as $key=>$item):?>
                <li class="<?php if($key == 0){echo 'active';}?>" data-order="<?php echo $key;?>"></li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    <?php if($goods['is_time_buy'] == 1 && $goods['buy_time'] && (strtotime($goods['buy_time']) > time())):?>
    <div class="buy_time_detail_page">
        <div class="title">距开始仅剩</div>
        <div class="time">
            <span class="point day">00</span>
            <span class="maohao">：</span>
            <span class="point hour">00</span>
            <span class="maohao">：</span>
            <span class="point minute">00</span>
            <span class="maohao">：</span>
            <span class="point second">00</span>
        </div>
    </div>
    <?php endif;?>
    
    <div class="goods-title-desc relative">
        <div class="goods-title"><?php echo $goods['name'];?></div>
        <!--<div class="goods-desc">加厚羊绒</div>-->
        <div class="goods-price">商品价格：<?php if($goods['model'] == 1){echo '购物券'.$goods['point_price'].'分';}elseif($goods['model'] == 2){echo '消费积分'.$goods['point_price'].'分';}else{echo '消费积分'.$goods['point_price'].'分 + 现金'.round($goods['cash_price'], 2).'元';}?></div>
        <?php if($goods['market_price'] > 0):?>
        <p class="market_price">市场价格：<font><?php echo $goods['market_price'];?>元</font></p>
        <?php endif;?>
        <?php if($goods['limit_buy'] > 0):?>
        <p class="limit_buy">限购：<?php echo $goods['limit_buy'];?>件/位</p>
        <?php endif;?>
        <p class="sales_volume">销量：<?php echo $goods['sales_volume'];?>件</p>
        <?php if($goods['is_share'] == 1 && $goods['share_price'] > 0):?>
	        <div class="share-content">
	           <img src="/media/img/wallet_03.png"/>
	           <span>我要分享</span>
	        </div>
        <?php endif;?>
    </div>
    
    <?php if((isset($goods['options']['is_option']) && $goods['options']['is_option'] == 0) || !isset($goods['options']['is_option'])):?>
        <div id="num-change" total="<?php echo $goods['total'];?>"></div>
    <?php else:?>
        <div id="num-change" total="1" style="display:none;"></div>
        <div class="goods-size-select">
	        <span id="out-guige-sel">请选择  <?php foreach($goods['options']['options'] as $key=>$item){echo $item['title'].' ';}?></span>
	    </div>
        <br/>
    <?php endif;?>
    <div class="buy-content">
        <form action="/index.php/orders/preview" method="post" id="submit_form">
           <input type="hidden" name="buy_num" id="buy_num" value="1"/>
           <input type="hidden" name="buy_goods_key" id="buy_goods_key" value="<?php echo $goods['key'];?>"/>
           <input type="hidden" name="buy_goods_attr" id="buy_goods_attr" value=""/>
           <?php if($goods['total'] > 0):?>
               <div class="buy-now" id="submit">立即购买</div>
           <?php else:?>
               <div class="buy-now" id="submit-two">立即购买</div>
           <?php endif;?>
        </form>
    </div>
    <br/>
    <br/>
    
    <div class="goods-detail">
        <div class="goods-detail-title-bg">
           <div class="goods-detail-title">商品详情</div>
        </div>
        <div><?php echo $goods['description']; ?></div>
    </div>
    
    <!--BEGIN actionSheet-->
	<div class="guige-select">
        <div class="weui-mask" id="iosMask" style="display: none"></div>
        <div class="weui-actionsheet" id="iosActionsheet">
            <div class="weui-actionsheet__menu">
                <div class="guige-actionsheet-top">
                    <div class="left">
                        <img id="guige-img" style="display:none;" src="<?php echo '/backend/uploads/'.$goods['face']; ?>"/>
                    </div>
                    <div class="right">
                        <div class="goods-price" id="goods_price">商品价格：<?php if($goods['model'] == 1){echo '购物券'.$goods['point_price'].'分';}elseif($goods['model'] == 2){echo '消费积分'.$goods['point_price'].'分';}else{echo '消费积分'.$goods['point_price'].'分 + 现金'.round($goods['cash_price'], 2).'元';}?></div>
				        <?php if($goods['is_share'] == 1 && $goods['share_price'] > 0):?>
				        <div class="goods-price" id="share_price" style="display:none;">分享价格：<?php echo $goods['share_price'];?> 元</div>
				        <?php endif;?>
				        <p class="guige-sel-title" id="guige_kucun" style="display:none;">商品库存：<font class="actionsheet-kucun"><?php echo $goods['total'];?></font></p>
				        <p class="guige-sel-title" id="share_kucun" style="display:none; margin-top:10px;">商品库存：<font><?php echo $goods['total'];?></font></p>
				        <p class="guige-sel-title guige-selected-title">请选择规格选项</p>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php if($goods['options']['is_option'] == 1):?>
                    <?php foreach($goods['options']['options'] as $key=>$item):?>
		                <div class="guige-actionsheet-bottom category" style="display:none;">
		                    <p class="guige-child-title"><?php echo $item['title'];?></p>
		                    <?php foreach($item['child'] as $child):?>
		                        <span class="sku guige-child-item" attr_id="<?php echo $child['attr_id'];?>"><?php echo $child['title'];?></span>
		                    <?php endforeach;?>
		                </div>
	                <?php endforeach;?>
                <?php endif;?>
                
                
                <div class="goods-num-content" id="num-select-normal" style="padding:15px 15px 20px 15px; display:none;">
	                <span class="goods-num-item-desc">数量：</span>
	                <span class="goods-num-item decrease disabled">-</span>
	                <span class="goods-num-item" style="padding: 0px;"><input type="number" value="1" data-total="<?php echo $goods['total'];?>" id="num-second" class="numSelInputStyle"/></span>
	                <span class="goods-num-item increase last">+</span>
	                <div class="clear"></div>
	            </div> 
	            
	            <div class="goods-num-content" id="num-select-share" style="padding:15px 15px 20px 15px; display:none;">
	                <span class="goods-num-item-desc">分享数量：</span>
	                <span class="goods-num-item decrease-share disabled">-</span>
	                <span class="goods-num-item" style="padding: 0px;"><input type="number" value="1" data-total="<?php echo $goods['total'];?>" id="num-share" class="numSelInputStyle"/></span>
	                <span class="goods-num-item increase-share last">+</span>
	                <div class="clear"></div>
	            </div> 
            </div>
            <div class="weui-actionsheet__action" style="margin-top:0px;">
                <div class="weui-actionsheet__cell" id="iosActionsheetCancel">立即购买</div>
                <?php if($goods['is_share'] == 1 && $goods['share_price'] > 0):?>
                <div class="weui-actionsheet__cell" style="display:none;" id="submitShareAction">我要分享</div>
                <?php endif;?>
            </div>
        </div>
    </div>
	<!--END actionSheet-->
	
	<div class="share-content" style="display:none;">
        <form action="/index.php/mall/share_preview" method="post" id="share_form">
           <input type="hidden" name="share_num" id="share_num" value="1"/>
           <input type="hidden" name="share_goods_key" id="share_goods_key" value="<?php echo $goods['key'];?>"/>
        </form>
    </div>

    <script type="text/babel" src="/media/js/mall/num-change.js"></script>
    
    <script>
        var already_buy = <?php echo intval($already_buy);?>;
        var limit_buy = <?php echo $goods['limit_buy'];?>;
        var level = <?php echo $level;?>;
        var options = <?php echo json_encode($goods['options']);?>;
        var defaultTotal = <?php echo $goods['total'];?>;
        var skuData = <?php echo json_encode($goods['sku']);?>;
    </script>
    
    <script>
        var is_time_buy = <?php echo $goods['is_time_buy'];?>;
        var buy_time_seconds = <?php if($goods['is_time_buy'] == 1 && $goods['buy_time'] && (strtotime($goods['buy_time']) > time())){echo strtotime($goods['buy_time']);}else{echo 0;}?>;
	    var key = '<?php echo $goods['key'];?>';
	    var url = 'http://www.ziyivip.com/mall/detail';
	    var desc = '<?php if($goods['model'] == 1){echo '购物券'.$goods['point_price'].'分';}elseif($goods['model'] == 2){echo '消费积分'.$goods['point_price'].'分';}else{echo '消费积分'.$goods['point_price'].'分 + 现金'.round($goods['cash_price'], 2).'元';}?>';
	    
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
	                 title: '<?php echo $goods['name'];?>',
	                 desc: desc,
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
	            	 title: '<?php echo $goods['name'];?>',
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
