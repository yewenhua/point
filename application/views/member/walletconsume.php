
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title"><?php if($categoty == 'total'){echo '累计消费';}else{echo '本月消费';}?></div>
       <div class="right"></div>
    </div>
    
    <div id="list" class="wait_record">
        
    </div>
    
    <div class="load-more" style="display:none;">点击加载更多</div>
    <div class="list-bottom-last" style="display:none;">我是有底线的</div>
    
    <script>
        var categoty = '<?php echo $categoty;?>';
        var type = '<?php echo $type;?>';
    </script>
    
