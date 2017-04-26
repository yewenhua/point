
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title"><?php echo $title;?></div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd">
               <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
	               <div class="order-detail-title">分享详情</div>
	           </div>
               <div class="weui-media-box order-detail-item">
                    <p>分享编号：<?php echo $share['order_id'];?></p>
                    <p>消费积分：<?php echo $share['single_point'];?>分</p>
                    <p>分享价格：<?php echo $share['share_price'];?>元</p>
                    <p>分享状态：<?php if($share['status'] == 0){echo '未结算';}else{echo '已结算';}?></p>
                    <p>分享时间：<?php echo $share['created_at'];?></p>
                    <p>已用积分：<?php echo $share['used_point'];?>分</p>
                    <p>所获佣金：<?php echo $share['geted_commision'];?>元</p>
                    <p>退回积分：<?php echo $share['back_point'];?>分</p>
                    <p>退回时间：<?php echo $share['clear_time'] ? $share['clear_time'] : '--';?></p>
               </div>
        </div>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd">
               <div class="weui-media-box weui-media-box_text point-item-top record-item-top">
	               <div class="order-detail-title">商品信息</div>
	           </div>
               <a href="/mall/detail?key=<?php echo $goods['key'];?>" class="weui-media-box weui-media-box_appmsg review-item">
                    <div class="weui-media-box__hd">
                        <img class="weui-media-box__thumb" src="/backend/uploads/<?php echo $goods['face'];?>" alt="">
                    </div>
                    <div class="weui-media-box__bd">
                        <h4 class="weui-media-box__title"><?php echo $goods['name'];?></h4>
                        <p class="weui-media-box__desc">数量：<?php echo $share['share_num'];?></p>
                        <div class="review-price">
                            <?php 
                               if($goods['model'] == 1){
                               	   echo '购物券 '.$goods['point_price'].'分';
                               }
                               elseif($goods['model'] == 2){
                               	   echo '消费积分 '.$goods['point_price'].'分';
                               }
                               elseif($goods['model'] == 3){
                               	   echo '消费积分 '.$goods['point_price'].'分 + 现金 &yen;'.$goods['cash_price'].'元';
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
	               <div class="order-detail-title">购买记录</div>
	           </div>
               <div class="weui-media-box order-detail-item">
                    <?php if(!empty($salelist)):?>
                        <ul class="logistic-detail">
	                        <?php foreach($salelist as $item):?>
                               <li class="relative">
                                  <p class="station"><?php echo $item['name'].'（'.$item['mobile'].')';?> <?php echo $item['num'];?>份</p>
                                  <p class="time"><?php echo $item['created_at'];?></p>
                                  <p class="time" style="position: absolute; right:0px; bottom:0px;"><?php if($item['status'] == 0){echo '待支付';}elseif($item['status'] == 1){echo '待发货';}elseif($item['status'] == 2){echo '已发货';}elseif($item['status'] == 3){echo '已完成';}elseif($item['status'] == 4){echo '已关闭';}elseif($item['status'] == 5){echo '退款中';}elseif($item['status'] == 6){echo '已退款';}elseif($item['status'] == 7){echo '待退款';}?></p>
                               </li>
	                        <?php endforeach;?>
                        </ul>
                    <?php else:?>
                        <p style="color:#bdbdbd;">暂未有购买信息</p>
                    <?php endif;?>
               </div>
        </div>
    </div>
	
	<script>
		$(function(){
			$(".goback").click(function(){
				location.href = '/orders/lists';
		    });
		});
	</script>
