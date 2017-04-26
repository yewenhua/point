    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    

    <div class="page__bd" style="font-size: 14px;">
        <div class="weui-form-preview">
            <div class="weui-form-preview__hd">
                <label class="weui-form-preview__label">付款金额</label>
                <em class="weui-form-preview__value">¥<?php echo $order['total_cash_price']; ?></em>
            </div>
            <div class="weui-form-preview__bd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">商品名称</label>
                    <span class="weui-form-preview__value"><?php echo $order['goods_name']; ?></span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label"><?php if($order['goods_model'] == 1){echo '购物券';}else{echo '消费积分';}?></label>
                    <span class="weui-form-preview__value"><?php echo $order['total_point_price']; ?>分</span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">现金价格</label>
                    <span class="weui-form-preview__value"><?php echo round($order['total_cash_price'] - $order['logistic_fee'], 2); ?>元</span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">运费</label>
                    <span class="weui-form-preview__value"><?php if($order['logistic_fee'] == 0){echo '免邮';}else{echo $order['logistic_fee'].'元';}; ?></span>
                </div>
            </div>
            <div class="weui-form-preview__ft">
                <a class="weui-form-preview__btn weui-form-preview__btn_default" style="font-size:18px;" href="/orders/lists">取消</a>
                <button type="button" class="weui-form-preview__btn weui-form-preview__btn_primary" id="pay" style="font-size:18px;">确定支付</button>
            </div>
        </div>
    </div>

 
    <script type='text/javascript'>
        var is_new = <?php echo $is_new;?>;
        var submited = false;
        function jsApiCall(){
            if(submited == true){
                layer.msg('请勿重复提交');
                return false;
            }
            
            submited = true;
            WeixinJSBridge.invoke
            (
                'getBrandWCPayRequest',
                <?php echo $jsapiParameters; ?>,
                function(res)
                {
                    // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。
                    if(res.err_msg == "get_brand_wcpay_request:ok")
                    {
                        if(is_new == 0){
                            location.href = '/orders/success';
                        }
                        else{
                        	var url = 'http://mp.weixin.qq.com/s?__biz=MzI2OTU0OTcyMA==&mid=2247483651&idx=1&sn=edf18318520d8fd542e35bce59961345&chksm=eadfe8f4dda861e2a1a9103f4a8d7d68fabcb4da3ed8972d2d228176cf89a945277fe9b68426#rd';
                        	location.href = url;
                        }
                    }
                    else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                    	layer.msg('支付取消');
                    }
                    else
                    {
                    	layer.msg('支付失败');
                    }
                    submited = false;
                }
            );
        }

        function callpay()
		{
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}

        $(function(){
       	    $("#pay").click(function(){
       	    	callpay();
            });
        });
    </script>