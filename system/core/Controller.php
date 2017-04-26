<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {
	
	protected $user;
	protected $userService;
	protected $admin;
	protected $merchant;
	protected $env = 'dev';
	protected $systemInfo;

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
		$this->getSystemInfo();
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}
	
   /**
	* curl post方法...
	*/
	public function curl_post($url, $data){ // 模拟提交数据函数
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	    $tmpInfo = curl_exec($curl); // 执行操作
	    if (curl_errno($curl)) {
	        //echo 'Errno'.curl_error($curl);//捕抓异常
	        curl_close($curl); // 关闭CURL会话
	        return null;
	    }
	    else{
		    curl_close($curl); // 关闭CURL会话
		    $result = json_decode($tmpInfo);
		    if($result && isset($result->errcode) && $result->errcode == 40001){
		        $fileName = dirname(dirname(dirname(__FILE__)))."/application/models/access_token.json";
				if(file_exists($fileName)){
					log_message('info', '有40001错误值，accesstoken清空');
		            file_put_contents($fileName, '');
		            $this->curl_post($url, $data);
		        }
		    }
		    return $result; // 返回数据
	    }
	}
	
   /**
	* curl post方法...
	* 设置timeout时间为1秒，也就是说，客户端至少必须等待1秒钟
	*/
	public function curl_post_timeout_short($url,$data){ // 模拟提交数据函数
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	    curl_setopt($curl, CURLOPT_TIMEOUT, 1); // 设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	    $tmpInfo = curl_exec($curl); // 执行操作
	    if (curl_errno($curl)) {
	        //echo 'Errno'.curl_error($curl);//捕抓异常
	        curl_close($curl); // 关闭CURL会话
	        return null;
	    }
	    else{
		    curl_close($curl); // 关闭CURL会话
		    return json_decode($tmpInfo); // 返回数据
	    }
	}
	
   /**
	* curl get方法...
	*/
	public function curl_get($url){ // 模拟提交数据函数
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// 要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	// https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output);
	    if($result && isset($result->errcode) && $result->errcode == 40001){
	        $fileName = dirname(dirname(dirname(__FILE__)))."/application/models/access_token.json";
			if(file_exists($fileName)){
				log_message('info', '有40001错误值，accesstoken清空');
	            file_put_contents($fileName, '');
	            $this->curl_get($url);
	        }
	    }
		return $result;
	}
	
    public function arrayToObject($e){
	    if( gettype($e)!='array' ) return;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='array' || getType($v)=='object' )
	            $e[$k]=(object)arrayToObject($v);
	    }
	    return (object)$e;
	}
	 
	public function objectToArray($e){
	    $e=(array)$e;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='resource' ) return;
	        if( gettype($v)=='object' || gettype($v)=='array' )
	            $e[$k]=(array)($this->objectToArray($v));
	    }
	    return $e;
	}
	
   /**
	 * 获取客户端IP...
	 */
	function get_client_ip()
	{
		if ($_SERVER['REMOTE_ADDR']) {
		    $cip = $_SERVER['REMOTE_ADDR'];
		} 
		elseif (getenv("REMOTE_ADDR")) {
		    $cip = getenv("REMOTE_ADDR");
		} 
		elseif (getenv("HTTP_CLIENT_IP")) {
		    $cip = getenv("HTTP_CLIENT_IP");
		} 
		else {
		    $cip = "133.126.181.12";
		}
		return $cip;
	}
	
    protected function auth_json_admin()
	{
		$userInfo = $this->session->admin;
    	if(!$userInfo){
		    $return = array("code"=>9999, "message"=>"请先登录");
		    echo json_encode($return);
		    exit();
    	}
	    else{
    		$this->admin = $userInfo;
    	}
	}
	
    protected function auth_json_merchant()
	{
		$userInfo = $this->session->merchant;
    	if(!$userInfo){
		    $return = array("code"=>9999, "message"=>"请先登录");
		    echo json_encode($return);
		    exit();
    	}
	    else{
    		$this->merchant = $userInfo;
    	}
	}
	
    protected function auth_json_member()
	{
		$userInfo = $this->session->user;
    	if(!$userInfo){
		    $return = array("code"=>9999, "message"=>"请先登录");
		    echo json_encode($return);
		    exit();
    	}
	    else{
	    	$this->user = $userInfo;
	    	$type = 'member';
	    	$token = $this->getToken($userInfo['id'], $type);
	    	if($token != $userInfo['token']){
	    		$this->user = '';
	    		$this->session->user = '';
	    		$return = array("code"=>8888, "message"=>"您的账号已在其他地方登录");
			    echo json_encode($return);
			    exit();
	    	}
    	}
	}
	
    protected function auth_json_member_or_service()
	{
		$memberInfo = $this->session->user;
		$serviceInfo = $this->session->service;
		
    	if(!$memberInfo && !$serviceInfo){
		    $return = array("code"=>9999, "message"=>"请先登录");
		    echo json_encode($return);
		    exit();
    	}
	    if($memberInfo){
    		$this->user = $memberInfo;
	        $type = 'member';
	    	$token = $this->getToken($memberInfo['id'], $type);
	    	if($token != $memberInfo['token']){
	    		$this->user = '';
	    		$this->session->user = '';
	    		$return = array("code"=>8888, "message"=>"您的账号已在其他地方登录");
			    echo json_encode($return);
			    exit();
	    	}
    	}
	    if($serviceInfo){
    		$this->userService = $serviceInfo;
	        $type = 'service';
	    	$token = $this->getToken($serviceInfo['id'], $type);
	    	if($token != $serviceInfo['token']){
	    		$this->userService = '';
	    		$this->session->service = '';
	    		$return = array("code"=>8888, "message"=>"您的账号已在其他地方登录");
			    echo json_encode($return);
			    exit();
	    	}
    	}
	}
	
    protected function auth_json_service()
	{
		$userInfo = $this->session->service;
    	if(!$userInfo){
		    $return = array("code"=>9999, "message"=>"请先登录");
		    echo json_encode($return);
		    exit();
    	}
	    else{
    		$this->userService = $userInfo;
	        $type = 'service';
	    	$token = $this->getToken($userInfo['id'], $type);
	    	if($token != $userInfo['token']){
	    		$this->userService = '';
	    		$this->session->service = '';
	    		$return = array("code"=>8888, "message"=>"您的账号已在其他地方登录");
			    echo json_encode($return);
			    exit();
	    	}
    	}
	}
	
    protected function auth_redirect_member()
	{
		$userInfo = $this->session->user;
    	if(!$userInfo){
		    $url = "Location: /member/login";
    	    header($url);
		    exit();
    	}
    	else{
    		$this->user = $userInfo;
    	    $type = 'member';
	    	$token = $this->getToken($userInfo['id'], $type);
	    	if($token != $userInfo['token']){
	    		$this->user = '';
	    		$this->session->user = '';
	    		$url = "Location: /member/login";
	    	    header($url);
			    exit();
	    	}
    	}
	}
	
    protected function auth_redirect_service()
	{
		$userInfo = $this->session->service;
    	if(!$userInfo){
		    $url = "Location: /service/login";
    	    header($url);
		    exit();
    	}
    	else{
    		$this->userService = $userInfo;
    	    $type = 'service';
	    	$token = $this->getToken($userInfo['id'], $type);
	    	if($token != $userInfo['token']){
	    		$this->userService = '';
	    		$this->session->service = '';
	    		$url = "Location: /service/login";
	    	    header($url);
			    exit();
	    	}
    	}
	}

	/*
	 * 加密
	 */
	protected function encrypt($data, $key)  
	{  
		$str = '';
		$char = '';
	    $key    =   md5($key);  
	    $x      =   0;  
	    $len    =   strlen($data);  
	    $l      =   strlen($key);  
	    for ($i = 0; $i < $len; $i++)  
	    {  
	        if ($x == $l)   
	        {  
	            $x = 0;  
	        }  
	        $char .= $key{$x};  
	        $x++;  
	    }  
	    for ($i = 0; $i < $len; $i++)  
	    {  
	        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);  
	    }  
	    return base64_encode($str);  
	}  
	
	/*
	 * 解密
	 */
	protected function decrypt($data, $key)  
	{  
		$str = '';
		$char = '';
	    $key = md5($key);  
	    $x = 0;  
	    $data = base64_decode($data);  
	    $len = strlen($data);  
	    $l = strlen($key);  
	    for ($i = 0; $i < $len; $i++)  
	    {  
	        if ($x == $l)   
	        {  
	            $x = 0;  
	        }  
	        $char .= substr($key, $x, 1);  
	        $x++;  
	    }  
	    for ($i = 0; $i < $len; $i++)  
	    {  
	        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))  
	        {  
	            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));  
	        }  
	        else  
	        {  
	            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));  
	        }  
	    }  
	    return $str;  
	}
	
	protected function getPageUrl(){  
	    $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://'; 
	    $url .= $_SERVER['HTTP_HOST'];  
	    $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
	    return $url;  
	}
	
	protected function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
    
    /*
     * json方式输出通信数据
     * @param integer $code
     * @param string $message
     * @param array $data
     * return string
     */
    protected function json($code, $message, $data=array())
    {
    	if(!is_numeric($code)){
    		return '';
    	}
    	else{
    		$result = array(
    		    "code" => $code,
	    		"message" => $message,
	    		"data" => $data
    		);
    	}
    	
    	echo json_encode($result);
    	exit;
    }
    
    /*
     * 按字典序排序参数
     * 请求参数合成字符串
     * $param为请求参数
     * $token和$uid通过https协议后的登录操作获取，然后保存在客户端
     * 用户退出登录时清空token
     * 参数里添加uid和timestamp uid后台获取token timestamp验证时效
     */
    protected function app_sign($param, $token, $uid, $urlencode=false)
    {
    	//添加时间戳，隔太久失效，防止被截取重复调用
    	$param['timestamp'] = time();
    	$param['uid'] = $uid;  //token不传输，后台通过uid获取token
    	
    	$buff = "";
		ksort($param);
		foreach ($param as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		
		$string = '';
		if (strlen($buff) > 0) 
		{
			//签名步骤一：按字典序排序参数
			$string = substr($buff, 0, strlen($buff)-1);
		}
		
		//签名步骤二：在string后加入KEY
		$string = $string."&key=".$token;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$res_sign = strtoupper($string);
		return $res_sign;
    }
    
    protected function setToken($uid, $type){
        $this->load->driver('cache',  array('adapter' => 'memcached', 'backup' => 'file') ); 
        $token = '';		
		if ($this->cache->memcached->is_supported()){
			$token = md5(time().'goodluck'.$uid.$type);
			$key = $uid.'_'.$type;
			$this->cache->save($key, $token, 2 * 60 * 60); //2h
		}
		
		return $token;
    }
    
    protected function getToken($uid, $type){
        $this->load->driver('cache',  array('adapter' => 'memcached', 'backup' => 'file') ); 	
        $value = '';	
		if ($this->cache->memcached->is_supported()){
			$key = $uid.'_'.$type;
	        $value = $this->cache->get($key);
		}
		
		return $value;
    }
    
    protected function deleteToken($uid, $type){
        $this->load->driver('cache',  array('adapter' => 'memcached', 'backup' => 'file') ); 
		if ($this->cache->memcached->is_supported()){
			$key = $uid.'_'.$type;
			$this->cache->delete($key);
		}
    }
    
    protected function getSystemInfo()
	{
		$this->load->model('system_model');
		$systemData = $this->system_model->getSystemData();
		$this->systemInfo = $systemData;
	}
}
