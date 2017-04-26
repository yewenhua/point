<?php 
function batch($url){
	$ch = curl_init(); 
	$timeout = 5; 
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
	$file_contents = curl_exec($ch); 
	curl_close($ch); 
	echo $file_contents; 
}
$urlArray = array(
    'http://www.ziyivip.com/index.php/crontab/order_auto_query_do',
	'http://www.ziyivip.com/index.php/crontab/online_auto_query_do',
	'http://www.ziyivip.com/index.php/crontab/auto_clear_wait_point',
	'http://www.ziyivip.com/index.php/crontab/auto_sure_order',
	'http://www.ziyivip.com/index.php/crontab/auto_close_order',
	'http://www.ziyivip.com/index.php/crontab/auto_close_declaration',
    'http://www.ziyivip.com/index.php/crontab/auto_process_refund_order'
);
foreach($urlArray as $item){
    batch($item);
}
?> 