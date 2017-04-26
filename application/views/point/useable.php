
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">可用积分</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <p class="wait-point-value"><?php echo $userInfo['useable_point'];?></p>
       <p class="wait-point-name">余额</p>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd useable-repeat">
	       <div class="left">
	           <div class="value">
	              <a href="/point/abletoconsume">
		              <img src="/media/img/point_03.png"/>
		              <div class="title">转消费积分</div>
	              </a>
	           </div>
	       </div>
	       <div class="right">
	           <div class="value">
	              <a href="/point/repeatlist">
		              <img src="/media/img/point_05.png"/>
		              <div class="title">复投</div>
	              </a>
	           </div>
	       </div>
	       <div class="clear"></div>
       </div>
    </div>
    
    <div id="list">
        
    </div>
    
    <div class="load-more" style="display:none;">点击加载更多</div>
    <div class="list-bottom-last" style="display:none;">我是有底线的</div>
    
    <script>
        var type = '<?php echo $type;?>';
    </script>
