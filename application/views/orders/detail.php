
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">订单详情</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd">
               <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
	               <div class="order-detail-title">订单详情</div>
	           </div>
               <div class="weui-media-box order-detail-item">
                    <p>订单号：<?php echo $order['order_id'];?></p>
                    <p>交易号：<?php if($order['pay_no']){echo $order['pay_no'];}else{echo '--';}?></p>
                    <div class="relative">
                        <p>订单状态：<?php if($order['status'] == 0){echo '未支付';}elseif($order['status'] == 1){echo '已支付';}elseif($order['status'] == 2){echo '已发货';}elseif($order['status'] == 3){echo '已完成';}elseif($order['status'] == 4){echo '已关闭';}elseif($order['status'] == 5){echo '退款中';}elseif($order['status'] == 6){echo '已退款';}elseif($order['status'] == 7){echo '待退款';}else{echo '--';}?></p>
                        <?php if($order['status'] == 0):?>
                           <a href="/orders/pay?key=<?php echo $key;?>"><span class="gotopay">去支付</span></a>
                        <?php elseif($order['status'] == 1):?>
                           <a href="javascript:void(0);" id="showIOSActionSheet"><span class="gotopay">退款</span></a>
                        <?php elseif($order['status'] == 2):?>
                           <a href="javascript:void(0);" id="sureOrder"><span class="gotosure">确认订单</span></a>
                        <?php endif;?>
                    </div>
                    <p>运费：<?php if($order['logistic_fee'] == 0){echo '免邮';}else{echo $order['logistic_fee'].'元';}; ?></p>
                    <p>下单时间：<?php echo $order['created_at'];?></p>
                    <p>支付时间：<?php if($order['pay_time']){echo $order['pay_time'];}else{echo '--';}?></p>
               </div>
        </div>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd">
               <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
	               <div class="order-detail-title">商品信息</div>
	           </div>
               <a href="<?php echo $order['url'];?>" class="weui-media-box weui-media-box_appmsg review-item">
                    <div class="weui-media-box__hd">
                        <img class="weui-media-box__thumb" src="/backend/uploads/<?php echo $order['goods_face'];?>" alt="">
                    </div>
                    <div class="weui-media-box__bd">
                        <h4 class="weui-media-box__title"><?php echo $order['goods_name'];?></h4>
                        <p class="weui-media-box__desc">数量：<?php echo $order['num'];?><?php if($order['size_op']){echo '&nbsp;&nbsp;'.$order['size_op'];}?></p>
                        <div class="review-price">
                            <?php 
                               if($order['share_id'] == 0){
	                               if($order['goods_model'] == 1){
	                               	   echo '购物券 '.$order['point_price'].'分';
	                               }
	                               elseif($order['goods_model'] == 2){
	                               	   echo '消费积分 '.$order['point_price'].'分';
	                               }
	                               elseif($order['goods_model'] == 3){
	                               	   echo '消费积分 '.$order['point_price'].'分 + 现金 &yen;'.$order['cash_price'].'元';
	                               }
                               }
                               else{
                               	   echo '分享价'.$order['cash_price'].'元';
                               }
                            ?>
                        </div>
                    </div>
               </a>
        </div>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd">
               <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
	               <div class="order-detail-title">收货人信息</div>
	           </div>
               <div class="weui-media-box order-detail-item">
                    <p>收货人：<?php echo $address['userName'];?></p>
                    <p>手机：<?php echo $address['telNumber'];?></p>
                    <p>地址：<?php echo $address['provinceName'].$address['cityName'].$address['countryName'].$address['detailInfo'];?></p>
                    <?php if($order['status'] == 2 || $order['status'] == 3):?>
                    <p>快递公司：<?php echo $address['logisticName'];?></p>
                    <p>物流单号：<?php echo $address['logisticNo'];?></p>
                    <?php endif;?>
               </div>
        </div>
    </div>
    
    <?php if($order['status'] == 2 || $order['status'] == 3):?>
	    <div class="weui-panel">
	       <div class="weui-panel__bd">
	               <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
		               <div class="order-detail-title">物流详情</div>
		           </div>
	               <div class="weui-media-box order-detail-item">
	                    <?php if(!empty($info)):?>
	                        <ul class="logistic-detail">
		                        <?php foreach($info as $item):?>
	                               <li>
	                                  <p class="station"><?php echo $item['station'];?></p>
	                                  <p class="time"><?php echo $item['time'];?></p>
	                               </li>
		                        <?php endforeach;?>
	                        </ul>
	                    <?php else:?>
	                        <p style="color:#bdbdbd;">暂未查到物流信息</p>
	                    <?php endif;?>
	               </div>
	        </div>
	    </div>
    <?php endif;?>
    
    <!--BEGIN actionSheet-->
	<div>
        <div class="weui-mask" id="iosMask" style="display: none"></div>
        <div class="weui-actionsheet" id="iosActionsheet">
            <div class="weui-actionsheet__menu" id="refund-item" style="display: none">
                <div class="sure_refund_title">确定要退款吗？</div>
                <div class="weui-actionsheet__cell sure_refund_btn">确定</div>
            </div>
            <div class="weui-actionsheet__menu" id="sure-item" style="display: none">
                <div class="sure_refund_title">确定要确认订单吗？</div>
                <div class="weui-actionsheet__cell sure_order_btn">确定</div>
            </div>
            <div class="weui-actionsheet__action">
                <div class="weui-actionsheet__cell" id="iosActionsheetCancel">取消</div>
            </div>
        </div>
    </div>
	<!--END actionSheet-->
	
	<script>
	    var key = '<?php echo $key;?>';
	</script>
