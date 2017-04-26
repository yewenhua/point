<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>商城首页</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/vendor/alternate/alternate.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
</head>
<body id="centerpage">
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

    <div class="alternate-content">
        <div class="alternate-content-detail" id="alternate-content-detail">
            <ul>
                <li style="background: transparent url('/media/img/banner1.jpg') no-repeat scroll center top" data-order="0"></li>
                <li style="background: transparent url('/media/img/banner2.jpg') no-repeat scroll center top" data-order="1"></li>
                <li style="background: transparent url('/media/img/banner3.jpg') no-repeat scroll center top" data-order="2"></li>
                <li style="background: transparent url('/media/img/banner4.jpg') no-repeat scroll center top" data-order="3"></li>
                <div class="clear"></div>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="count-area">
            <ul>
                <li class="active" data-order="0"></li>
                <li data-order="1"></li>
                <li data-order="2"></li>
                <li data-order="3"></li>
            </ul>
        </div>
        <div class="click-left">
            <div class="click-pointer" id="click_left">
                <img src="/media/img/m_left02.png" alt="left"/>
            </div>
        </div>
        <div class="click-right">
            <div class="click-pointer" id="click_right">
                <img src="/media/img/m_right02.png" alt="right"/>
            </div>
        </div>
    </div>
    <div class="clear"></div>

    <div class="hot-recommend">热门推荐</div>
    <div class="weui-grids">
        <a href="/mall/detail" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="/media/img/jiezhi1.png" alt="">
            </div>
            <p class="weui-grid__label center-title">中梁国宾馆1号138伯爵户型</p>
            <p class="weui-grid__label center-price">积分兑换：消费积分5000</p>
        </a>
        <a href="/mall/detail" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="/media/img/jiezhi2.png" alt="">
            </div>
            <p class="weui-grid__label center-title">中梁国宾馆1号138伯爵户型</p>
            <p class="weui-grid__label center-price">积分兑换：消费积分5000</p>
        </a>
        <a href="/mall/detail" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="/media/img/jiezhi1.png" alt="">
            </div>
            <p class="weui-grid__label center-title">中梁国宾馆1号138伯爵户型</p>
            <p class="weui-grid__label center-price">积分兑换：消费积分5000</p>
        </a>
        <a href="/mall/detail" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="/media/img/jiezhi2.png" alt="">
            </div>
            <p class="weui-grid__label center-title">中梁国宾馆1号138伯爵户型</p>
            <p class="weui-grid__label center-price">积分兑换：消费积分5000</p>
        </a>

    </div>
    <script src="/media/vendor/alternate/alternate.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>
    <script src="/media/js/mall/center.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>
</body>
</html>