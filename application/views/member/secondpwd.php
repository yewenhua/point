
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">二级密码设置</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell" style="<?php if($have_second_pwd == 'no'){echo 'display:none';} ?>">
                <div class="weui-cell__hd"><label class="weui-label">原二级密码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="original_password" placeholder="请输入原密码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label"><?php if($have_second_pwd == 'yes'){echo '新';}?>二级密码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="new_password" placeholder="请输入<?php if($have_second_pwd == 'yes'){echo '新';}?>二级密码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label"><?php if($have_second_pwd == 'yes'){echo '新';}?>密码确认</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" id="renew_password" placeholder="请再次输入<?php if($have_second_pwd == 'yes'){echo '新';}?>二级密码"/>
                </div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
    <?php if($have_second_pwd == 'yes'):?>
        <a href="/member/findpaypwd"><div style="margin-top:-12px; text-align:left;font-size:13px; color:#333; padding-left:15px;">忘记二级密码？</div></a>
    <?php endif;?>
    
    <script>
        var have_second_pwd = '<?php echo $have_second_pwd;?>';
    </script>
