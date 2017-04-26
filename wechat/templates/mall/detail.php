<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="viewport" content="width=device-width, initial-scale=1"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <title>商品详情</title>

   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
   <link rel="stylesheet" href="/media/vendor/alternate/alternate.css" type="text/css" />
   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
   
   <script src="/media/vendor/weui/jweixin.js"></script>
   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
   <script src="/media/vendor/layer/2.4/layer.js"></script>   
   
   <script type="text/javascript" src="/media/vendor/react/react.js"></script>
   <script type="text/javascript" src="/media/vendor/react/react-dom.js"></script>
   <script type="text/javascript" src="/media/vendor/react/browser.min.js"></script>
</head>
<body id="detailpage">
    <div class="list-header">
      <div class="left goback">返回</div>
      <div class="title">商品详情</div>
      <div class="right"></div>
    </div>
    <div class="alternate-content">
        <div class="alternate-content-detail" id="alternate-content-detail">
            <ul>
                <?php foreach($imglist as $key=>$item):?>
                <li style="background: transparent url('<?php echo '/backend/uploads/'.$item['file']; ?>') no-repeat scroll center top" data-order="<?php echo $key;?>"></li>
                <?php endforeach;?>
                <div class="clear"></div>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="count-area">
            <ul>
                <?php foreach($imglist as $key=>$item):?>
                <li class="<?php if($key == 0){echo 'active';}?>" data-order="<?php echo $key;?>"></li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    
    <div class="goods-title-desc">
        <div class="goods-title"><?php echo $goods['name'];?></div>
        <!--<div class="goods-desc">加厚羊绒</div>-->
        <div class="goods-price">积分兑换：<?php echo $goods['price'];?>元（已销<?php echo $goods['sales_volume'];?>件）</div>
    </div>
    
    <div class="weui-media-box weui-media-box_small-appmsg" style="margin-top:1px;">
        <div class="weui-cells">
            <a class="weui-cell weui-cell_access" href="javascript:;">
                <div class="weui-cell__bd weui-cell_primary relative">
                    <p>兑换方式</p>
                    <span class="exchange_type"><?php if($goods['model'] == 1){echo '购物券';}elseif($goods['model'] == 2){echo '消费积分';}else{echo '消费积分 + 现金';}?></span>
                </div>
                <span class="weui-cell__ft"></span>
            </a>
        </div>
    </div>
    
    <div id="num-change" total="<?php echo $goods['total'];?>"></div>
    
    <div class="buy-content">
        <form action="/index.php/mall/preview" method="post" id="submit_form">
           <input type="hidden" name="buy_num" id="buy_num" value="1"/>
           <input type="hidden" name="buy_goods_id" id="buy_goods_id" value="<?php echo $goods['id'];?>"/>
           <div class="buy-now" id="submit">立即购买</div>
        </form>
    </div>
    
    <div class="goods-detail">
        <div class="goods-detail-title-bg">
           <div class="goods-detail-title">商品详情</div>
        </div>
        <div><?php echo $goods['description']; ?></div>
    </div>
    
    <div class="footer">
        <div class="footer-title">捷点科技</div>
    </div>
    
    <script src="/media/vendor/alternate/alternate.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>
    <script src="/media/js/mall/detail.js?v=<?php echo rand(1,10).'.'.rand(1,10);?>"></script>    
    <script type="text/babel" src="/media/js/mall/num-change.js"></script>
</body>
</html>