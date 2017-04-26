
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">个人详情</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">手机号码</label></div>
                <div class="weui-cell__bd fourteen-font"><?php echo $userInfo['mobile'];?></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">推荐人编号</label></div>
                <div class="weui-cell__bd fourteen-font"><?php echo $userInfo['parent_mobile'];?></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">用户名</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="name" value="<?php echo $userInfo['name']; ?>" placeholder="请输入用户名"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">性别</label></div>
                <div class="weui-cell__bd">
                    <div class="form-group inline-block sex-bg">
			            <div class="radio inline-block sex-img-item">
			              <label class="i-checks">
			                <input type="radio" name="sex" value="1" <?php if($userInfo['sex'] == 1){echo 'checked';}?>/><span class="sex-name">男</span>
			              </label>
			            </div>
			            <div class="radio inline-block sex-img-item">
			              <label class="i-checks">
			                <input type="radio" name="sex" value="0" <?php if($userInfo['sex'] == 0){echo 'checked';}?>/><span class="sex-name">女</span>
			              </label>
			            </div>
			        </div>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">身份证号码</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="card_no" value="<?php echo $userInfo['card_no']; ?>" placeholder="请输入身份证号码"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">省市区</label></div>
                <div class="weui-cell__bd fourteen-font"><?php echo $userInfo['province'].$userInfo['city']; ?></div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
