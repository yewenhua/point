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
 * Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		log_message('info', 'Model Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * __get magic
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string	$key
	 */
	public function __get($key)
	{
		// Debugging note:
		//	If you're here because you're getting an error message
		//	saying 'Undefined Property: system/core/Model.php', it's
		//	most likely a typo in your model code.
		return get_instance()->$key;
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

}
