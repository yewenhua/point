
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">积分复投</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel relative">
       <div class="repeat-empty-top"></div>
       <p class="repeat-point-name">当前排期倍率</p>
       <p class="repeat-point-value"><?php echo round($repeatInfo['rate'], 2);?>倍</p>
       <p class="repeat-point-limit">额度<font><?php echo $repeatInfo['limit_point'];?></font></p>
       <p class="repeat-point-use">已用<font id="already_shot_point"><?php echo $repeatInfo['already_shot_point'];?> (<?php echo round($repeatInfo['already_shot_point']*100/$repeatInfo['limit_point'], 2);?>%)</font></p>
       <div class="repeat-empty-bottom"></div>
       <div class="wait-point-pie">
          <canvas id="wait-point-pie" width="300" height="150"></canvas>
       </div>
    </div>
    
    <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">当前可用积分</label></div>
                <div class="weui-cell__bd fourteen-font" id="now-point"><?php echo $userInfo['useable_point'];?></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">复投可用积分</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" id="shoot-point" type="number" placeholder="请输入复投可用积分"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">获得待用积分</label></div>
                <div class="weui-cell__bd fourteen-font" id="future-point">--</div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
    <script>
		var multiple = <?php echo $repeatInfo['rate'];?>;
		var day = <?php echo $repeatInfo['day'];?>;
		var info = <?php echo json_encode($repeatInfo);?>;
	</script>

