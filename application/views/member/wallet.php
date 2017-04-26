
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">钱包</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
       <p class="wait-point-value"><?php echo round($userInfo['commision'], 2);?><span class="cash_unit">元</span></p>
       <p class="wait-point-name">余额</p>
    </div>
    <div class="weui-panel">
       <div class="weui-grids">
	        <a href="<?php if($type == 'member'){echo '/member/commision/month';}else{echo '/service/commision/month';}?>" class="weui-grid">
	            <p class="grid-point-value"><?php echo round($monthCommision, 2);?><span class="cash_unit">元</span></p>
	            <p class="grid-point-name">本月佣金</p>
	        </a>
	        <a href="<?php if($type == 'member'){echo '/member/commision/total';}else{echo '/service/commision/total';}?>" class="weui-grid">
	            <p class="grid-point-value"><?php echo round($totalCommision, 2);?><span class="cash_unit">元</span></p>
	            <p class="grid-point-name">累计佣金</p>
	        </a>
	        <a href="/member/walletconsume/month/<?php echo $type;?>" class="weui-grid">
	            <p class="grid-point-value"><?php echo round($monthConsume, 2);?><span class="cash_unit">元</span></p>
	            <p class="grid-point-name">本月消费</p>
	        </a>
	        <a href="/member/walletconsume/total/<?php echo $type;?>" class="weui-grid">
	            <p class="grid-point-value"><?php echo round($totalConsume, 2);?><span class="cash_unit">元</span></p>
	            <p class="grid-point-name">累计消费</p>
	        </a>
	        <a href="javascript:;" class="weui-grid" id="getcash">
	            <div class="takecash_img">
	                <img src="/media/img/wallet_03.png"/>
	            </div>
	            <p class="grid-point-name">我要提现</p>
	        </a>
	        <a href="/member/cashlog/<?php echo $type;?>" class="weui-grid">
	            <p class="grid-point-value"><?php echo round($totalTakecash, 2);?><span class="cash_unit">元</span></p>
	            <p class="grid-point-name">累计提现</p>
	        </a>
	    </div>
    </div>
    
    <script>
        var bank_num = <?php echo $bank_num;?>;
        var type = '<?php echo $type;?>';
    </script>
    
