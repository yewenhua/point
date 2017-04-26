    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">订单预览</div>
       <div class="right"></div>
    </div>
    
    <div class="address-content relative" id="select_address">
       <p class="address-name" id="address-name"></p>
       <p class="address-detail" id="address-detail"></p>
       <div class="address-edit"><img src="/media/img/edit_03.png"/></div>
    </div>
    
    <div class="weui-panel">
       <div class="weui-panel__bd">
               <div class="weui-media-box weui-media-box_text point-item-top record-item-top preview-item-top">
	               <div class="left">编号：<?php echo $order_id;?></div>
	               <div class="right"><?php echo substr($created_at, 0, 16);?></div>
	               <div class="clear"></div>
	           </div>
               <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg review-item">
                    <div class="weui-media-box__hd">
                        <img class="weui-media-box__thumb" src="/backend/uploads/<?php echo $goods['face'];?>" alt="">
                    </div>
                    <div class="weui-media-box__bd">
                        <h4 class="weui-media-box__title"><?php echo $goods['name'];?></h4>
                        <p class="weui-media-box__desc preview-num">数量：<?php echo $buy_num;?><?php if($size_op){echo '&nbsp;&nbsp;'.$size_op;}?></p>
                        <div class="review-price"><?php echo round($goods['share_price'], 2).'元';?></div>
                    </div>
               </a>
        </div>
    </div>
    
    <div class="weui-cells weui-cells_form review-margin-top">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label" style="width:86px;">商品金额</label></div>
                <div class="weui-cell__bd" style="font-size:15px;">&yen; <?php echo $goods['share_price'] * $buy_num;?> 元</div>
            </div>
            <div class="weui-cell" id="logistic-cell">
                <div class="weui-cell__hd"><label class="weui-label" style="width:86px;">运费</label></div>
                <div class="weui-cell__bd" style="font-size:15px;">&yen; <font id="logistic_fee"><?php echo $logistic_fee;?></font> 元</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label" style="width:86px;">支付方式</label></div>
                <div class="weui-cells_checkbox">
		            <label class="weui-check__label inline-block" for="s11">
		                <div class="weui-cell__hd">
		                    <input type="radio" class="weui-check" name="pay_type" value="wxpay" id="s11" checked="checked">
		                    <i class="weui-icon-checked"></i>
		                    <span class="fourteen-font relative" style="top:1px;">微信支付</span>
		                </div>
		            </label>
		            <label class="weui-check__label inline-block" for="s12">
		                <div class="weui-cell__hd">
		                    <input type="radio" name="pay_type" value="wallet" class="weui-check" id="s12">
		                    <i class="weui-icon-checked"></i>
		                    <span class="fourteen-font relative" style="top:1px;">钱包余额</span>
		                </div>
		            </label>
		        </div>
            </div>
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label for="" class="weui-label" style="width:86px;">配送方式</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select fourteen-font" id="logistic">
                        <?php foreach($goods['send_type_array'] as $item):?>
                            <option value="<?php if($item == 1){echo 'logistic';}else{echo 'self';}?>"><?php if($item == 1){echo '普通快递';}else{echo '上门自提';}?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
    </div>
    
    <div class="weui-cells weui-cells_form review-margin-top">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <textarea class="weui-textarea" placeholder="请输入订单留言" rows="2" id="memo"></textarea>
                    <div class="weui-textarea-counter"><span id="rest">0</span>/200</div>
                </div>
            </div>
    </div>
    
    <div class="bottom-btn-area">
        <form action="/share/create" method="post" id="submit_form">
           <input type="hidden" name="buy_num" id="buy_num" value="<?php echo $buy_num;?>"/>
           <input type="hidden" name="buy_goods_key" id="buy_goods_key" value="<?php echo $buy_goods_key;?>"/>
           <input type="hidden" name="buy_goods_attr" id="buy_goods_attr" value="<?php echo $buy_goods_attr;?>"/>
           <input type="hidden" name="buy_share_key" id="buy_share_key" value="<?php echo $buy_share_key;?>"/>
           <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id;?>"/>
           <input type="hidden" name="check_key" id="check_key" value=""/>
           <input type="hidden" name="comment" id="comment" value=""/>
           <input type="hidden" name="address" id="address" value=""/>
           <input type="hidden" name="pay_type" id="pay_type" value=""/>
           <input type="hidden" name="send_type" id="send_type" value="logistic"/>
           <div class="submit-now" id="submit">提交订单</div>
        </form>
    </div>
    
    <script>
        var buy_num = <?php echo $buy_num;?>;
        var weight = <?php echo $goods['weight'];?>;
        var logistic = <?php echo json_encode($logistic);?>;
        var pay_money = <?php echo $goods['cash_price'] * $buy_num;?>;
        var logistic_fee = <?php echo $logistic_fee;?>;
        var total_point = <?php echo $total_point;?>;
        var addressObj = null;
        <?php if($userInfo && $userInfo['address']):?>
	        addressObj = <?php echo trim($userInfo['address']);?>;
	    <?php endif;?>
    </script>

    <script type='text/javascript'>
	    /**
         * 如果初始时临时地址为空，则在页面或者微信js完成后，执行选择地址函数
         * 
         */
         if(addressObj === null){
        	 document.getElementById("address-detail").innerHTML = '收货地址';
	         if (typeof WeixinJSBridge == "undefined") {
	             if (document.addEventListener) {
	                 document.addEventListener('WeixinJSBridgeReady', selectAddress, false);
	             } else if (document.attachEvent) {
	                 document.attachEvent('WeixinJSBridgeReady', selectAddress);
	                 document.attachEvent('onWeixinJSBridgeReady', selectAddress);
	             }
	         } 
	         else {
	             selectAddress();
	         }
         }
         else{
        	 document.getElementById("address-name").innerHTML = addressObj.userName + '，' + addressObj.telNumber;
       	     document.getElementById("address-detail").innerHTML = addressObj.provinceName + addressObj.cityName + addressObj.countryName + addressObj.detailInfo;
       	     document.getElementById("address").value = JSON.stringify(addressObj);
         }
         
         function selectAddress(){
        	 wx.config({
                     debug:false,
                     signature: '<?php echo $signature;?>',
                     appId: '<?php echo $appId;?>',
                     timestamp: '<?php echo $timeStamp;?>',
                     nonceStr: '<?php echo $nonceStr;?>',
                     jsApiList: ['openAddress'],
		     });
             wx.ready(function(){
            	 wx.openAddress({
	        	     success: function (res) { 
	        	          // 用户成功拉出地址 
	        	          if(res.errMsg == 'openAddress:ok'){
		        	          var temp_obj = {};
	        	        	  addressObj = res;
	        	        	  document.getElementById("address-name").innerHTML = res.userName + '，' + res.telNumber;
	        	        	  document.getElementById("address-detail").innerHTML = res.provinceName + res.cityName + res.countryName + res.detailInfo;

	        	        	  temp_obj.userName = trim(res.userName);
	        	        	  temp_obj.telNumber = trim(res.telNumber);
	        	        	  temp_obj.nationalCode = trim(res.nationalCode);
	        	        	  temp_obj.postalCode = trim(res.postalCode);
	        	        	  temp_obj.provinceName = trim(res.provinceName);
	        	        	  temp_obj.cityName = trim(res.cityName);
	        	        	  temp_obj.countryName = trim(res.countryName);
	        	        	  temp_obj.detailInfo = trim(res.detailInfo);
	        	        	  document.getElementById("address").value = JSON.stringify(temp_obj);

	        	        	  //修改地址时更改运费
	        	        	  countLogistic(res.provinceName);
	        	          }
	        	     },
	        	     cancel: function (res) { 
	        	          // 用户取消拉出地址
	        	     }
	 	         });
             });
         }

         function trim(str) {
        	 return str.replace(/(^\s+)|(\s+$)/g, "");
         }

         function countLogistic(str){
        	 var provinceName = str.substring(0, 2);
        	 var flag = false;
        	 var firstWeight = 0;
        	 var firstFee = 0;
    		 var otherWeight = 0;
    		 var otherFee = 0;
    		 var totalWeight = buy_num * weight;
    		 var totalFee = 0;

        	 //第一次循环计算省份有没有在配置模板里
        	 for(var i=0; i<logistic.length; i++){
            	 if(logistic[i].isDefault){
            		 continue;
            	 }
            	 else{
            		 for(var j=0; j<logistic[i].addr_list.length; j++){
                		 var temp = logistic[i].addr_list[j].name;
                		 var addr_name = temp.substring(0, 2);
                		 if(addr_name == provinceName){
                			 firstWeight = Number(logistic[i].first_weight);
    	            		 firstFee = Number(logistic[i].first_fee);
    	            		 otherWeight = Number(logistic[i].other_weight);
    	            		 otherFee = Number(logistic[i].other_fee);
    	            		 
                			 flag = true;
                			 break;
                		 }
            		 }
            		 if(flag){
            			 break;
            		 }
            	 }
        	 }

        	 if(!flag){
	        	 //第二次循环获取默认邮费
	        	 for(var i=0; i<logistic.length; i++){
	            	 if(logistic[i].isDefault){
	            		 firstWeight = Number(logistic[i].first_weight);
	            		 firstFee = Number(logistic[i].first_fee);
	            		 otherWeight = Number(logistic[i].other_weight);
	            		 otherFee = Number(logistic[i].other_fee);
	            	 }
	            	 else{
	                	 
	            	 }
	        	 }
        	 }

        	 if(firstWeight > totalWeight){
        		 totalFee = firstFee;
        	 }
        	 else{
            	 var leftWeight = totalWeight - firstWeight;
            	 var leftFee = Math.ceil(leftWeight/otherWeight) * otherFee;
            	 totalFee = firstFee + leftFee;
        	 }

        	 logistic_fee = totalFee;
        	 document.getElementById("logistic_fee").innerHTML = logistic_fee;
         }

         $(function(){
        	 $("#select_address").click(function(){
        		 selectAddress();
             });
         });
    </script>