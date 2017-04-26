<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

define("TOKEN","14ed1a22176d3805f01deeab4c7aae03");
class Agent extends CI_Controller {
	private $_USERDATA = array();
	private $_IS_SERVER = true;
	private $_VERSION = 1.0;
	
	function __construct() {
        parent::__construct();
        $this->load->model('agent_model');
        $this->load->model('tencent_model');
        $this->_VERSION = date('mdh', time());
    }
    
    /**
	* 微信接口认证与执行...
	*/
    public function main(){
    	//初始化时
        if(isset($_GET["echostr"])){
            if($this->checkSignature()){
                echo $_GET["echostr"];
            }
            else{
                echo '验证失败';
            }
            exit;
        }
        else{
        	//非初始化，已经认证过时
        	$this->run();
        }
    }
    
   /**
	* 微信主运行方法...
	*/
    public function run() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->_USERDATA['fromusername'] = $postObj->FromUserName;
            $this->_USERDATA['tousername'] = $postObj->ToUserName;
            $this->_USERDATA['msgtype'] = $postObj->MsgType;
            $openid = $postObj->FromUserName;
            $param = $this->objectToArray($postObj);
            switch ($this->_USERDATA['msgtype']) {
                case 'event':
                    $this->_USERDATA['event'] = strtolower($postObj->Event);

                    if ($this->_USERDATA['event'] == 'subscribe') {
                    	if(isset($postObj->EventKey)){
                    		//用户未关注时，扫码关注事件
                    		$this->_USERDATA['event_key'] = $postObj->EventKey;
                    		$this->_USERDATA['ticket'] = $postObj->Ticket; //二维码的ticket，可用来换取二维码图片
                    	}
                    	else{
                    		//用户未关注时，非扫码关注事件
                    	}
                    	
                    	if(is_array($param['EventKey']) && empty($param['EventKey'])){
                    	    $param['EventKey'] = '';
                    	}
                    	log_message('info', 'subscribe参数：'.var_export($param, true));
                		$return = $this->subscribe($param);
                    	log_message('info', 'subscribe结果：'.var_export($return, true));
                    	if($return && isset($return['code']) && $return['code'] == 0){
                    	    $this->welcome_msg($return['message']);
                    	}
                    	else{
                    		$this->welcome_msg();
                    	}
                    }
                    elseif ($this->_USERDATA['event'] == 'unsubscribe') {
                    	//取消关注事件
                    	$return = $this->unsubscribe($param);
                    }
                    elseif ($this->_USERDATA['event'] == 'scan') {
                    	//用户已关注时的扫码事件推送
                    	$this->_USERDATA['event_key'] = $postObj->EventKey;
                    	$this->_USERDATA['ticket'] = $postObj->Ticket;  //二维码的ticket，可用来换取二维码图片
                		$return = $this->scan($param);
                    	log_message('info', '关注后扫码结果：'.var_export($return, true));
                        if($return && isset($return['code']) && $return['code'] == 0){
                    	    $this->returnTxt('十分感谢您持续关注我们');
                    	}
                    }
                    elseif ($this->_USERDATA['event'] == 'location') {
                    	//上报地理位置事件，每次进入公众号会话时
                    }
                    elseif ($this->_USERDATA['event'] == 'view') {
                    	//点击菜单跳转链接时的事件推送
                    }
                    elseif ($this->_USERDATA['event'] == 'click') {
                    	//点击菜单拉取消息时的事件推送
                    	$this->_USERDATA['event_key'] = $postObj->EventKey;
                        switch ($this->_USERDATA['event_key']) {
                            case 'V3001_HAIBAO': 
                            	$platformInfo = $this->agent_model->isOpenidExist($openid);
                            	if($platformInfo != null && $platformInfo['userid']){
			                    	//设置timeout时间为1秒防止长时间二维码未生成导致的重复调用该接口，也就是说，客户端至少必须等待1秒钟,不管生成结果客户端先发送消息
			                    	$url = base_url("index.php/wechat/sendHaibaoMsg");
			                    	$return = $this->objectToArray($this->curl_post_timeout_short($url, $param));
			                    	log_message('info', '海报二维码生成结果：'.var_export($return, true));
			                    	$this->returnTxt('海报生成中，稍后将发送至您的微信，请注意查收');
                            	}
                            	else{
                            		$url = base_url("index.php/wechat/makemoney");
	                                $return = array(
							            'title' => '抱歉，您还未注册',
							            'desc' => '点击注册~',
							            'picurl' => base_url("backend_media/img/caishen.png"),
							            'url' => $url
							        );
							        $this->returnNews($return);
                            	}
                                break;
                            case 'V1001_PRO': 
                            	//单图文消息
                            	$url = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942415&idx=1&sn=f5b9a0a0adab47676e0e91cd71cd56b7#rd";
                                $return = array(
						            'title' => '公司介绍',
						            'desc' => '沪深通投资管理有限公司是一家集资产管理、投资管理、财富管理、股指期货等业务于一体的综合性现代服务行业',
						            'picurl' => base_url("wechat/img/hst.jpg"),
						            'url' => $url
						        );
						        $this->returnNews($return);
                                break;
                            case 'V1002_ANNOUNCE':   
                            	//单图文消息
                            	$url = base_url("index.php/wechat/announce");
                                $return = array(
						            'title' => '平台公告',
						            'desc' => '公告列表！点我~',
						            'picurl' => base_url("wechat/img/gonggao.jpg"),
						            'url' => $url
						        );
						        $this->returnNews($return);
                                break;
                            case 'V1003_JOINUS':
                            	//多图文消息         	
                                $urlOne = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942587&idx=1&sn=b6c980edd0c9b621093a18104baae6df&scene=4#wechat_redirect";
                                $urlTwo = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942587&idx=2&sn=ce2453ea769c7af35d1313edad92210e&scene=4#wechat_redirect";
                                $urlThree = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942587&idx=3&sn=35cb2e0fc23bac3686792d801c332e3b&scene=4#wechat_redirect";
                                $urlFour = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942587&idx=4&sn=d412a0f8e32ee623b296ff5627b61ac7&scene=4#wechat_redirect";
                                $return = array(
	                                'a' => array(
								            'title' => '沪深通公司简介',
								            'desc' => '沪深通公司简介',
								            'picurl' => base_url("wechat/img/gsjj.jpg"),
								            'url' => $urlOne
	                                ),
	                                'b' => array(
								            'title' => '沪深通公司文化',
								            'desc' => '沪深通公司文化',
								            'picurl' => base_url("wechat/img/gswh.png"),
								            'url' => $urlTwo
	                                ),
	                                'c' => array(
								            'title' => '沪深通办公环境',
								            'desc' => '沪深通办公环境',
								            'picurl' => base_url("wechat/img/bghj.jpg"),
								            'url' => $urlThree
	                                ),
	                                'd' => array(
								            'title' => '加盟沪深通 携手财富盛宴',
								            'desc' => '加盟沪深通 携手财富盛宴',
								            'picurl' => base_url("wechat/img/joinus.jpg"),
								            'url' => $urlFour
	                                )
                                );
						        $this->returnManyNews($return);
                                break;
                            case 'V2001_HUATONG': 
                            	//多图文消息         	
                                $urlOne = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942500&idx=1&sn=78e1fb051fbdf3a397693ffad3bfc512&scene=0#wechat_redirect";
                                $urlTwo = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942500&idx=2&sn=22d8f0d0e652afc3213d91f9bb4d2aac&scene=0#wechat_redirect";
                                $urlThree = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942500&idx=3&sn=2c52cecc782eca5f43dc3cf68623b887&scene=0#wechat_redirect";
                                $urlFour = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942500&idx=4&sn=ab9a0cda3a793c39d72f447443615dd3&scene=0#wechat_redirect";
                                $return = array(
	                                'a' => array(
								            'title' => '白银"微交易" 重磅来袭',
								            'desc' => '白银"微交易" 重磅来袭',
								            'picurl' => base_url("wechat/img/671868734182602428.jpg"),
								            'url' => $urlOne
	                                ),
	                                'b' => array(
								            'title' => '白银升贴水1000',
								            'desc' => '白银升贴水1000',
								            'picurl' => base_url("wechat/img/334282314982667583.jpg"),
								            'url' => $urlTwo
	                                ),
	                                'c' => array(
								            'title' => '白银基差1000',
								            'desc' => '白银基差1000',
								            'picurl' => base_url("wechat/img/825361051520573637.jpg"),
								            'url' => $urlThree
	                                ),
	                                'd' => array(
								            'title' => '华通白银(排期)',
								            'desc' => '华通白银(排期)',
								            'picurl' => base_url("wechat/img/717364061130758491.jpg"),
								            'url' => $urlFour
	                                )
                                );
						        $this->returnManyNews($return);
                                break;
                            case 'V2002_OPEN': 
                            	//单图文消息
                            	$url = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942501&idx=1&sn=20765a9c3134d1b61e4ac4b0b31f6f74&scene=0#wechat_redirect";
                                $return = array(
						            'title' => '开户流程',
						            'desc' => '华通铂银开户流程介绍~',
						            'picurl' => base_url("wechat/img/open2.jpg"),
						            'url' => $url
						        );
						        $this->returnNews($return);
                                break;
                            case 'V2003_GUID': 
                            	//多图文消息         	
                                $urlOne = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942659&idx=1&sn=d9ee3213ca474c9a29fa8e94171e9e4e&scene=4#wechat_redirect";
                                $urlTwo = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942659&idx=2&sn=9a538e392cb4b3cdc3056668d3e35c4e&scene=4#wechat_redirect";
                                $urlThree = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942659&idx=3&sn=683500698b6e37d64da354d4e0b2c61a&scene=4#wechat_redirect";
                                $urlFour = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942659&idx=4&sn=43511a0cf3714747d015420f1e299491&scene=4#wechat_redirect";
                                $urlFive = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942659&idx=5&sn=a001f77b1536517c2feeb7f3e3836839&scene=4#wechat_redirect";
                                $urlSix = "http://mp.weixin.qq.com/s?__biz=MzAxNDE3NzAwNA==&mid=2451942659&idx=6&sn=ad995f8165f318a3c6f82b5bb337cbb8&scene=4#wechat_redirect";
                                $return = array(
                                    'a' => array(
                                            'title' => '新手操作指南',
                                            'desc' => '新手操作指南',
                                            'picurl' => base_url("wechat/img/21.jpg"),
                                            'url' => $urlOne
                                    ),
                                    'b' => array(
                                            'title' => '微信注册网络居间商',
                                            'desc' => '微信注册网络居间商',
                                            'picurl' => base_url("wechat/img/561300404673159203.png"),
                                            'url' => $urlTwo
                                    ),
                                    'c' => array(
                                            'title' => '开户注册华通铂银',
                                            'desc' => '开户注册华通铂银',
                                            'picurl' => base_url("wechat/img/22.jpg"),
                                            'url' => $urlThree
                                    ),
                                    'd' => array(
                                            'title' => '绑定签约银行卡',
                                            'desc' => '绑定签约银行卡',
                                            'picurl' => base_url("wechat/img/110458978309043419.png"),
                                            'url' => $urlFour
                                    ),
                                    'e' => array(
                                            'title' => 'PC端软件使用操作',
                                            'desc' => 'PC端软件使用操作',
                                            'picurl' => base_url("wechat/img/24.jpg"),
                                            'url' => $urlFive
                                    ),
                                    'f' => array(
                                            'title' => '手机端软件使用操作',
                                            'desc' => '手机端软件使用操作',
                                            'picurl' => base_url("wechat/img/25.jpg"),
                                            'url' => $urlSix
                                    )
                                );
                                $this->returnManyNews($return);
                                break;
                            default :
                                break;
                        }
                    }
                    break;
                case 'transfer_customer_service':
                	break;
                case 'image':
                    break;
                case 'link':
                    break;
                case 'voice':
                    break;
                case 'video':
                    break;
                case 'text':
                	$keyword = $postObj->Content;
                    //if (strstr($keyword, "投诉") || strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "有人在吗") || strstr($keyword, "在吗") || strstr($keyword, "有人吗")){
                    	//触发多客服模式
			            //$this->customerService($param);
			        //}   	
                    break;
                case 'location':
                    $this->_USERDATA['Location_X'] = $postObj->Location_X;
                    $this->_USERDATA['Location_Y'] = $postObj->Location_Y;
                    $this->_USERDATA['Scale'] = $postObj->Scale;
                    $this->_USERDATA['Label'] = $postObj->Label;
                    break;
                default:
                    break;
            }
        } else {
            echo "";
            exit;
        }
    }
    
    /**
	* 微信接口验证...
	*/
    private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

    /**
	* 微信关注欢迎信息...
	*/
    private function welcome_msg($msg='') {
    	if($msg){
    		$js = '{"content":"恭喜您，'.$msg.'！\n请和大家一起赚钱吧！\n\r微信白银微交易，赚钱妥妥的。\n\r点击菜单中的“一起赚钱”\n开始赚钱吧！"}';
    	}
    	else{
    		$js = '{"content":"恭喜您！\n请和大家一起赚钱吧！\n\r微信白银微交易，赚钱妥妥的。\n\r点击菜单中的“一起赚钱”\n开始赚钱吧！"}';
    	}
    	$js = json_decode($js, true);
        $this->returnTxt($js['content']);
    } 
    
    /**
	* 微信返回文本信息...
	*/
    private function returnTxt($string) {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        $msgType = "text";
        $contentStr = $string;
        $resultStr = sprintf($textTpl, $this->_USERDATA['fromusername'], $this->_USERDATA['tousername'], time(), $msgType, $contentStr);
        echo $resultStr;
        exit;
    }
    
   /**
	* 微信图片信息...
	*/
    private function returnImage($media_id) {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <MediaId ><![CDATA[%s]]></MediaId >
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        $msgType = "image";
        $contentStr = $media_id;
        $resultStr = sprintf($textTpl, $this->_USERDATA['fromusername'], $this->_USERDATA['tousername'], time(), $msgType, $contentStr);
        echo $resultStr;
        exit;
    }

    /**
	* 微信返回图文信息...
	*/
    private function returnNews($array) {
        if (!empty($array) && $array!= null) {
            $str = '';
            $title = $array['title'];
            $desc = $array['desc'];
            $picurl = $array['picurl'];
            $url = $array['url'];
            $str .= "<item>
                     <Title><![CDATA[".$title."]]></Title>
                     <Description><![CDATA[".$desc."]]></Description>
                     <PicUrl><![CDATA[".$picurl."]]></PicUrl>
                     <Url><![CDATA[".$url."]]></Url>
                     </item>";
        } else {
            exit;
        }

        $textTpl = " <xml>
                    <ToUserName><![CDATA[" . $this->_USERDATA['fromusername'] . "]]></ToUserName>
                    <FromUserName><![CDATA[" . $this->_USERDATA['tousername'] . "]]></FromUserName>
                    <CreateTime>" . time() . "</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>

                    <ArticleCount>1</ArticleCount>

                    <Articles>

                            " . $str . "

                    </Articles>
                    </xml> ";
        echo $textTpl;
        exit;
    }
    
    /**
	* 微信返回多图文信息...
	*/
    private function returnManyNews($array) {
    	$count = 0;
        if (!empty($array) && $array!= null) {
            $str = '';
            foreach($array as $item){
	            $title = $item['title'];
	            $desc = $item['desc'];
	            $picurl = $item['picurl'];
	            $url = $item['url'];
	            $str .= "<item>
	                     <Title><![CDATA[".$title."]]></Title>
	                     <Description><![CDATA[".$desc."]]></Description>
	                     <PicUrl><![CDATA[".$picurl."]]></PicUrl>
	                     <Url><![CDATA[".$url."]]></Url>
	                     </item>";
            }
            $count = count($array);
        } else {
            exit;
        }

        $textTpl = " <xml>
                    <ToUserName><![CDATA[" . $this->_USERDATA['fromusername'] . "]]></ToUserName>
                    <FromUserName><![CDATA[" . $this->_USERDATA['tousername'] . "]]></FromUserName>
                    <CreateTime>" . time() . "</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>

                    <ArticleCount>" . $count . "</ArticleCount>

                    <Articles>

                            " . $str . "

                    </Articles>
                    </xml> ";
        echo $textTpl;
        exit;
    }
    
    public function subscribe($param){
     	$msgData = array(
     	    'EventKey'=>$param['EventKey'],
     	    'FromUserName'=>$param['FromUserName'],
     	);
        $eventkey = isset($msgData['EventKey']) ? $msgData['EventKey'] : '';
        $openid = $msgData['FromUserName'];
        $userInfo = $this->tencent_model->getUserInfo($openid);
        if(isset($msgData['EventKey']) && is_string($eventkey) && strpos($eventkey, "qrscene_") === 0){
        	//扫码推荐用户二维码关注
            $sys_key = str_replace('qrscene_', '', $eventkey);
            if(strpos($sys_key, "userTg-") === 0){
            	//分享个人海报二维码关注
                $scene_str = $sys_key;
                $scene_str = explode('-', $scene_str);
                $user_id = $scene_str[1];//推广者用户user_id
                $recommendMember = $this->user_model->getUserById($user_id);
                //log_message('info', '推广者用户ID：'.var_export($user_id, true));
                $from = '推广二维码';
                $type = 'scan';
                $rst = $this->updateUserInfo($userInfo, $recommendMember, $from, $type);
            }
            elseif(intval($sys_key) !== 0){
                //商品分享
                
                //绑定用户
                
                //推送产品图文信息
                
            }
        }
        else{
        	//主动关注
        	$recommendMember = null;
        	//是否存在用户信息
        	$from = '主动关注';
        	$type = 'self';
            $rst = $this->updateUserInfo($userInfo, $recommendMember, $from, $type);
        }
        
        if($rst['result']){
        	$result = array('code'=>0, 'message'=>$rst['message']);
        }
        else{
        	$result = array('code'=>10001, 'message'=>$rst['message']);
        }
        return $result;
    }
    
    public function unsubscribe($param){
    	$msgData = array(
     	    'EventKey'=>$param['EventKey'],
     	    'FromUserName'=>$param['FromUserName']
     	);
    	$openid = $msgData['FromUserName'];
    	//是否存在用户信息
        $platformInfo = $this->agent_model->isOpenidExist($openid);
        if($platformInfo !== null && !empty($platformInfo)){
        	$rst = $this->agent_model->unsubscribe($openid);
        	if($result){
        		$message = '取消关注成功';
        	    $result = array('code'=>0, 'message'=>$message);
        	}
        	else{
        		$message = '取消关注失败';
        		$result = array('code'=>10001, 'message'=>$message);
        	}
        }
        else{
        	$message = '取消关注失败';
        	$result = array('code'=>10002, 'message'=>$message);
        }
        return $result;
    }
    
    /**
     * 用户已关注时的事件推送
     * @param $msgData
     */
    public function scan($param){
    	log_message('info', '关注后扫码参数：'.var_export($_POST, true));
    	$msgData = array(
     	    'EventKey'=>$param['EventKey'],
     	    'FromUserName'=>$param['FromUserName']
     	);
        $qrscene_param = $msgData['EventKey'];
        $openid = $msgData['FromUserName'];
        if(strpos($qrscene_param, "userTg-")===0){
        	log_message('info', '扫码获得参数：'.var_export($qrscene_param, true));
            $scene_str = $qrscene_param;
            $scene_str = explode('-', $scene_str);
            $user_id = $scene_str[1];//推广者用户user_id
            $userInfo = $this->tencent_model->getUserInfo($openid);
            $recommendMember = $this->user_model->getUserById($user_id);
            $from = '推广二维码';
            $type = 'scan';
            $rst = $this->updateUserInfo($userInfo, $recommendMember, $from, $type);
        }
        else if(is_numeric($qrscene_param)){
            //商品分享
                
            //绑定用户
                
            //推送产品图文信息
        }

        if($rst['result']){
        	$result = array('code'=>0, 'message'=>$rst['message']);
        }
        else{
        	$result = array('code'=>10001, 'message'=>$rst['message']);
        }
        return $result;
    }
    
    public function createMenu() {
    	$this->load->model('tencent_model');
    	$result = $this->tencent_model->createMenu();
    	echo $result;
    }
	
	private function bindUser($param)
	{
		$platformInfo = $this->agent_model->isOpenidExist($param['openid']);
		if($platformInfo === null){
			$is_subscribe = 0;
			$insertResult = $this->agent_model->insertWechatInfo($param, $is_subscribe);
			if($insertResult !== null){
			    $param = $insertResult;
			    $param['userid'] = null;
			}
			else{
				$param['id'] = null;
			}
		}
		else{
			$updateResult = $this->agent_model->updateAccesstoken($param);
			$param['key'] = $platformInfo['code'];
			$param['id'] = $platformInfo['id'];
			$param['userid'] = $platformInfo['userid'];  //无论更新是否成功
		}
		return $param;
	}
	
   /**
     * 判断是否在微信中浏览
     */
    public function isInWechat()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
    
	/**
     * 判断用户信息 ...
     */
    private function checkUser(){
    	log_message('info', '用户确认开始');
    	if($this->isInWechat()){
	    	$code = $this->input->get('code');
	    	$key = $this->input->get('key');
	    	$from = $this->input->get('from');
	    	if(!$code){
	    	    $user = $this->session->wechat;
	    		if(!$user){
	    			log_message('info', '第一次进入oauth');
	    			//没有session，oauth授权获取openid
	    			$redirect_uri = $this->getPageUrl();
	    			$state = 'good';
	    			log_message('info', 'oauth授权获取openid，回调URL：'.$redirect_uri);
	    			$oauth_url = $this->tencent_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
	    			//执行网页授权模式，然后跳转到redirect_uri
	    			header("location: ".$oauth_url);
	                die();
	    		}
	    		else{
	    		    log_message('info', '第二次进入，带有个人key信息');
	    			$redirect_uri = $this->getPageUrl();
	    			if(strpos($redirect_uri, "key")){
		                //有session,不执行oauth取用户信息，有key，URL完整，执行后续代码
		                log_message('info', '路径完整，带有个人key信息');
	    			}
	    			else{
	    				//有session,不执行oauth取用户信息，没有key，添加key后跳转回来
	    				$url = $this->updateKeyToUrl($redirect_uri, $user['code']);
	    				log_message('info', '路径不完整，重新组织URL:'.$url);
		                header("location: " . $url);
	    			}
	    		}
	    	}
	    	else{
	    	    //oauth后回调原来URL并带上code参数
	    		$oauthInfo = $this->tencent_model->getOauthInfoByCode($code);
	    		if($oauthInfo != null){
	    			log_message('info', 'oauth回调带code');
	    			$openid = $oauthInfo['openid'];
	                $access_token = $oauthInfo['access_token'];
	                
	                //授权模式获取用户信息
                	$userInfo = $this->tencent_model->getUserInfoByOauth($openid, $access_token);
                	if($userInfo != null){
                		log_message('info', '网页授权获取用户信息');
                		$param = array(
                		    "key" => $key,
                		    "from" => $from
                		);
                		$param = array_merge($param, $oauthInfo);
                		$param = array_merge($param, $userInfo);
                		unset($param['privilege']);
                		$userData = $this->bindUser($param);
		                if($userData['id']){
			                $this->session->wechat = $userData;
			                if(isset($userData['userid']) && $userData['userid']){
			                	$userInfo = $this->user_model->getUserById($userData['userid']);
			                	$this->session->user = $userInfo;
			                }
			                $redirect_uri = $this->getPageUrl();
			                $key = $userData['code'];
			                $url = $this->updateKeyToUrl($redirect_uri, $key); //去掉code state参数,session 有用户信息
			    			log_message('info', '去掉code state参数，URL带上个人key'.$url);
			                header("location: " . $url);
		                }
		                else{
		                	$this->session->wechat = '';
		                	log_message('info', '获取用户数据出错');
		                	echo '获取用户数据出错';
			                die();
		                }
                	}
                	else{
                		//获取出错，重新oauth获取用户信息，换了种oauth方式
                		$url = $this->getPageUrl();
		                $redirect_uri = $this->updateKeyToUrl($url, $key); //去掉code state参数,session 有用户信息
		    			$state = '';
		    			$oauth_url = $this->tencent_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
		    			//执行网页授权模式，然后跳转到redirect_uri
		    			log_message('info', '授权模式获取用户信息出错,改变授权方式，oauth_url：'.$oauth_url);
		    			header("location: ".$oauth_url);
		                die();
                	}
	    		}
	    		else{
	    			log_message('info', '授权出错');
	    			echo $oauthInfo['errmsg'];
	                die();
	    		}
	    	}
    	}
    	else{
    		echo '请使用微信查看';
    		die();
    	}
    }
    
    public function updateKeyToUrl($url, $key)
    {
        if (!isset($key)){
            return $url;
        }
        
        $urlArr = parse_url($url);
        if (isset($urlArr['query'])) {
            $query_old_block_arr = explode("&", $urlArr['query']);
            $query_old_arr = array();
            foreach ($query_old_block_arr as $k => $v) {
                $tmp = explode("=", $v);
                if (count($tmp) == 2){
                    $query_old_arr[$tmp[0]] = $tmp[1];
                }
            }
            if(empty($key)){
                unset($query_old_arr['key']);
            }
            else{
                $query_old_arr['key'] = $key;
            }
            unset($query_old_arr['code']);
            unset($query_old_arr['state']);
            $query_new_block_arr = array();
            foreach ($query_old_arr as $key => $val) {
                array_push($query_new_block_arr, $key . "=" . $val);
            }
            $urlArr['query'] = implode("&", $query_new_block_arr);
        } 
        else if(!empty($key)){
            $urlArr['query'] = "key=" . $key;
        }
        
        $url_new = $urlArr["scheme"] . "://" . $urlArr["host"] . $urlArr["path"] . "?" . $urlArr["query"];
        if (isset($urlArr['fragment'])){
            $url_new .= "#" . $urlArr['fragment'];
        }

        return $url_new;
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
	
	public function jsapi($param_url){
		$jsapi_ticket = $this->tencent_model->getJsapiTicket();
    	$appid = $this->config->item('serviceAppId');
    	$params = array();
    	$params["url"] = $param_url;
        $params["timestamp"] = time();
        $noncestr = rand(1000000, 9999999);
        $params["noncestr"] = "$noncestr";
        $params["jsapi_ticket"] = $jsapi_ticket;
        ksort($params);
        $paramString = $this->ToUrlParams($params);
        $addrSign = sha1($paramString);

        $data = array(
            "signature" => $addrSign,
            "appId" => $appid,
            "timeStamp" => $params["timestamp"],
            "nonceStr" => $params["noncestr"],
        );
        return $data;
	}
	
    /**
     * 发送文本消息 ...
     * @param $open_id
     * @param $message
     */
    public function sendCustomMessageOfText($openid, $message){
    	header("content-type:text/html; charset=UTF-8");
    	$postData = array(
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>array(
                'content'=>$message
            ),
        );
        $rtn = $this->tencent_model->sendCustomMessage($postData);
        if($rtn){
        	log_message('info', '发送信息给：'.$openid.'成功');
        }
        else{
        	log_message('info', '发送信息给：'.$openid.'失败');
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
     * 将创建海报发送给用户 ...
     * @param $radius
     */
    public function sendHaibaoMsg() {
    	$msgData = array(
     	    'FromUserName'=>$this->input->post('FromUserName'),
     	);
    	$open_id = $msgData['FromUserName'];
    	$platformInfo = $this->agent_model->isOpenidExist($open_id);
    	if($platformInfo !== null){
    	    $media_path = $this->agent_model->createHaibao($platformInfo);
	    	$type = 'image';
	    	$media_id = $this->agent_model->uploadMediaFile($media_path, $type);
	    	if($media_id != null){
	    		$result = $this->sendCustomMessageOfImg($open_id, $media_id);
	    	}
	    	else{
	    		$result = array('code'=>10002, 'message'=>'上传素材失败');
	    	}
    	}
    	else{
    		$result = array('code'=>10004, 'message'=>'获取平台信息失败');
    	}
    	echo json_encode($result);
    	exit;
    }
    
    /**
     * 发送图片消息 ...
     * @param $open_id
     * @param $media_id
     */
    public function sendCustomMessageOfImg($open_id, $media_id){
    	$postData = array(
            'touser'=>$open_id,
            'msgtype'=>'image',
            'image'=>array(
                'media_id'=>$media_id
            ),
        );
        $rtn = $this->tencent_model->sendCustomMessage($postData);
        if($rtn){
        	$result = array('code'=>0, 'message'=>'发送成功');
        }
        else{
        	$result = array('code'=>10001, 'message'=>'发送失败');
        }
        return $result;
    }
    
    private function updateUserInfo($userInfo, $recommendMember, $from, $type){
        //是否存在平台信息
        $platformInfo = $this->agent_model->isOpenidExist($userInfo['openid']);
        if($platformInfo !== null){
            //存在则修改平台信息和修改用户信息
            if($type == 'self'){
                $message = "欢迎再次关注我们";
            }
            else{
                $message = "欢迎扫码再次关注我们";
            }
    		$rst = $this->agent_model->updateWechatInfo($userInfo);
        }
        else{
            //不存在则插入平台信息和修改用户信息
            if($type == 'self'){
                $message = "欢迎首次关注我们";
            }
            else{
                $message = "欢迎扫码首次关注我们";
            }
            $subscribe = 1;
            $userInfo['access_token'] = '';
            $userInfo['refresh_token'] = '';
            $userInfo['from'] = $type;
            if($recommendMember){
                $userInfo['key'] = $recommendMember['code'];
            }
            else{
            	$userInfo['key'] = '';
            }
            $is_subscribe = 1;
    		$rst = $this->agent_model->insertWechatInfo($userInfo, $is_subscribe);
        }
        
        if($rst){
        	return array(
        	    'result'=>true,
        	    'message'=>$message
        	);
        }
        else{
        	return array(
        	    'result'=>false,
        	    'message'=>'数据更新错误'
        	);
        }
    }
       
}