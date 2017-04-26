<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>个人资料</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="personal-page">
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">个人详情</div>
       <div class="right"></div>
    </div>
    
    <div class="weui-panel">
        <div class="input-item-div">
           <span class="input-item-title">手机号码</span>
           <input type="text" placeholder="请输入手机号码" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">推荐人编号</span>
           <input type="text" placeholder="请输入推荐人编号" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">用户名</span>
           <input type="text" placeholder="请输入用户名" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">性别</span>
           <div class="form-group inline-block sex-bg">
	            <div class="radio inline-block sex-img-item">
	              <label class="i-checks">
	                <input type="radio" name="sex" value="1"/><span class="sex-name">男</span>
	              </label>
	            </div>
	            <div class="radio inline-block sex-img-item">
	              <label class="i-checks">
	                <input type="radio" name="sex" value="0"/><span class="sex-name">女</span>
	              </label>
	            </div>
	        </div>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">联系方式</span>
           <input type="text" placeholder="请输入联系方式" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">身份证号码</span>
           <input type="text" placeholder="请输入身份证号码" class="input-item"/>
        </div>
        <div class="input-item-div">
           <span class="input-item-title">省市区</span>
           <input type="text" placeholder="请输入省市区" class="input-item"/>
        </div>
    </div>
    
    <div class="bottom-btn-area">
        <div class="submit-now">提交</div>
    </div>
    <script src="/media/js/member/personal.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>