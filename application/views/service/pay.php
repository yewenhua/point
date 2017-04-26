    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    

    <div class="page__bd" style="font-size: 14px;">
        <div class="weui-form-preview">
            <div class="weui-form-preview__hd">
                <label class="weui-form-preview__label">实际付款金额</label>
                <em class="weui-form-preview__value">¥<?php echo $order['pay_money']; ?></em>
            </div>
            <div class="weui-form-preview__bd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">商品</label>
                    <span class="weui-form-preview__value">用户报单充值</span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">抵用消费积分</label>
                    <span class="weui-form-preview__value"><?php echo $order['point']; ?></span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">总充值金额</label>
                    <span class="weui-form-preview__value"><?php echo $order['recharge_money']; ?></span>
                </div>
            </div>
            <div class="weui-form-preview__ft">
                <a class="weui-form-preview__btn weui-form-preview__btn_default" style="font-size:18px;" href="/service/record">取消</a>
                <button type="button" class="weui-form-preview__btn weui-form-preview__btn_primary" id="pay" style="font-size:18px;">确定支付</button>
            </div>
        </div>
    </div>

 
    <script type='text/javascript'>
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
                        location.href = '/service/success';
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