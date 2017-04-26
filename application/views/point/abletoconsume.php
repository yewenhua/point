
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">可用积分转消费积分</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">当前可用积分</label></div>
                <div class="weui-cell__bd fourteen-font" id="now-useable"><?php echo $userInfo['useable_point'];?></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">当前消费积分</label></div>
                <div class="weui-cell__bd fourteen-font" id="now-consume"><?php echo $userInfo['consume_point'];?></div>
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
    
