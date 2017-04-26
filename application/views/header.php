<html>
<head>
   <meta charset="UTF-8" />
   <meta name="renderer" content="webkit|ie-comp|ie-stand">
   <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   <meta name="format-detection" content="telephone=no" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
   <meta http-equiv="Cache-Control" content="no-siteapp" />
   <meta name='keywords' content='<?php if($systemInfo !== null && isset($systemInfo['keywords'])){echo $systemInfo['keywords'];}else{echo $title;}?>' />
   <meta name='description' content='<?php if($systemInfo !== null && isset($systemInfo['description'])){echo $systemInfo['description'];}else{echo $title;}?>' />
   <title><?php if($systemInfo !== null && isset($systemInfo['site_name'])){echo $title.'-'.$systemInfo['site_name'];}else{echo $title;}?></title>

   <?php if(isset($env) && $env == 'dev'):?>
	   <link rel="stylesheet" href="/media/vendor/weui/weui.min.css" type="text/css" />
	   <link rel="stylesheet" href="/media/css/mall.css?v=<?php echo rand(1,10).'.'.rand(1,10);?>" type="text/css" />
	   <link rel="stylesheet" href="/media/css/loading.css" type="text/css" />
   <?php else:?>
       <link rel="stylesheet" href="/media/dist/css/wechat.min.css?v=1.0" type="text/css" />
   <?php endif;?>
   
   <?php if(isset($page_css) && !empty($page_css)):?>
	   <?php foreach($page_css as $item):?>
	       <link rel="stylesheet" href="<?php echo $item;?>" type="text/css" />
	   <?php endforeach;?>
   <?php endif;?>
   
   <?php if(isset($env) && $env == 'dev'):?>
	   <script src="/media/vendor/jquery/1.9.1/jquery.min.js"></script>
	   <script src="/media/vendor/layer/2.4/layer.js"></script>   
   <?php else:?>
       <script src="/media/dist/js/wechat.min.js?v=1.0"></script>   
   <?php endif;?>
   
   <?php if(isset($page_js) && !empty($page_js)):?>
	   <?php foreach($page_js as $item):?>
	       <script src="<?php echo $item;?>"></script>
	   <?php endforeach;?>
   <?php endif;?>
</head>
<body id="<?php echo $page_id;?>" style="height:100%;">