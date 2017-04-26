
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">银行卡绑定</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
	        <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">发卡银行</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="bank_name" placeholder="请输入发卡银行"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">开户姓名</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" id="username" placeholder="请输入开户名称" value="<?php echo $userInfo['name'];?>"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">银行卡号</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" id="card_no" placeholder="请输入银行卡号"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">开户网点</label></div>
                <div class="weui-cell__hd weui_cell_primary">
	                <div id="city_select_content">
				  		<select class="prov"></select> 
				    	<select class="city" disabled="disabled"></select>
				    	<select class="dist" disabled="disabled"></select>
				    </div>
	            </div>
            </div>
            <div class="weui-cell">
	            <div class="weui-cell__hd"><label class="weui-label"></label></div>
	            <div class="weui-cell__hd weui_cell_primary">
	                <input class="weui-input" type="text"  placeholder="请输入开户网点" id="bank_address"/>
	            </div>
	        </div>
	        <input type="hidden" value="<?php echo $type;?>" id="type"/>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now" id="submit">提交</div>
    </div>
