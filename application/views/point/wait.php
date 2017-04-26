
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">待用积分</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <p class="wait-point-value"><?php echo $userInfo['wait_point'];?></p>
       <p class="wait-point-name">余额</p>
    </div>
    
    <div id="list" class="wait_record">
        
    </div>
    
    <div class="load-more" style="display:none;">点击加载更多</div>
    <div class="list-bottom-last" style="display:none;">我是有底线的</div>
    
    <script>
        var type = '<?php echo $type;?>';
    </script>
