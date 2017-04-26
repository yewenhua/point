<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Alipay extends CI_Controller {
	
	private $appid = '2017032906453753';
	private $gatewayUrl = 'https://openapi.alipay.com/gateway.do';
	
	//商户应用私钥 用于签名
	private $app_private_key = 'MIIEogIBAAKCAQEA2Cj3rl/rPLQjAFx8wwQ4CtpHhIApTID2FykwO8OOjt3r6uHQ5cQN35yiGjSLwvKKwYObboD4KS6u4qFzDorCxcCw40SkhOilLFWhvlWhyRrIbxSdSmKRVclVhu8z19O/ypWYCIJU4Z3ZkguT8ORatztDvAtFlaMVFYe3xsS+1kXZKjrC7CSQJsXmPhyCx0aq3yRtiw6/ya6j4hnLfZUZjKXe6YMuqr+wWBcGvzTWMtqrkfZAb5fW4jrlHwJyDhWOVfLca3GVyrZ8hHbpa72TnsaXnBZWo/Pyujy7TWxwQL7dq+cNs6h+7ZUip+qr931joILcIwoB3XCZAtMuM7LnFQIDAQABAoIBABcdSl77Isx69sMzIKQ570Q50yv4CHKF18riPKYnYzcjw/Z57zyimlkvBqaGt+tAxFWtHRHT+LVuqITTftovPGSpFkE3NeCAgCkdfw47WfGHS6eVLLynjaL+xIaJTaHmorunA3rldO4rUvirBEbeJFUzoSqaifZWWgrTWHOI1l9TJnYqteY7J4x5g3AGm7fGUoj/HMeXx2RGtFGHT5bYso8f/hwu4MMptT/J4mra5t0KiGhSN9cgPuU2Q26S+i9Y4MQg8EMLhXQ0sdHww8Zl9tShIqzRUnoFN6I9TpllTLp4lgUMIHaqjTQIk1HdYTshY4FWU0Td5uowP9XzMsRj6AECgYEA7NCC9jXKitKdgy57a/Z8r1Kjk/eLfzAr4zpYLq7TZFO8cEn5xO4xfaV3nHclNdO8GfgsG+VDtxKa9YL7NilXjyHUgq8SoJ4z7xVUyVwH7oUarO2O3BCUFIUhNHJIntNMUkxwlOMpuUHdejSFJi6jk6LFYHc7wdmMWA9GNb50SWUCgYEA6awWCzd7ar+pjyH3KZPt1+u2aRyyocQ/OVdCJ/rfwcuFloHWrOBpEZ7IP3ESfG2uJome4rzh7oQJ/qEN7X0toKDEw/b48tiPMNg+jkP03pOJKnbbq0HZ9numBEX01hyYhHIWaq9cuzOwYC3RiZClQnLcSsA4MbE4DUqGzq7dI/ECgYA9dvkm0ltZoMTFMaFTKky+bKKMWynXvzcE6TXOGA5B8gHs2yZ62J/7zqC2+a2sxOIDk4hPTThAIhIaOv7c1eol+k/gA3TAP4+XNGAnSz27yMxdqeL0d1EX6l3t8OOsZYdfrPtjiXpg3RmRCGSuvlgyj4LPQOM/sylcCcrdVCt2iQKBgGLTMdt6koq6Fnb/cW23L38DJHld9rptqiORINyRwHJQpeXacbVZj3YxRhV2t8/B/0YzK+xO3+qlEykKaj6Hk3V8qNsMJ4tlRWHuAr7eRMChewBnlk3eotUYxETIZVzsaSCSs6JZGgneOXjjx9u7PflVZI+Erf9uKPuOCmd9Eu+xAoGAQJmYIbM/DFC1RM0RNItcWI6pR+yW1idUUB5YZemUYMtUKJof/+t6E5MwGPwmCJ6L4tfR4BLHbIqsLL6Rd3Mw4PsCZZQ16r4zL2tnXUEfewjmfy2fVVDclzGBLee3EZjopcAnYKnAyOZwl+gmwQmA+Ox77nI0hEgmAmvrj61Aq6M=';
	//支付宝公钥 用于验签
	private $alipay_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsboHIjwl0C8ihJMrT5n0pLsakOCFZIk8PjNtPbs/vZXL23WVXju8GSm/F1CKpycciGyLO1ApNCfKPfvUOHQcbj6W7afm2SBF6l0TnayTQZdrHwhICfAMxKFrn8pEyzCSoH7PGlOzBygWtlK0XBwTEQ1S3TRQ1rSsDer4rHnGpR/Ai1NFJ63LRTYaIM7zjAMuvjsszgqQKBQqn85caqmJrMWegR/lGIpkneg9MTSyek+pcH3U03GUvBu4BKU/AUqvMbLNQO/tlW9lQIGruCRiP3lZ5xTBsW/tnDw1/OJKfaloCuvIUIjF+1HheDPuZLg+stpAZiznVKyJcjrslNxtbwIDAQAB';
	
	function __construct() {
        parent::__construct();
    }
    
    public function phpinfo(){
		phpinfo();
	}
	
    public function generateOrderInfo1(){
		echo json_encode(array(
		    'result' => 'app_id=2015052600090779&biz_content=%7B%22timeout_express%22%3A%2230m%22%2C%22seller_id%22%3A%22%22%2C%22product_code%22%3A%22QUICK_MSECURITY_PAY%22%2C%22total_amount%22%3A%220.01%22%2C%22subject%22%3A%221%22%2C%22body%22%3A%22%E6%88%91%E6%98%AF%E6%B5%8B%E8%AF%95%E6%95%B0%E6%8D%AE%22%2C%22out_trade_no%22%3A%22IQJZSRC1YMQB5HU%22%7D&charset=utf-8&format=json&method=alipay.trade.app.pay&notify_url=http%3A%2F%2Fdomain.merchant.com%2Fpayment_notify&sign_type=RSA2&timestamp=2016-08-25%2020%3A26%3A31&version=1.0&sign=cYmuUnKi5QdBsoZEAbMXVMmRWjsuUj%2By48A2DvWAVVBuYkiBj13CFDHu2vZQvmOfkjE0YqCUQE04kqm9Xg3tIX8tPeIGIFtsIyp%2FM45w1ZsDOiduBbduGfRo1XRsvAyVAv2hCrBLLrDI5Vi7uZZ77Lo5J0PpUUWwyQGt0M4cj8g%3D'
		));
	}
	
	public function generateOrderInfo(){
		$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "third_party" . DIRECTORY_SEPARATOR . 'aop' . DIRECTORY_SEPARATOR;
		include($dir . "AopClient.php");
		include($dir . "request/AlipayTradeAppPayRequest.php");
		$aop = new AopClient;
		$aop->gatewayUrl = $this->gatewayUrl;
		$aop->appId = $this->appid;
		$aop->rsaPrivateKey = $this->app_private_key;  //请填写开发者私钥去头去尾去回车，一行字符串
		$aop->format = "json";
		$aop->charset = "UTF-8";
		$aop->signType = "RSA2";
		$aop->alipayrsaPublicKey = $this->alipay_public_key;  //请填写支付宝公钥，一行字符串
		
		$out_trade_no = $this->createRandNum();
		//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
		$request = new AlipayTradeAppPayRequest();
		//SDK已经封装掉了公共参数，这里只需要传入业务参数
		$bizcontent = "{\"body\":\"我是太阳数据\","
		                . "\"subject\": \"App支付\","
		                . "\"out_trade_no\": \"".$out_trade_no."\","
		                . "\"timeout_express\": \"30m\","
		                . "\"total_amount\": \"0.01\","
		                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
		                . "}";
		$request->setNotifyUrl(base_url('alipay/pay_notify'));
		$request->setBizContent($bizcontent);
		//这里和普通的接口调用不同，使用的是sdkExecute
		$response = $aop->sdkExecute($request);
		//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
		echo json_encode(array(
		    'result' => $response //就是orderString 可以直接给客户端请求，无需再做处理。
		));
	}
	
    public function pay_notify(){
    	$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "third_party" . DIRECTORY_SEPARATOR . 'aop' . DIRECTORY_SEPARATOR;
		include($dir . "AopClient.php");
		$aop = new AopClient;
		$aop->alipayrsaPublicKey = $this->alipay_public_key;
		log_message('info', 'APP支付结果通知：'.var_export($_POST, true));
		$flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
		if($flag){
			log_message('info', '000000000000000');
			if($_POST['out_trade_no'] && $_POST['total_amount'] && $_POST['seller_id'] && $_POST['app_id']){
				log_message('info', '1111111111');
				echo 'success';
			}
			else{
				log_message('info', '222222222222');
				echo 'failure';
			}
		}
		else{
			log_message('info', '33333333333333');
			echo 'failure';
		}
	}
	
    private function createRandNum($str = '')
    {
        return date('YmdHis') . rand(100000, 999999) . $str;
    }
}