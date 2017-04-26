
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">我的会员</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <p class="wait-point-value"><?php echo $count;?></p>
       <p class="wait-point-name">会员总数</p>
    </div>
    
    <div id="list" class="wait_record">
        <?php foreach($student_list as $item):?>
	        <div class="weui-panel">
		       <div class="weui-panel__bd">
		           <div class="weui-media-box weui-media-box_text exchange-point-item-top">
		               <div class="title">用户编号：<?php echo $item['mobile'];?></div>
		           </div>
		           <div class="weui-media-box weui-media-box_text exchange-point-item student-point-item">
		               <div class="left">
		                  <div class="value"><?php if($item['name']){echo $item['name'];}else{echo '--';}?></div>
		               </div>
		               <div class="right">
		                  <div class="status">加入时间：<?php echo substr($item['created_at'], 0, 10);?></div>
		               </div>
		               <div class="clear"></div>
		           </div>
		       </div>
		    </div>
	    <?php endforeach;?>
    </div>
    
    <div class="load-more" style="display:<?php if($count > $num){echo 'block';}else{echo 'none';}?>;">点击加载更多</div>
    <div class="list-bottom-last" style="display:<?php if($count <= $num){echo 'block';}else{echo 'none';}?>;">我是有底线的</div>
    
    <script>
        var page = <?php echo $page;?>;
        var num = <?php echo $num;?>;
    </script>
