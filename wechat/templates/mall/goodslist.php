<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>商品列表</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
   <script src="/media/vendor/laypage/1.3/laypage.js"></script>   
</head>
<body id="goodslistpage">
   <div class="category-search">
        <div class="weui-search-bar" id="searchBar">
            <form class="weui-search-bar__form">
                <div class="weui-search-bar__box">
                    <i class="weui-icon-search"></i>
                    <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索" required/>
                    <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
                </div>
                <label class="weui-search-bar__label" id="searchText">
                    <i class="weui-icon-search"></i>
                    <span>搜索</span>
                </label>
            </form>
            <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
        </div>
    </div>
   <div class="list-header-sort">
      <ul>
         <li class="active"><span>默认</span></li>
         <li><span>销量</span></li>
         <li><span>价格</span></li>
         <div class="clear"></div>
      </ul>
   </div>
   
   <div class="list-content">
      <div class="weui-grids" id="list">
	        
	  </div>
   </div>
   <div id="page"></div>
   <script src="/media/js/mall/goodslist.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
</body>
</html>