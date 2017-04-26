
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">我要提现</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">钱包余额</label></div>
                <div class="weui-cell__bd fourteen-font"><span id="left_money"><?php echo $userInfo['commision'];?></span><span> 元</span></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">提现金额</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="submit_money" style="font-family: microsoft yahei;" placeholder="请输入提现金额"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">到账金额</label></div>
                <div class="weui-cell__bd fourteen-font" id="get_money"></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">银行卡</label></div>
                <div class="weui-cell__bd">
                    <select class="bank_select" id="bank">
                        <?php foreach($banklist as $item):?>
	                       <option value="<?php echo $item['id'];?>"><?php echo $item['bank_name'];?></option>
	                    <?php endforeach;?>
	                </select>
                </div>
            </div>
    </div>
    <p class="cash-alert">提现金额超过3500的部分收取8%的税点，每月只能提现一次</p>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
    
    <script>
        var type = '<?php echo $type;?>';
    </script>
    
