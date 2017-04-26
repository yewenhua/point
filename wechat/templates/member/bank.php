<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>银行卡绑定</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
   <script src="/media/vendor/jquery.cityselect.js"></script>   
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">银行卡绑定</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-cells weui-cells_form">
	        <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">发卡银行</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入发卡银行"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">开户名称</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入开户名称"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">银行卡号</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" placeholder="请输入银行卡号"/>
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
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now">提交</div>
    </div>
    <script src="/media/js/member/bank.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>