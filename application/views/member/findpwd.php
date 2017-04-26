
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">找回密码</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd"><label class="weui-label">随机验证码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="rand_code" placeholder="请输入随机验证码"/>
                </div>
                <div class="weui-cell__ft">
                    <span class="captcha-img"></span>
                </div>
            </div>
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd">
                    <label class="weui-label">手机号</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="mobile" placeholder="请输入手机号">
                </div>
                <div class="weui-cell__ft" id="get_mobile_code_div">
                    <a href="javascript:;" class="weui-vcode-btn" id="get_mobile_code">手机验证码</a>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">手机验证码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="mobile_code"  placeholder="请输入手机验证码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">新密码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="password" placeholder="请输入新密码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">新密码确认</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="re_password" placeholder="请再次输入新密码"/>
                </div>
            </div>
        </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>

    <script>
        var type = '<?php echo $type;?>';
    </script>