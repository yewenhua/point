   <?php foreach($banklist as $key=>$item):?>
	   <div class="banklist-outer">
		   <div class="weui-panel relative visible margin-top-zero bank bank-item-<?php echo $key;?>">
		        <div class="weui-panel__hd">
		            <div class="weui_media_box weui_media_text">
		                <div>
		                   <span class="layout-left"><span class="bank-title"><?php echo $item['bank_name'];?></span></span>
		                   <span class="layout-right zhengshu"><span class="bank-no"><?php echo $item['card_no'];?></span></span>
		                   <div class="clear"></div>
		                </div>
		                <div class="item-bank">
		                   <span class="layout-left tx-time"><?php echo $item['bank_address'];?></span>
		                   <span class="layout-right"><span class="bank-owner"><?php echo $item['user_name'];?></span></span>
		                   <div class="clear"></div>
		                </div>
		            </div>
		        </div>
		   </div>
	       <div class="weui_panel_delete" data-bankid="<?php echo $item['id'];?>"><span>删除</span></div>
	   </div>
   <?php endforeach;?>
   <div class="weui_panel relative visible margin-top-zero">
        <div class="weui_panel_bd">
            <a href="/index.php/service/bank"><div class="item-bank-add">+</div></a>
        </div>
   </div>
   <script>
        var size = <?php echo count($banklist);?>;
        var type = '<?php echo $type;?>';
   </script>