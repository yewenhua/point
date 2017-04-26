
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">在线报单</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
       <form action="/service/do_online" method="post" id="submit_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">用户编号</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="mobile" id="mobile" placeholder="请输入用户编号"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">报单金额</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="total" id="total" placeholder="请输入报单金额"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">消费积分</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="point" id="point" placeholder="请输入消费积分"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">支付方式</label></div>
                <div class="weui-cells_checkbox">
		            <label class="weui-check__label inline-block" for="s11">
		                <div class="weui-cell__hd">
		                    <input type="radio" class="weui-check" name="pay_type" value="wxpay" id="s11" checked="checked">
		                    <i class="weui-icon-checked"></i>
		                    <span class="fourteen-font relative" style="top:1px;">微信支付</span>
		                </div>
		            </label>
		            <label class="weui-check__label inline-block" for="s12">
		                <div class="weui-cell__hd">
		                    <input type="radio" name="pay_type" value="wallet" class="weui-check" id="s12">
		                    <i class="weui-icon-checked"></i>
		                    <span class="fourteen-font relative" style="top:1px;">钱包余额</span>
		                </div>
		            </label>
		        </div>
            </div>
            <input class="weui-input" type="hidden" name="check_key" id="check_key"/>
        </form>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
    
    <script>
        var consume_point = <?php echo $userInfo['consume_point'];?>;
        var commision = <?php echo $userInfo['commision'];?>;
        var schedule = <?php echo json_encode($schedule);?>;
    </script>
