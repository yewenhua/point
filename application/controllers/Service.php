<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('service_model');
        $this->load->model('member_model');
    }
    
    public function login(){
        $userInfo = $this->session->service;
    	if($userInfo){
		    $url = "Location: /service/center";
    	    header($url);
		    exit();
    	}
    	else{
	    	$data = array();
	    	$this->checkUser();
			$this->load->view('service/login', $data);
    	}
    }
    
    public function dologin(){
    	$username = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));
		$data = array(
			"username" => $username,
			"password" => $password,
		    "wechat" => false
		);
		
        $agent = $this->session->Agent;
		if($agent){
			$data = array_merge($agent, $data);
			$data['wechat'] = true;
		}
		
		$result = $this->service_model->dologin($data);
		if($result !== null && $result != 'no_privilege'){
			$type = 'service';
			$token = $this->setToken($result['id'], $type);
			$result['token'] = $token;
			
			$this->userService = $result;
			$this->session->service = $result;
		    $return = array("code"=>0, "data"=>$result, "message"=>"登录成功！");
		}
		elseif($result == 'no_privilege'){
			$return = array("code"=>10001, "message"=>"您的权限不够，无法登录");
		}
		else{
			$this->session->service = '';
			$return = array("code"=>9999, "message"=>"登录失败！");
		}
		
		echo json_encode($return);
		exit();
    }
    
	public function logout()
	{
		$this->session->service = '';
		$this->session->wechat = '';
		$url = "Location: /service/login";
        header($url);
	    exit();
	}
    
    public function chpwd(){
    	$this->auth_redirect_service();
    	$data = array();
    	$data['userInfo'] = $this->userService;
    	
    	$data['title'] = '密码设置';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/service/chpwd.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/chpwd', $data);
		$this->load->view('footer', $data);
    }
    
    public function secondpwd(){
    	$this->auth_redirect_service();
    	$userInfo = $this->userService;
    	$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
    	$data = array();
    	$data['userInfo'] = $userInfo;
        if($userInfo['exchange_pwd']){
    		$data['have_second_pwd'] = 'yes';
    	}
    	else{
    		$data['have_second_pwd'] = 'no';
    	}
    	
    	$data['title'] = '二级密码设置';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/service/secondpwd.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/secondpwd', $data);
		$this->load->view('footer', $data);
    }
    
    public function record(){
    	$data = array();
    	$this->auth_redirect_service();
		$userInfo = $this->userService;
		$month_money = $this->service_model->getMonthMoney($userInfo['id']);
		$total_money = $this->service_model->getTotalMoney($userInfo['id']);
		
		$data['month_money'] = $month_money !== null && $month_money['recharge_money'] !== null ? $month_money['recharge_money'] : 0;
		$data['total_money'] = $total_money !== null && $total_money['recharge_money'] !== null ? $total_money['recharge_money'] : 0;
		
		$data['title'] = '报单记录';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'order';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/service/record.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/record', $data);
		$this->load->view('footer', $data);
    }
    
    public function commision($categoty='total'){
    	$data = array();
    	$this->auth_redirect_service();
		$userInfo = $this->userService;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['commision'] = $newlist_userInfo['commision'] > 0 ? $newlist_userInfo['commision'] : 0;
    	$data['userInfo'] = $userInfo;
    	$data['categoty'] = $categoty;
    	
    	$data['title'] = '佣金记录';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/service/commision.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/commision', $data);
		$this->load->view('footer', $data);
    }
    
    public function online(){
    	$data = array();
    	$this->auth_redirect_service();
		$userInfo = $this->userService;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['commision'] = $newlist_userInfo['commision'] > 0 ? $newlist_userInfo['commision'] : 0;
    	$data['userInfo'] = $userInfo;
    	
    	$this->load->model('setting_model');
		$schedule = $this->setting_model->getConfigDeclaration();
    	if($schedule !== null){
	    	$data['title'] = '在线报单';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'personal';
			$data['type'] = 'service';
			$data['env'] = $this->env;
			$data['schedule'] = $schedule;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/service/online.js?v='.rand(1,10).'.'.rand(1,10)
			);
			$data['systemInfo'] = $this->systemInfo;
		    $this->load->view('header', $data);
			$this->load->view('service/online', $data);
			$this->load->view('footer', $data);
	    }
	    else{
	    	echo '请先设置排期';
	    	die;
	    }
    }
    
    public function center(){
    	$this->auth_redirect_service();
    	$data = array();
    	$userInfo = $this->userService;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['commision'] = $newlist_userInfo['commision'] > 0 ? $newlist_userInfo['commision'] : 0;
    	$userInfo['share_point'] = $newlist_userInfo['share_point'];
    	$userInfo['level'] = $newlist_userInfo['level'];
    	$data['userInfo'] = $userInfo;
    	$data['menu'] = 'personal';
    	
    	$data['title'] = '用户中心';
		$data['page_id'] = 'personal-page';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array();
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/center', $data);
		$this->load->view('footer', $data);
    }
    
    public function bank(){
    	$this->auth_redirect_service();
    	$data = array();
    	$data['userInfo'] = $this->userService;
    	
    	$data['title'] = '银行卡绑定';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    '/media/vendor/jquery.cityselect.js'
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/service/bank.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/bank', $data);
		$this->load->view('footer', $data);
    }
    
    public function personal(){
    	$this->auth_redirect_service();
    	$data = array();
    	$data['userInfo'] = $this->userService;
    	
    	$data['title'] = '个人资料';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'service';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/service/personal.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/personal', $data);
		$this->load->view('footer', $data);
    }
    
    public function submit_chgpwd()
	{
		$this->auth_json_service();
		$userInfo = $this->userService;
    	$this->load->model('member_model');
        $data = array(
			"id" => $userInfo['id'],
			"password" => trim($this->input->post('original_password')),
            "newpassword" => trim($this->input->post('new_password'))
		);
        $result = $this->member_model->submit_chgpwd($data);
        if($result){
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function submit_second_chgpwd()
	{
		$this->auth_json_service();
		$userInfo = $this->userService;
    	$this->load->model('member_model');
	    if($userInfo['exchange_pwd'] && !$original_pwd){
			$return = array("code"=>10001, "message"=>"原密码不能为空");
			echo json_encode($return);
		    exit();
		}
		
        $data = array(
			"id" => $userInfo['id'],
			"password" => trim($this->input->post('original_password')),
            "newpassword" => trim($this->input->post('new_password'))
		);
        $result = $this->member_model->submit_second_chgpwd($data);
        if($result){
        	$userInfo['exchange_pwd'] = $this->second_password($data['password']);
        	$this->userService = $userInfo;
        	$this->session->service = $userInfo;
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function submit_personal()
	{
		$this->auth_json_service();
		$userInfo = $this->userService;
    	$this->load->model('member_model');
        $data = array(
			"id" => $userInfo['id'],
            "name" => trim($this->input->post('name')),
            "card_no" => trim($this->input->post('card_no')),
            "sex" => trim($this->input->post('sex'))
		);
        $result = $this->member_model->submit_personal($data);
        if($result){
        	$userInfo['name'] = trim($this->input->post('name'));
        	$userInfo['card_no'] = trim($this->input->post('card_no'));
        	$userInfo['sex'] = trim($this->input->post('sex'));
        	$this->session->service = $userInfo;
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function do_online()
	{
		$this->auth_redirect_service();
		$userInfo = $this->userService;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
		$point = intval($this->input->post('point'));
		$mobile = $this->input->post('mobile');
		$total = $this->input->post('total');
		$check_key = $this->input->post('check_key');
		$pay_money = $total - $point;
		
		if($mobile && $total && $check_key){
			$this->load->model('setting_model');
			$schedule = $this->setting_model->getConfigDeclaration();
	    	if($schedule !== null){
	    		$setting_res = false;
	    		if($schedule['charge_repeat_rate'] && strpos($schedule['charge_repeat_rate'], '/') === false && $schedule['charge_repeat_period'] && strpos($schedule['charge_repeat_period'], '/') === false){
	    			//只有一轮
	    			$setting_res = true;
	    		}
	    	    elseif(strpos($schedule['charge_repeat_rate'], '/') !== false && strpos($schedule['charge_repeat_period'], '/') !== false){
	    	    	//同为多轮
	    	    	$charge_repeat_rate_arr = explode('/', $schedule['charge_repeat_rate']);
	    	    	$charge_repeat_peroid_arr = explode('/', $schedule['charge_repeat_period']);
	    	    	if(count($charge_repeat_rate_arr) == count($charge_repeat_peroid_arr)){
	    	    		//轮数设置相同
	    			    $setting_res = true;
	    	    	}
	    		}
	    		
	    		if($setting_res){
					if($total >= $schedule['least_money'] && $total <= $schedule['most_money']){
				    	$hash = $this->check($check_key);
					    if(!$userInfo['exchange_pwd']){
					    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
					    }
				    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
						    $recharge_user = $this->member_model->getDataByMobile($mobile);
					    	if($recharge_user !== null){
					    		$order_id = $this->createRandNum();
								    
							    if($userInfo['consume_point'] >= $point){
							    	$rate_point = $total * $schedule['use_consume_most_rate'] * 0.01;
							    	if(!$point || ($point && $rate_point >= $point)){
								        $result = $this->service_model->do_online($point, $total, $userInfo['id'], $recharge_user['id'], $order_id);
								        if($result !== null){
								        	$pwd_key = $this->config->item('pwd_key');
										    $order_key = $this->encrypt($order_id, $pwd_key);
			    			                $url = "Location: /service/pay?key=".$order_key;
			    			                header($url);
			                                return;
										}
										else{
											$msg = '订单创建失败';
											$url = "Location: /service/fail?msg=".$msg;
										}
							    	}
							    	else{
							    		$msg = '消费积分比例不能大于'.$schedule['use_consume_most_rate'].'%';
							    	}
							    }
							    else{
							    	$msg = '消费积分不足';
							    }
					    	}
					    	else{
					    		$msg = '用户编号不存在';
					    	}
				    	}
				    	else{
				    		$msg = '二级密码错误';
				    	}
					}
					else{
						$msg = '报单金额必须在'.$schedule['least_money'].'和'.$schedule['most_money'].'之间';
					}
	    		}
	    		else{
	    			$msg = '复投设置不一致';
	    		}
	    	}
            else{
            	$msg = '请先设置排期';
            }
		}
		else{
			$msg = '参数错误';
		}
		
		$url = "Location: /service/fail?msg=".$msg;
		header($url);
		return;
	}
	
	public function online_notify(){
		$this->load->model('setting_model');
		$this->load->model('qqpay_model');
        $return = $this->qqpay_model->notify();
        $callbackData = $return['callback'];
		if(isset($callbackData['return_code']) && $callbackData['return_code'] == 'SUCCESS' && isset($callbackData['result_code']) && $callbackData['result_code'] == 'SUCCESS')
		{
			$pay_time = date('Y-m-d H:i:s', strtotime($callbackData['time_end']));
			$order_id = $callbackData['out_trade_no'];
			$order = $this->service_model->selectRechargeDataByOrderid($order_id);
	    	$schedule = $this->setting_model->getConfigDeclaration();
	    	$recharge_user = $this->member_model->getDataById($order['recharge_user_id']);
	    	$commisionSetting = $this->setting_model->getCommisionSetting();
    	    if($schedule !== null && $recharge_user !== null && $order !== null && $order['status'] == 0 && $commisionSetting !== null){
				$res = $this->service_model->online_callback($order, $callbackData['transaction_id'], $pay_time, $schedule, $recharge_user['router'], $commisionSetting);
				if($res){
					log_message('info', '服务中心充值回调=>支付成功'.var_export($callbackData, true));
					echo $return['xml'];
				}
				else{
					echo '';
				}
    	    }
    	    else{
    	    	echo '';
    	    }
		}
		else{
			log_message('info', '服务中心充值回调=>支付失败');
			echo '';
		}
		exit;
	}
	
	protected function check($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
    private function createRandNum($str = '')
    {
        return date('YmdHis') . rand(100000, 999999).$str;
    }
    
    public function record_data_by_page()
	{
		$this->auth_json_service();
		$userInfo = $this->userService;
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    
        $result = $this->service_model->record_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
        	foreach($result['data'] as $key=>$item){
        		$recharge_user = $this->member_model->getDataById($item['recharge_user_id']);
        		if($recharge_user !== null){
        			$result['data'][$key]['name'] = $recharge_user['name'];
        			$result['data'][$key]['mobile'] = $recharge_user['mobile'];
        		}
        		else{
        			$result['data'][$key]['name'] = '--';
        			$result['data'][$key]['mobile'] = '--';
        		}
        		
        		$pwd_key = $this->config->item('pwd_key');
		        $order_key = $this->encrypt($item['order_id'], $pwd_key);
		        $result['data'][$key]['key'] = $order_key;
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function commision_data_by_page()
	{
		$this->auth_json_service();
		$userInfo = $this->userService;
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $categoty = $this->input->post('categoty');
	    
        $result = $this->service_model->commision_data_by_page($userInfo['id'], $page, $num, $categoty);
        if($result !== null){
        	foreach($result['data'] as $key=>$item){
        		$recharge_user = $this->member_model->getDataById($item['recharge_user_id']);
        		if($recharge_user !== null){
        			$result['data'][$key]['name'] = $recharge_user['name'];
        		}
        		else{
        			$result['data'][$key]['name'] = '--';
        		}
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
	/*
	 * admin
	 */
    public function selectPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
        $status = $this->input->post('status');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $userid = $this->input->post('userid');
	    $type = $this->input->post('type');
        $result = $this->service_model->selectPageData($userid, $type, $start, $end, $searchkey, $offset, $num, $status);
        if($result !== null){
        	foreach($result['data'] as $key=>$item){
        		$result['data'][$key]['recharge_money'] = round($item['recharge_money'] , 2);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->service_model->deleteBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除报单记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	   
    	echo json_encode($return);
		exit();
	}
	
    private function checkUser(){
    	log_message('info', '用户确认开始');
    	if($this->isInWechat()){
    		$this->load->model('tencent_model');
	    	$code = $this->input->get('code');
	    	if(!$code){
	    	    $agent = $this->session->Agent;
	    		if($agent){
	    			log_message('info', '第二次进入，带有个人openid信息');
	    			//有session,不执行oauth取用户信息，有openid，URL完整，执行后续代码
	    		}
	    		else{
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
                		unset($userInfo['privilege']);
                		$this->session->Agent = $userInfo;
                	}
                	else{
                		//获取出错，重新oauth获取用户信息
                		$url = $this->getPageUrl();
		                $redirect_uri = $this->updateOpenidToUrl($url, $openid); //去掉code state参数,session 有用户信息
		    			$state = '';
		    			$oauth_url = $this->tencent_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
		    			//执行网页授权模式，然后跳转到redirect_uri
		    			log_message('info', '授权模式获取用户信息出错,改变授权方式');
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
    
    public function updateOpenidToUrl($url, $openid)
    {
        if (!isset($openid)){
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
            if(empty($openid)){
                unset($query_old_arr['openid']);
            }
            else{
                $query_old_arr['openid'] = $openid;
            }
            unset($query_old_arr['code']);
            unset($query_old_arr['state']);
            $query_new_block_arr = array();
            foreach ($query_old_arr as $key => $val) {
                array_push($query_new_block_arr, $key . "=" . $val);
            }
            $urlArr['query'] = implode("&", $query_new_block_arr);
        } 
        else if(!empty($openid)){
            $urlArr['query'] = "openid=" . $openid;
        }
        
        $url_new = $urlArr["scheme"] . "://" . $urlArr["host"] . $urlArr["path"] . "?" . $urlArr["query"];
        if (isset($urlArr['fragment'])){
            $url_new .= "#" . $urlArr['fragment'];
        }

        return $url_new;
    }
    
    public function pay(){
    	$this->auth_redirect_service();
    	$data = array();
    	$userInfo = $this->userService;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
        if(isset($userInfo['service_openid']) && isset($newlist_userInfo['service_openid']) && $userInfo['service_openid'] != $newlist_userInfo['service_openid']){
    		$url = "Location: /service/logout";
	    	header($url);
	    	return;
    	}
    	
    	$userInfo['service_openid'] = $newlist_userInfo['service_openid'];
    	$data['userInfo'] = $userInfo;
    	
    	$order_key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$order_id = $this->decrypt($order_key, $pwd_key);
		$order = $this->service_model->selectDataByOrderid($order_id);
		
    	if($order !== null){
    		$data['title'] = '报单支付';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'personal';
			$data['order'] = $order;
			$data['type'] = 'service';
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    
			);
			
			$this->load->model('qqpay_model');
			$openid = $userInfo['service_openid'];
	    	$type = 'JSAPI';
	    	$fee = $order['pay_money'] * 100; //订单总金额，单位为分
	    	$prepayInfo = $this->qqpay_model->prepay($order['order_id'], $openid, $type, $fee);
	    	
	    	if($prepayInfo !== null){
	    		$prepay_id = $prepayInfo["prepay_id"];
	    		$jsapiParameters = $this->qqpay_model->getJsapiParameters($prepay_id);
	    		$data['jsapiParameters'] = $jsapiParameters;
	    	}
	    	else{
	    		// 'prepay fail';
	    		$url = "Location: /service/fail";
		    	header($url);
		    	return;
	    	}
	    	
	    	$url = base_url("service/pay?showwxpaytitle=1");
	        $jsapi = $this->jsapi($url);
	        $data = array_merge($data, $jsapi);
			$data['systemInfo'] = $this->systemInfo;
    		$this->load->view('header', $data);
			$this->load->view('service/pay', $data);
			$this->load->view('footer', $data);
    	}
        else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
    }
    
	public function jsapi($param_url){
		$this->load->model('weixin_model');
		$jsapi_ticket = $this->weixin_model->getJsapiTicket();
    	$appid = $this->config->item('appId');
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
    
    public function success(){
    	$data = array();
		$this->load->view('service/success', $data);
    }
    
    public function fail($message = null){
    	$data = array();
    	$data['message'] = $message;
		$this->load->view('service/fail', $data);
    }
	
	public function banklist()
	{
	    $data = array();
    	$this->auth_redirect_service();
		$userInfo = $this->userService;
		$data['userInfo'] = $userInfo;
		
		$banklist = $this->member_model->getBankList($userInfo['id']);
		if($banklist !== null){
			$data['banklist'] = $banklist;
		    $data['title'] = '银行卡列表';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'personal';
			$data['type'] = 'service';
			$data['env'] = $this->env;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/service/banklist.js?v='.rand(1,10).'.'.rand(1,10)
			);
			$data['systemInfo'] = $this->systemInfo;
		    $this->load->view('header', $data);
			$this->load->view('service/banklist', $data);
			$this->load->view('footer', $data);
		}
		else{
			$url = "Location: /service/bank";
	    	header($url);
	    	return;
		}
	}
	
	public function second_password($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
    public function do_online_wallet()
	{
		$this->auth_redirect_service();
		$userInfo = $this->userService;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
    	$userInfo['commision'] = $newlist_userInfo['commision'];
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
		$point = intval($this->input->post('point'));
		$mobile = $this->input->post('mobile');
		$total = $this->input->post('total');
		$check_key = $this->input->post('check_key');
		$pay_money = $total - $point;
		
		if($mobile && $total && $check_key){
			$this->load->model('setting_model');
    		$schedule = $this->setting_model->getConfigDeclaration();
    		$commisionSetting = $this->setting_model->getCommisionSetting();
            if($schedule !== null && $commisionSetting !== null){
            	$setting_res = false;
	    		if($schedule['charge_repeat_rate'] && strpos($schedule['charge_repeat_rate'], '/') === false && $schedule['charge_repeat_period'] && strpos($schedule['charge_repeat_period'], '/') === false){
	    			//只有一轮
	    			$setting_res = true;
	    		}
	    	    elseif(strpos($schedule['charge_repeat_rate'], '/') !== false && strpos($schedule['charge_repeat_period'], '/') !== false){
	    	    	//同为多轮
	    	    	$charge_repeat_rate_arr = explode('/', $schedule['charge_repeat_rate']);
	    	    	$charge_repeat_peroid_arr = explode('/', $schedule['charge_repeat_period']);
	    	    	if(count($charge_repeat_rate_arr) == count($charge_repeat_peroid_arr)){
	    	    		//轮数设置相同
	    			    $setting_res = true;
	    	    	}
	    		}
	    		
	    		if($setting_res){
	            	if($total >= $schedule['least_money'] && $total <= $schedule['most_money']){
				    	$hash = $this->check($check_key);
					    if(!$userInfo['exchange_pwd']){
					    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
					    }
				    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
						    $recharge_user = $this->member_model->getDataByMobile($mobile);
				
					    	if($recharge_user !== null){
					    		$order_id = $this->createRandNum();
					    		$pay_no = $this->createRandNum('walletpay');
								    
							    if($userInfo['consume_point'] >= $point){
							    	if($userInfo['commision'] >= $pay_money){
								    	$rate_point = $total * $schedule['use_consume_most_rate'] * 0.01;
								    	if(!$point || ($point && $rate_point >= $point)){
									        $result = $this->service_model->do_online_wallet($point, $total, $userInfo['id'], $recharge_user['id'], $order_id, $pay_no, $schedule, $recharge_user['router'], $commisionSetting);
									        if($result !== null){
				    			                $url = "Location: /service/success";
				    			                header($url);
				                                return;
											}
											else{
												$msg = '支付失败';
												$url = "Location: /service/fail?msg=".$msg;
											}
								    	}
								    	else{
								    		$msg = '消费积分比例不能大于'.$schedule['use_consume_most_rate'].'%';
								    	}
							    	}
							    	else{
							    		$msg = '钱包余额不足';
							    	}
							    }
							    else{
							    	$msg = '消费积分不足';
							    }
					    	}
					    	else{
					    		$msg = '用户编号不存在';
					    	}
				    	}
				    	else{
				    		$msg = '二级密码错误';
				    	}
					}
					else{
						$msg = '报单金额必须在'.$schedule['least_money'].'和'.$schedule['most_money'].'之间';
					}
	    		}
	    		else{
	    			$msg = '复投设置不一致';
	    		}
            }
            else{
            	$msg = '请先设置排期';
            }
		}
		else{
			$msg = '参数错误';
		}
		
		$url = "Location: /service/fail?msg=".$msg;
		header($url);
		return;
	}
}