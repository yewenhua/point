
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">消费积分转让</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">当前消费积分</label></div>
                <div class="weui-cell__bd fourteen-font" id="now-consume"><?php echo $userInfo['consume_point'];?></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">转让用户手机</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="user_mobile" placeholder="请输入转让用户手机号码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">待转积分</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="transfer-point" placeholder="请输入待转积分"/>
                </div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
