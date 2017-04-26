<?php
class Weixin_model extends CI_Model {
	
	private $appId = null;
	private $appSecret = null;
	private $accessToken = null;
	
	const SNSAPI_BASE = 'snsapi_base';
    const SNSAPI_INFO = 'snsapi_userinfo';
    
	public function __construct() {
		parent::__construct();
		$this->appId = $this->config->item('appId');
        $this->appSecret = $this->config->item('appSecret');
	}
    
   /**
    * 官方access_token有效时间为7200S， 本例设置为6000S，过期后再去重新获取
	* 获取accesstoken...
	* 若正确返回access_token，否则返回null
	*/
	public function getAccessToken() {
	    $data = json_decode(file_get_contents(dirname(__FILE__)."/access_token.json"));
        if ($data == null || empty($data) || $data->expire_time < time()) {
	        if ($data == null || empty($data)){
	        	log_message('info', 'accesstoken为空');
	    		$data = (object)array();
	    	}

			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appSecret;
			$result = $this->curl_get($url);
			if($result && isset($result->access_token)){
				log_message('info', '请求接口成功');
			    $accessToken = $result->access_token;
			    $data->expire_time = time() + 6000;
                $data->access_token = $accessToken;
                $fp = fopen(dirname(__FILE__)."/access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
			}
			else{
				log_message('info', '请求接口失败');
				$accessToken = null;
				$fileName = dirname(__FILE__)."/access_token.json";
				if(file_exists($fileName)){
					log_message('info', 'accesstoken清空');
		            file_put_contents($fileName, '');
		        }
			}
        } 
        else {
        	log_message('info', 'accesstoken有效');
            $accessToken = $data->access_token;
        }
        	
		return $accessToken;
	}
    
    /**
	* 创建自定义菜单...
	*/
    public function createMenu() {
    	header("content-type:text/html; charset=UTF-8");
    	$access_token = $this->getAccessToken();
    	if($access_token != null){
    	    $menu_body = $this->config->item('wechat_menu_array');
	        //https请求
	    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
	        $return = $this->curl_post($url, json_encode($menu_body, JSON_UNESCAPED_UNICODE));
	        if(isset($return->errcode) && $return->errcode != 0){
	        	return '抱歉，菜单生成出错，错误代码为：'.$return->errcode;
	        }
            else{
            	return '恭喜你，菜单已生成';
            }
    	}
    	else{
    		return 'access_token 错误！';
    	}
    } 

    /**
     * 生成网页授权跳转地址 ，用户同意授权，获取code...
     * @param $redirect_uri
     * @param $stateArr
     * @param $scope
     */
    private function oauth_url($redirect_uri, $state, $scope)
    {  
        $urlparam = array(
            'appid=' . $this->appId,
            'redirect_uri=' . urlencode($redirect_uri),
            'response_type=code',
            'scope=' . $scope,
            'state=' . $state,
        );
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . join("&", $urlparam) . "#wechat_redirect";
    }

    /**
     * snsapi_base为scope发起的网页授权，是用来获取进入页面的用户的openid的 ...
     * @param $redirect_uri
     * @param $state
     * @return code ...
     */
    public function set_oauth_snsapi_base($redirect_uri, $state)
    {
        return $this->oauth_url($redirect_uri, $state, self::SNSAPI_BASE);
    }

    /**
     * snsapi_userinfo为scope发起的网页授权，是用来获取用户的基本信息的 ...
     * @param $redirect_uri
     * @param $state
     * @return code ...
     */
    public function set_oauth_snsapi_userinfo($redirect_uri, $state)
    {
        return $this->oauth_url($redirect_uri, $state, self::SNSAPI_INFO);
    }
    
   /**
     * 从微信API获取,通过code换取网页授权access_token等信息
     * @param $code string api返回的code
     * @return array access_token、expires_in、refresh_token、savetime等信息
     */
    public function getOauthInfoByCode($code)
    {
        $urlparam = array(
            'appid=' . $this->appId,
            'secret=' . $this->appSecret,
            'code=' . $code,
            'grant_type=authorization_code',
        );
        $apiUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?" . join("&", $urlparam);
        $json = file_get_contents($apiUrl);
        $result = json_decode($json);
        
        if(isset($result->errcode) && $result->errcode != 0){
			return null;
		}
		else{
            return $this->objectToArray($result);
		}
    }
    
   /**
     * 刷新access_token
     * @param string $refresh_token 填写通过access_token获取到的refresh_token参数
     * @return array用户基本数据
     */
    private function refreshAccessToken($refresh_token)
    {
        $urlparam = array(
            'appid=' . $this->appId,
            'grant_type=refresh_token',
            'refresh_token=' .$refresh_token,
        );
        $apiUrl = "https://api.weixin.qq.com/sns/oauth2/refresh_token?" . join("&", $urlparam);
        $json = file_get_contents($apiUrl);

        $result = json_decode($json);
        if(isset($result->errcode) && $result->errcode != 0){
			return null;
		}
		else{
            return $result;
		}
    }
    
	/**
     * 网页授权情况下  根据openid获取用户信息
     * @param string $openid 用户openid
     * @param string $accessToken 通过code获取的accessToken
     * @return array用户基本数据
     */
    public function getUserInfoByOauth($openid, $accessToken)
    {
        $urlparam = array(
            'access_token=' . $accessToken,
            'openid=' . $openid,
            'lang=zh_CN',
        );
        $apiUrl = "https://api.weixin.qq.com/sns/userinfo?" . join("&", $urlparam);
        $json = file_get_contents($apiUrl);
        $result = json_decode($json);

        if(isset($result->errcode) && $result->errcode != 0){
			return null;
		}
		else{
			return $this->objectToArray($result);
		}
    }
    
	/**
     * 非网页授权情况下  根据openid获取用户信息
     * @param string $openid 用户openid
     * @param string $accessToken 通过code获取的accessToken
     * @return array用户基本数据
     */
    public function getUserInfo($openid)
    {
    	$accessToken = $this->getAccessToken();
        $urlparam = array(
            'access_token=' . $accessToken,
            'openid=' . $openid,
            'lang=zh_CN',
        );
        $apiUrl = "https://api.weixin.qq.com/cgi-bin/user/info?" . join("&", $urlparam);
        $json = file_get_contents($apiUrl);
        $result = json_decode($json);

        if(isset($result->errcode) && $result->errcode != 0){
			return null;
		}
		else{
			return $this->objectToArray($result);
		}
    }
    
    /**
     * 发送客服消息
     * @param $postData
     */
    public function sendCustomMessage($postData)
    {
    	header("content-type:text/html; charset=UTF-8");
    	log_message('info', '发送信息：'.var_export($postData, true));
    	$access_token = $this->getAccessToken();
        if($access_token != null){    	    
	    	$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	    	$postData = $this->json_encode_ex($postData);
	        $return = $this->curl_post($url, $postData);
	        if(isset($result->errcode) && $result->errcode != 0){
	        	return false;
	        }
            else{
            	return true;
            }
    	}
    	else{
    		return false;
    	}
    }
    
  /**
	* 对变量进行 JSON 编码
	* @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
	* @return string 返回 value 值的 JSON 形式
	*/
	function json_encode_ex($value)
	{
	    if (version_compare(PHP_VERSION,'5.4.0','<'))
	    {
	        $str = $this->encode_json($value);
	        return $str;
	    }
	    else
	    {
	        return json_encode($value, JSON_UNESCAPED_UNICODE);
	    }
	}
	
	//5.3之前中文转码
	function encode_json($str) {  
	    return urldecode(json_encode($this->url_encode($str)));      
	}  
	  
	/** 
	 *  
	 */  
	function url_encode($str) {  
	    if(is_array($str)) {  
	        foreach($str as $key=>$value) {  
	            $str[urlencode($key)] = $this->url_encode($value);  
	        }  
	    } else {  
	        $str = urlencode($str);  
	    }  
	      
	    return $str;  
	} 
    
    /**
     * 模板消息接口   设置所属行业
     * @param $postData {"industry_id1":"1", "industry_id2":"4"}
     */
    public function setIndustry($postData)
    {
    	$access_token = $this->getAccessToken();
        if($access_token != null){    	    
	    	$url = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token='.$access_token;
	        $return = $this->curl_post($url, $postData);
	        if(isset($result->errcode) && $result->errcode != 0){
	        	return true;
	        }
            else{
            	return false;
            }
    	}
    	else{
    		return false;
    	}
    }
    
    /**
     * 模板消息接口   获得模板ID
     * @param $postData  {"template_id_short":"TM00015"}
     * @return {template_id ...}
     */
    public function addTemplate($postData)
    {
    	$access_token = $this->getAccessToken();
        if($access_token != null){    	    
	    	$url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$access_token;
	        $return = $this->curl_post($url, $postData);
	        if(isset($result->errcode) && $result->errcode != 0){
	        	return true;
	        }
            else{
            	return false;
            }
    	}
    	else{
    		return false;
    	}
    }
    
    /**
     * 模板消息接口   发送模板消息
     * @param $postData
     */
    public function sendTemplateMessage($postData)
    {
    	$access_token = $this->getAccessToken();
        if($access_token != null){    	    
	    	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
	        $return = $this->curl_post($url, $postData);
	        if(isset($result->errcode) && $result->errcode != 0){
	        	return true;
	        }
            else{
            	return false;
            }
    	}
    	else{
    		return false;
    	}
    }
    
    /**
     * 新增临时素材
     * @param $type image voice video thumb
     */
    public function uploadMediaFile($media_path, $type)
    {
    	$access_token = $this->getAccessToken();
        if($access_token != null){    	    
	    	$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
	        $curl = curl_init ();
	        curl_setopt ( $curl, CURLOPT_URL, $url );
	        curl_setopt ( $curl, CURLOPT_POST, 1 );
	        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	        curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
	        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
	        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
	
	        $curlfile = curl_file_create($media_path);
	        $postdata = array('media' => $curlfile);
	        
	        curl_setopt($curl, CURLOPT_POST,1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	        curl_setopt($curl, CURLOPT_INFILESIZE,filesize($media_path));
	        $return = curl_exec ( $curl );
	
	        if(curl_errno($curl)==0){
	            curl_close($curl);
	            $result = json_decode($return, true);
	            if(isset($result['errcode']) && $result['errcode'] != 0){
	                return null;
	            }
	            else{
	                return $result['media_id'];
	            }
	        }else {
	            return null;
	        }
    	}
    	else{
    		return null;
    	}
    }
    
    /**
     * sendPicMsgByOpenid ...
     * @param $open_id
     * @param $media_id
     */
    public function sendPicMsgByOpenid($open_id,$media_id){
        $postData = array(
            'touser'=>$open_id,
            'msgtype'=>'image',
            'image'=>array(
                'media_id'=>$media_id
            ),
        );
        return $this->sendCustomMessage($postData);
    }
    
    public function getAddressParameters($url, $accesstoken, &$data)
    {
    	//签名过程中所有参数名均为小写字符，例如appId 在排序后字符串则为appid，签名五个参数
        $data = array();
        $data["appid"] = $this->appId;
        $data["url"] = $url;  //调用JavaScript API的网页url
        $time = time();
        $data["timestamp"] = "$time";
        $noncestr = rand(1000000, 9999999);
        $data["noncestr"] = "$noncestr";
        $data["accesstoken"] = $accesstoken;  //获取accessToken使用网页授权接口，获取accessToken的scope是snsapi_base
        ksort($data);
        $params = $this->toAddressUrlParams($data);
        $addrSign = sha1($params);

        $parameters = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $data["appid"],
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        return $parameters;
    }
    
    private function toAddressUrlParams($urlObj)
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
    
   /**
    * 官方jsapi_ticket有效时间为7200S， 本例设置为6000S，过期后再去重新获取
	* 获取jsapi_ticket...
	* 若正确返回jsapi_ticket，否则返回null
	*/
	public function getJsapiTicket() {
	    $data = json_decode(file_get_contents(dirname(__FILE__)."/jsapi_ticket.json"));
        if ($data == null || empty($data) || $data->expire_time < time()) {
	        if ($data == null || empty($data)){
	        	log_message('info', 'jsapi_ticket为空');
	    		$data = (object)array();
	    	}

			$accesstoken = $this->getAccessToken();
			if($accesstoken !== null){
	    	    $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accesstoken.'&type=jsapi';
				$result = $this->curl_get($url);
				if($result && $result->errcode == 0 &&  isset($result->ticket)){
					log_message('info', 'getJsapiTicket请求接口成功');
				    $jsapi_ticket = $result->ticket;
				    $data->expire_time = time() + 6000;
	                $data->jsapi_ticket = $jsapi_ticket;
	                $fp = fopen(dirname(__FILE__)."/jsapi_ticket.json", "w");
	                fwrite($fp, json_encode($data));
	                fclose($fp);
				}
				else{
					log_message('info', 'getJsapiTicket请求接口失败');
					$jsapi_ticket = null;
					$fileName = dirname(__FILE__)."/jsapi_ticket.json";
					if(file_exists($fileName)){
						log_message('info', 'jsapi_ticket清空');
			            file_put_contents($fileName, '');
			        }
				}
			}
			else{
				log_message('info', 'accesstoken请求接口失败');
				$jsapi_ticket = null;
			}
        } 
        else {
        	log_message('info', 'jsapi_ticket有效');
            $jsapi_ticket = $data->jsapi_ticket;
        }
        	
		return $jsapi_ticket;
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