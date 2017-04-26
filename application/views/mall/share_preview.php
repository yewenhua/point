<div class="list-header">
    <div class="left goback">返回</div>
    <div class="title">分享预览</div>
    <div class="right"></div>
</div>
    
<div class="weui-panel" style="margin-top: 10px;">
    <div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_text point-item-top record-item-top order-item-top">
            <div class="left">编号：<?php echo $order_id;?></div>
            <div class="right"><?php echo date('Y-m-d H:i');?></div>
            <div class="clear"></div>
        </div>
        <div class="weui-media-box weui-media-box_appmsg review-item">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="<?php echo '/backend/uploads/'.$goods['face']; ?>" alt="">
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title"><?php echo $goods['name'];?></h4>
                <div class="relative">
                    <p class="weui-media-box__desc order-list-num">数量：<?php echo $num;?></p>
                </div>
                <div class="review-price"><?php if($goods['model'] == 1){echo '购物券'.$goods['point_price'].'分';}elseif($goods['model'] == 2){echo '消费积分'.$goods['point_price'].'分';}else{echo '消费积分'.$goods['point_price'].'分 + 现金'.round($goods['cash_price'], 2).'元';}?></div>
            </div>
        </div>
    </div>
</div>

<div class="weui-panel cneter-footer-item">
    <div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_small-appmsg">
            <div class="weui-cells">
                <div class="weui-cell weui-cell_access first">
                   <div class="weui-cell__bd weui-cell_primary">
                      <p>消费积分<font class="share-detail"><?php echo $goods['point_price'] * $num;?> 分</font></p>
                   </div>
                </div>
                <div class="weui-cell weui-cell_access">
                   <div class="weui-cell__bd weui-cell_primary">
                      <p>分享单价<font class="share-detail"><?php echo round($goods['share_price'], 2);?> 元</font></p>
                   </div>
                </div>
                <div class="weui-cell weui-cell_access">
                   <div class="weui-cell__bd weui-cell_primary">
                      <p>预计佣金<font class="share-detail"><?php if($goods['share_price'] >= $goods['cash_price']){echo round(($goods['share_price'] - $goods['cash_price']) * $num, 2);}else{echo round(($goods['cash_price'] - $goods['share_price']) * $num, 2);}?> 元</font></p>
                   </div>
                </div>
             </div>
         </div>
    </div>
</div>

<div class="bottom-btn-area">
    <div class="logout-now" id="submit">确定分享</div>
</div>

<script>
var order_id = '<?php echo $order_id;?>';
var num = <?php echo $num;?>;
var key = '<?php echo $goods['key'];?>';
var uid = '<?php echo $uid;?>';


$(function(){
	$(".goback").click(function(){
		history.back();
    });

	$("#submit").click(function(){
		layer.prompt({
			title: '请输入二级密码', 
			formType: 1
		}, function(text){
			if(!$("#submit").hasClass('on')){
				layer.closeAll('dialog');
				$("#submit").addClass('on');
				$("#submit").html('提交中…');
				
				$.ajax({
		            url: '/mall/do_share',
		            type: 'POST',
		            data: {
					    key: key,
					    num: num,
					    order_id: order_id,
					    check_key: text
		            },
		            dataType: 'json',
		            cache: false,
		            beforeSend: function() {
		
		            },
		            success: function(response){
		                if(response.code == 0){
		                	layer.msg('提交成功，跳转中…');
		                	setTimeout(function(){
		                		var url = '/mall/share?key=' + response.key; 
		                		location.href = url;
				            }, 1000);
		                }
		                else if(response.code == 9999 || response.code == 8888){
							layer.msg(response.message);
							setTimeout(function(){
								location.href = '/member/login';
							}, 1500);
						}
		                else{
		                    layer.msg(response.message);
		                }
		                $("#submit").removeClass('on');
		                $("#submit").html('确定分享');
		            },
		            error: function(XMLHttpRequest, textStatus, errorThrown){
		                layer.msg('未知错误');
		                $("#submit").removeClass('on');
		                $("#submit").html('确定分享');
		            }
		        });
			}
		});
	});


	
});
</script>