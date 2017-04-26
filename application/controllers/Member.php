<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('member_model');
    }
    
    public function login(){
        $userInfo = $this->session->user;
    	if($userInfo){
		    $url = "Location: /member/center";
    	    header($url);
		    exit();
    	}
    	else{
	    	$data = array();
	    	$this->checkUser();
			$this->load->view('member/login', $data);
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
		
		$wechat = $this->session->wechat;
		if($wechat){
			$data = array_merge($wechat, $data);
			$data['wechat'] = true;
		}
		
		$result = $this->member_model->dologin($data);
		if($result !== null){
			$original_parentid = $this->config->item('original_parentid');
			if($result['parent_id'] == $original_parentid){
				$result['parent_name'] = '系统';
				$result['is_system'] = 1;
			}
			else{
				$parent = $this->member_model->getDataById($result['parent_id']);
				$result['parent_name'] = $parent['name'];
				$result['is_system'] = 0;
			}
			
			$type = 'member';
			$token = $this->setToken($result['id'], $type);
			$result['token'] = $token;
			
			$this->user = $result;
			$this->session->user = $result;
		    $return = array("code"=>0, "data"=>$result, "message"=>"登录成功！");
		}
		else{
			$this->session->user = '';
			$return = array("code"=>9999, "message"=>"登录失败！");
		}
		
		echo json_encode($return);
		exit();
    }
    
    public function center(){
    	$this->auth_redirect_member();
    	$data = array();
    	$userInfo = $this->user;
    	$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['exchange_point'] = $newlist_userInfo['exchange_point'];
    	$userInfo['share_point'] = $newlist_userInfo['share_point'];
    	$userInfo['useable_point'] = $newlist_userInfo['useable_point'];
    	$userInfo['wait_point'] = $newlist_userInfo['wait_point'];
    	$userInfo['level'] = $newlist_userInfo['level'];
    	$userInfo['name'] = $newlist_userInfo['name'];
    	$userInfo['commision'] = $newlist_userInfo['commision'] > 0 ? $newlist_userInfo['commision'] : 0;
    	$userInfo['router'] = $newlist_userInfo['router'];
    	$data['userInfo'] = $userInfo;
    	$pwd_key = $this->config->item('pwd_key');
		$key = $this->encrypt($data['userInfo']['mobile'], $pwd_key);
		$data['key'] = $key;
		$data['is_exchange_pwd_exist'] = 'no';
		if($newlist_userInfo['exchange_pwd']){
			$data['is_exchange_pwd_exist'] = 'have';
		}
		
		//获取最近服务中心
		$serviceCenter = array();
		$router_array = explode(',', $userInfo['router']);
        $last = count($router_array) - 1;
        unset($router_array[$last]);
        $router_array = array_reverse($router_array);
        foreach($router_array as $item){
        	$itemUser = $this->member_model->getDataById($item);
        	if($itemUser !== null && $itemUser['level'] >= 11){
        		$serviceCenter = $itemUser;
        		break;
        	}
        }
        $data['service'] = $serviceCenter;
		
		$directChildrenNum = $this->member_model->getDirectChildrenNumById($userInfo['id']);
		$data['directChildrenNum'] = $directChildrenNum;
		
		$data['title'] = '个人中心';
		$data['page_id'] = 'member-center-page';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		$data['menu'] = 'personal';
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/center.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/center', $data);
		$this->load->view('footer', $data);
    }
    
    public function personal(){
    	$this->auth_redirect_member();
    	$data = array();
    	$data['userInfo'] = $this->user;
    	
    	$data['title'] = '个人资料';
		$data['page_id'] = 'personal-page';
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/personal.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/personal', $data);
		$this->load->view('footer', $data);
    }
    
    public function chpwd(){
    	$this->auth_redirect_member();
    	$data = array();
    	$data['userInfo'] = $this->user;
    	
    	$data['title'] = '密码设置';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/chpwd.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/chpwd', $data);
		$this->load->view('footer', $data);
    }
    
    public function secondpwd(){
    	$this->auth_redirect_member();
    	$userInfo = $this->user;
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
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/secondpwd.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/secondpwd', $data);
		$this->load->view('footer', $data);
    }
    
    public function findpwd($type='member'){
    	$data = array();
    	$data['title'] = '找回密码';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		$data['type'] = $type;
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/findpwd.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/findpwd', $data);
		$this->load->view('footer', $data);
    }
    
    public function findpaypwd($type='member'){
    	$data = array();
        if($type == 'member'){
	    	$this->auth_redirect_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_redirect_service();
			$userInfo = $this->userService;
    	}
    	
    	$data['title'] = '找回二级密码';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		$data['type'] = $type;
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/findpaypwd.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/findpaypwd', $data);
		$this->load->view('footer', $data);
    }
    
    public function regist(){
    	$data = array();
    	$userInfo = $this->session->user;
    	$key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
    	if($key){
    		//有推荐人参数
			$mobile = $this->decrypt($key, $pwd_key);
			if($userInfo){
				//再分享时带上自己分享参数
				$key = $this->encrypt($userInfo['mobile'], $pwd_key);
			}
			else{
				//再分享时使用原参数
			}
    	}
    	else{
    		//无推荐人参数
    		if($userInfo){
    			//再分享时带上自己分享参数
    			$mobile = $userInfo['mobile'];
    			$key = $this->encrypt($mobile, $pwd_key);
    		}
    		else{
    			//再分享时没有参数
    			$mobile = '';
    			$key = '';
    		}
    	}
		$data['mobile'] = $mobile;
		$data['key'] = $key;
		
		$url = $this->getPageUrl();
    	$jsapi = $this->jsapi($url);
    	$data = array_merge($data, $jsapi);
    	$data['share_img'] = base_url("media/img/share_img.png");
		$this->load->view('member/regist', $data);
    }
    
    public function code()
	{
		$this->load->helper('captcha');
		/*
    	$wordArray = array(
    	    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    	    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    	    1, 2 , 3, 4, 5, 6, 7, 8, 9, 0
    	);
    	$word = $wordArray[rand(0, 61)].$wordArray[rand(0, 61)].$wordArray[rand(0, 61)].$wordArray[rand(0, 61)];
    	*/
		
		$wordArray = array(1, 2 , 3, 4, 5, 6, 7, 8, 9, 0);
    	$word = $wordArray[rand(0, 9)].$wordArray[rand(0, 9)].$wordArray[rand(0, 9)].$wordArray[rand(0, 9)];
    	
    	$vals = array(
    		'word'      => $word,
    		'img_path'  => './captcha/',
    		'img_url'   => '/captcha/',
    		'img_width' => 108,
    		'img_height'    => 46,
    		'expiration'    => 7200,
    		'font_size' => 16,
    		 
    		// White background and border, black text and red grid
    		'colors'    => array(
    			'background' => array(255, 255, 255),
    			'border' => array(255, 255, 255),
    			'text' => array(0, 0, 0),
    			'grid' => array(233, 223, 223)
    		)
    	);
    	 
    	$cap = create_captcha($vals);
    	$cap['word'] = strtolower($cap['word']);
    	$this->session->code = $cap['word'];
    	echo json_encode($cap);
	}
	
	public function logout()
	{
		$this->session->user = '';
		$this->session->wechat = '';
		$url = "Location: /member/login";
        header($url);
	    exit();
	}
	
    public function bank(){
    	$this->auth_redirect_member();
    	$userInfo = $this->user;
    	$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['name'] = $newlist_userInfo['name'];
    	
    	$data = array();
    	$data['userInfo'] = $userInfo;
    	$data['title'] = '银行卡绑定';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'member';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    '/media/vendor/jquery.cityselect.js'
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/bank.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/bank', $data);
		$this->load->view('footer', $data);
    }
    
    public function student(){
    	$data = array();
    	$this->auth_json_member();
		$userInfo = $this->user;
    	$page = 1;
    	$num = 10;
    	$data['page'] = $page;
    	$data['num'] = $num;
    	$student_list = $this->member_model->getDirectChildrenById($page, $num, $userInfo['id']);
    	if($student_list !== null){
    		$data['student_list'] = $student_list['data'];
    		$data['count'] = $student_list['count'];
    	}
    	else{
    		$data['student_list'] = array();
    		$data['count'] = 0;
    	}
    	
    	$data['title'] = '我的徒弟';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/student.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/student', $data);
		$this->load->view('footer', $data);
    }
    
    public function submit_chgpwd()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
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
		$this->auth_json_member();
		$userInfo = $this->user;
		$original_pwd = trim($this->input->post('original_password'));
		if($userInfo['exchange_pwd'] && !$original_pwd){
			$return = array("code"=>10001, "message"=>"原密码不能为空");
			echo json_encode($return);
		    exit();
		}
		
        $data = array(
			"id" => $userInfo['id'],
			"password" => $original_pwd,
            "newpassword" => trim($this->input->post('new_password'))
		);
        $result = $this->member_model->submit_second_chgpwd($data);
        if($result){
        	$userInfo['exchange_pwd'] = $this->second_password($data['password']);
        	$this->user = $userInfo;
        	$this->session->user = $userInfo;
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10002, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function submit_regist()
	{
		$parent_mobile = trim($this->input->post('parent_mobile'));
		$rand_code = trim($this->input->post('rand_code'));
		$mobile = trim($this->input->post('mobile'));
		$mobile_code = strtolower(trim($this->input->post('mobile_code')));
		$password = trim($this->input->post('password'));
		$smsInfo = $this->session->sms;
		
    	if($mobile && $mobile_code && $password && $rand_code){
    		$rand_code_session = $this->session->code;
    		if($rand_code_session == $rand_code){
	    		//半小时有效
			    if($smsInfo && $smsInfo['code'] == $mobile_code && (intval($smsInfo['created_at']-time())) <= 30*60){
				    $recommend_user = $this->member_model->getDataByMobile($parent_mobile);
					if(($recommend_user != null && $recommend_user['level'] == 0) || $recommend_user == null){
						if($recommend_user == null){
							$message = '推荐人不存在';
						}
						else{
							$message = '推荐人等级必须在VIP以上';
						}
						$return = array("code"=>10001, "message"=>$message);
					}
					else{
			    	    $data = array(
							"parent_mobile" => $parent_mobile,
			    	        "mobile" => $mobile,
				    	    "password" => $password
						);
			    	    $result = $this->member_model->submit_regist($data);
				        if($result !== null){
				        	$this->session->sms = '';
						    $return = array("code"=>0, "message"=>"注册成功");
						}
						else{
							$return = array("code"=>10002, "message"=>"注册失败");
						}
					}
			    }
		    	elseif($smsInfo && ($smsInfo['created_at']-time()) >= 30*60){
					$return = array("code"=>10002, "message"=>"手机验证码失效");
				}
		    	else{
					$return = array("code"=>10003, "message"=>"手机验证码错误");
				}
    		}
    	    else{
		    	$return = array("code"=>10004, "message"=>"随机验证码出错");
		    }
	    }
    	else{
    		$return = array("code"=>10005, "message"=>"参数错误！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function submit_personal()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
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
        	$this->session->user = $userInfo;
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function is_user_exist_by_mobile()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
		$mobile = $this->input->post('mobile');
		
    	if($mobile){   
    		$userInfo = $this->member_model->getDataByMobile($mobile); 	
		    if($userInfo !== null){
		    	$return = array("code"=>0, "message"=>"获取成功！");
		    }
		    else{
		    	$return = array("code"=>10001, "message"=>"用户不存在");
		    }
    	}
    	else{
    		$return = array("code"=>10002, "message"=>"参数错误！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function is_child_user_exist_by_mobile()
	{
		$this->auth_json_service();
		$userInfo = $this->userService;
		$mobile = $this->input->post('mobile');
		
    	if($mobile){   
    		$recharge_user = $this->member_model->getDataByMobile($mobile); 	
		    if($recharge_user !== null){
		    	$flag = false;
		    	if(strstr($recharge_user['router'], ',')){
		    	    $router_array = explode(',', $recharge_user['router']);
		    	    $last = count($router_array) - 1;
		    	    //unset($router_array[$last]);
		    	
		    	    if(in_array($userInfo['id'], $router_array)){
		    	    	$flag = true;
		    	    }
		    	    else{
		    	    	$flag = false;
		    	    }
		    	}
		    	else{
		    		$flag = false;
		    	}
		    	
		    	if($flag){
		    		$return = array("code"=>0, "message"=>"获取成功！");
		    	}
		    	else{
		    		$return = array("code"=>10001, "message"=>"用户编号必须是当前用户的下级");
		    	}
		    }
		    else{
		    	$return = array("code"=>10002, "message"=>"用户不存在");
		    }
    	}
    	else{
    		$return = array("code"=>10003, "message"=>"参数错误！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function student_data_by_page()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    
        $result = $this->member_model->getDirectChildrenById($page, $num, $userInfo['id']);
        if($result !== null){
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
		$manager = $this->input->post('manager');
		$company = $this->input->post('company');
    	$level = $this->input->post('level');
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $time = $this->input->post('time');
	    $num = $this->input->post('num');
        $result = $this->member_model->selectPageData($manager, $company, $level, $time, $searchkey, $offset, $num);
        if($result !== null){
        	foreach($result['data'] as $key=>$item){
        		//推荐人
        		$result['data'][$key]['parent_name'] = '';
        		$result['data'][$key]['parent_mobile'] = '';
        		$result['data'][$key]['commision'] = round($item['commision'], 2);
        		$parent = $this->member_model->getDataById($item['parent_id']);
        		if($parent !== null){
        			$result['data'][$key]['parent_name'] = $parent['name'];
        			$result['data'][$key]['parent_mobile'] = $parent['mobile'];
        		}
        		
        		//直属徒弟数
        		$childrenNum = $this->member_model->getDirectChildrenNumById($item['id']);
        	    $result['data'][$key]['children_num'] = $childrenNum;
        	    
        	    //报单总额
        		$declaration_total = $this->member_model->getDeclarationTotalByUserd($item['id']);
        		$result['data'][$key]['declaration_total'] = $declaration_total;
        		
        		//充值总额
        		$charge_total = $this->member_model->getChargeTotalByUserd($item['id']);
        		$result['data'][$key]['charge_total'] = $charge_total;
        		
        		//现金总额
        		$cash_total = $this->member_model->getCashTotalByUserd($item['id']);
        		$result['data'][$key]['cash_total'] = $cash_total;
        		
        		//银行卡列表
        		$banklist = $this->member_model->getBankList($item['id']);
        		if($banklist !== null){
        		    $result['data'][$key]['banklist'] = $banklist;
        		}
        		else{
        			$result['data'][$key]['banklist'] = array();
        		}
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
        $result = $this->member_model->deleteBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除用户，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
	/**
	 * 短信发送 用例
	 */
	public function sendSmsCode()
	{
		$this->load->model('sms_model');
		$session_code = $this->session->code;
		$mobile = trim($this->input->post('mobile'));
		$word = strtolower($this->input->post('word'));
		
		if($session_code && $word && $word == $session_code){
			$code = rand(100, 999).rand(100, 999);
			$tpl = $this->member_model->getSmsTemplateByType(1);
			if($tpl !== null && $tpl['status'] == 1){
			    if($tpl['content'] && strpos($tpl['content'], '{code}') !== false){
	            	$message = $tpl['content'];
		            $message = str_replace('{code}', $code ,$message);
	            }
	            else{
	                $message = "您的验证码为：".$code."，有效期半个小时，请妥善保管，感谢支持。";
	            }
		    	
		    	$type = 1;
				$insert_id = $this->sms_model->insert($mobile, $message, $type);
				if($insert_id){
					$resJson = $this->sms_model->send($mobile, $message);
					$res = json_decode($resJson, true);
					if($res && $res['returnstatus'] &&  strtolower($res['returnstatus']) == 'success'){
						$this->session->sms = array(
						    'code'=>$code,
						    'content'=>$message,
						    'type'=>$type,
						    'created_at'=>time()
						);
						
						//修改发送状态
						$updateInfo = $this->sms_model->update($insert_id);
		        	    $return = array("code"=>0, "message"=>"发送成功！");
					}
				    else{
				    	$return = array("code"=>10001, "message"=>"发送失败！");
				    }
				}
				else{
		        	$return = array("code"=>10002, "message"=>"插入数据失败！");
		        }
			}
			elseif($tpl !== null && $tpl['status'] == 0){
				$return = array("code"=>10003, "message"=>"短信已关闭");
			}
			else{
				$return = array("code"=>10004, "message"=>"短信模板未配置");
			}
		}
		else{
			$return = array("code"=>10005, "message"=>"验证码错误");
		}
		
		echo json_encode($return);
		exit();
	}
	
    public function isRegist(){
		$mobile = $this->input->post('mobile');
		$result = $this->member_model->getDataByMobile($mobile);
		if($result !== null){
		    $return = array("code"=>0, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
		echo json_encode($return);
		exit();
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
	
    private function checkUser(){
    	log_message('info', '用户确认开始');
    	if($this->isInWechat()){
    		$this->load->model('weixin_model');
	    	$code = $this->input->get('code');
	    	if(!$code){
	    	    $wechat = $this->session->wechat;
	    		if($wechat){
	    			log_message('info', '第二次进入，带有个人openid信息');
	    			//有session,不执行oauth取用户信息，有openid，URL完整，执行后续代码
	    		}
	    		else{
	    			log_message('info', '第一次进入oauth');
	    			//没有session，oauth授权获取openid
	    			$redirect_uri = $this->getPageUrl();
	    			$state = 'good';
	    			log_message('info', 'oauth授权获取openid，回调URL：'.$redirect_uri);
	    			$oauth_url = $this->weixin_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
	    			//执行网页授权模式，然后跳转到redirect_uri
	    			header("location: ".$oauth_url);
	                die();
	    		}
	    	}
	    	else{
	    	    //oauth后回调原来URL并带上code参数
	    		$oauthInfo = $this->weixin_model->getOauthInfoByCode($code);
	    		if($oauthInfo != null){
	    			log_message('info', 'oauth回调带code');
	    			$openid = $oauthInfo['openid'];
	                $access_token = $oauthInfo['access_token'];
	                
	                //授权模式获取用户信息
                	$userInfo = $this->weixin_model->getUserInfoByOauth($openid, $access_token);
                	if($userInfo != null){
                		log_message('info', '网页授权获取用户信息');
                		unset($userInfo['privilege']);
                		$this->session->wechat = $userInfo;
                	}
                	else{
                		//获取出错，重新oauth获取用户信息
                		$url = $this->getPageUrl();
		                $redirect_uri = $this->updateOpenidToUrl($url, $openid); //去掉code state参数,session 有用户信息
		    			$state = '';
		    			$oauth_url = $this->weixin_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
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
    
    /**
	 * 增加银行卡信息.
	 */
    public function insertBankInfo()
	{
		$type = $this->input->post('type');
		if($type == 'service'){
			$this->auth_json_service();
			$userInfo = $this->userService;
		}
		else{
			$this->auth_json_member();
			$userInfo = $this->user;
		}
		$bank_name = $this->input->post('bank_name');
		$username = $this->input->post('username');
		$card_no = $this->input->post('card_no');
		$bank_address = $this->input->post('bank_address');
		$result = $this->member_model->insertBankInfo($userInfo['id'], $bank_name, $username, $card_no, $bank_address);
		
		if($result){
		    $return = array("code"=>0,"data"=>$result, "message"=>"添加成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>$result, "message"=>"添加失败！");
		}

		echo json_encode($return);
		exit();
	}
	
    public function deleteBankInfo()
	{
		$id = $this->input->post('id');
	    $type = $this->input->post('type');
		if($type == 'service'){
			$this->auth_json_service();
			$userInfo = $this->userService;
		}
		else{
			$this->auth_json_member();
			$userInfo = $this->user;
		}

        $result = $this->member_model->deleteBankInfo($id, $userInfo['id']);
        if($result){
		    $return = array("code"=>0, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
	public function banklist()
	{
	    $data = array();
    	$this->auth_redirect_member();
		$userInfo = $this->user;
		$data['userInfo'] = $userInfo;
		
		$banklist = $this->member_model->getBankList($userInfo['id']);
		if($banklist !== null){
			$data['banklist'] = $banklist;
		    $data['title'] = '银行卡列表';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'personal';
			$data['type'] = 'member';
			$data['env'] = $this->env;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/member/banklist.js?v='.rand(1,10).'.'.rand(1,10)
			);
			$data['systemInfo'] = $this->systemInfo;
		    $this->load->view('header', $data);
			$this->load->view('member/banklist', $data);
			$this->load->view('footer', $data);
		}
		else{
			$url = "Location: /member/bank";
	    	header($url);
	    	return;
		}
	}
	
	public function student_data_by_page_of_admin()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $parent_id = $this->input->post('parent_id');
	    
        $result = $this->member_model->getDirectChildrenById($page, $num, $parent_id);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function changeRecommend()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;

        $id = $this->input->post('id');
        $mobile = $this->input->post('mobile');
        $isMemberExist = $this->member_model->getDataByMobile($mobile);
        if($isMemberExist !== null){
        	$res = $this->member_model->changeRecommend($id, $isMemberExist);
        	if($res){
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '修改用户上级为：'.$mobile.'，id：'.$id;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "message"=>"修改成功！");
        	}
        	else{
        		$return = array("code"=>10001, "message"=>"修改失败！");
        	}
		}
		else{
			$return = array("code"=>10002, "message"=>"用户不存在");
		}

    	echo json_encode($return);
		exit();
	}
	
    public function upgradeLevel()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;

        $id = $this->input->post('id');
        $old_level = $this->input->post('old_level');
        $new_level = $this->input->post('new_level');
        $pay_consume_rate = intval($this->input->post('pay_consume_rate'));
        $pay_mobile = $this->input->post('pay_mobile');
        $order_id = $this->createRandNum();
        
        $parent = $this->is_parent_exist($pay_mobile, $id);
        if($parent['code'] == 0){
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
	    			$commisionSetting = $this->setting_model->getCommisionSetting();
	    			if($commisionSetting !== null){
				        $res = $this->member_model->upgradeLevel($id, $parent['parent']['id'], $pay_consume_rate, $old_level, $new_level, $order_id, $schedule, $commisionSetting);
				        if($res){
				        	$operater_id = $userInfo['id'];
						    $operater_name = $userInfo['name'];
						    $operater_desc = $id.'用户升级为：'.$new_level.'，旧等级：'.$old_level;
						    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
						    $return = array("code"=>0, "message"=>"升级成功");
				        }
				        else{
				        	$return = array("code"=>10001, "message"=>"升级失败");
				        }
	    			}
	    			else{
	    				$return = array("code"=>10004, "message"=>"复投设置不一致");
	    			}
	    		}
	    		else{
	    			$return = array("code"=>10005, "message"=>"请先佣金设置");
	    		}
		    }
		    else{
	            $return = array("code"=>10002, "message"=>"请先报单设置");
	        }
        }
        else{
        	$return = array("code"=>10003, "message"=>$parent['message']);
        }

    	echo json_encode($return);
		exit();
	}
	
	private function createRandNum($str = '')
    {
        return $str.date('YmdHis') . rand(100000, 999999);
    }
    
    public function is_user_same_line_by_mobile()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
		$mobile = $this->input->post('mobile');
		
    	if($mobile){   
    		$transferUser = $this->member_model->getDataByMobile($mobile); 	
    		$routeTransferArray = explode(',', $transferUser['router']);
    		$routeServiceArray = explode(',', $userInfo['router']);
    		
		    if($transferUser !== null && (in_array($userInfo['id'], $routeTransferArray) || in_array($transferUser['id'], $routeServiceArray))){
		    	if($transferUser['level'] > 0){
		    	    $return = array("code"=>0, "message"=>"获取成功！");
		    	}
		    	else{
		    		$return = array("code"=>10001, "message"=>"转让用户不能是普通会员");
		    	}
		    }
    	    elseif($transferUser !== null){
		    	$return = array("code"=>10002, "message"=>"转让用户必须是上级或下级");
		    }
		    else{
		    	$return = array("code"=>10003, "message"=>"转让用户不存在");
		    }
    	}
    	else{
    		$return = array("code"=>10004, "message"=>"参数错误！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	public function second_password($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
	/*
	public function modify(){
		$i = 0;
		$data = $this->member_model->selectAllData();
		if($data !== null){
			foreach($data as $item){
				$parent = $this->member_model->getDataById($item['parent_id']);
				if($parent !== null){
					$res = $this->member_model->updateParentMobile($item['id'], $parent['mobile']);
					if($res){
						$i++;
					}
				}
			}
		}
		echo $i;
	}
	*/
	
	/**
	 * 忘记密码
	 */
	public function sendForgetSmsCode()
	{
		$this->load->model('sms_model');
		$session_code = $this->session->code;
		$mobile = trim($this->input->post('mobile'));
		$word = strtolower($this->input->post('word'));
		
		if($session_code && $word && $word == $session_code){
			$code = rand(100, 999).rand(100, 999);
		    $tpl = $this->member_model->getSmsTemplateByType(2);
			if($tpl !== null && $tpl['status'] == 1){
			    if($tpl['content'] && strpos($tpl['content'], '{code}') !== false){
	            	$message = $tpl['content'];
		            $message = str_replace('{code}', $code ,$message);
	            }
	            else{
	                $message = "您的验证码为：".$code."，有效期半个小时，请妥善保管，感谢支持。";
	            }
		    	
		    	$type = 2;
				$insert_id = $this->sms_model->insert($mobile, $message, $type);
				if($insert_id){
					$resJson = $this->sms_model->send($mobile, $message);
					$res = json_decode($resJson, true);
					if($res && $res['returnstatus'] &&  strtolower($res['returnstatus']) == 'success'){
						$this->session->smsForget = array(
						    'code'=>$code,
						    'content'=>$message,
						    'type'=>$type,
						    'created_at'=>time()
						);
						
						//修改发送状态
						$updateInfo = $this->sms_model->update($insert_id);
		        	    $return = array("code"=>0, "message"=>"发送成功！");
					}
				    else{
				    	$return = array("code"=>10001, "message"=>"发送失败！");
				    }
				}
				else{
		        	$return = array("code"=>10002, "message"=>"插入数据失败！");
		        }
			}
		    elseif($tpl !== null && $tpl['status'] == 0){
				$return = array("code"=>10003, "message"=>"短信已关闭");
			}
			else{
				$return = array("code"=>10004, "message"=>"短信模板未配置");
			}
		}
		else{
			$return = array("code"=>10005, "message"=>"验证码错误");
		}
		
		echo json_encode($return);
		exit();
	}
	
    public function submit_forget()
	{
		$type = trim($this->input->post('type'));
		$rand_code = trim($this->input->post('rand_code'));
		$mobile = trim($this->input->post('mobile'));
		$mobile_code = strtolower(trim($this->input->post('mobile_code')));
		$password = trim($this->input->post('password'));
		$smsInfo = $this->session->smsForget;
		
    	if($mobile && $mobile_code && $password && $rand_code){
    		//半小时有效
    		$rand_code_session = $this->session->code;
    		if($rand_code_session == $rand_code){
			    if($smsInfo && $smsInfo['code'] == $mobile_code && (intval($smsInfo['created_at']-time())) <= 30*60){
				    $user = $this->member_model->getDataByMobile($mobile);
					if($user == null){
						$return = array("code"=>10001, "message"=>'用户不存在');
					}
					else{
			    	    $data = array(
			    	        "mobile" => $mobile,
				    	    "password" => $password
						);
			    	    $result = $this->member_model->submit_forget($data);
				        if($result != 'update_success' && $result != 'update_fail'){
							$original_parentid = $this->config->item('original_parentid');
							if($result['parent_id'] == $original_parentid){
								$result['parent_name'] = '系统';
								$result['is_system'] = 1;
							}
							else{
								$parent = $this->member_model->getDataById($result['parent_id']);
								$result['parent_name'] = $parent['name'];
								$result['is_system'] = 0;
							}
							
							$type = 'member';
							$token = $this->setToken($result['id'], $type);
							$result['token'] = $token;
							
				        	$this->session->user = $result;
				        	$this->session->smsForget = '';
						    $return = array("code"=>0, "message"=>"修改成功，登录成功");
						}
					    elseif($result == 'updata_success'){
						    $return = array("code"=>10001, "message"=>"修改成功，登录失败");
						}
					    elseif($result == 'update_fail'){
						    $return = array("code"=>10002, "message"=>"修改失败");
						}
						else{
							$return = array("code"=>10003, "message"=>"修改失败");
						}
					}
			    }
	    		elseif($smsInfo && ($smsInfo['created_at']-time()) >= 30*60){
					$return = array("code"=>10004, "message"=>"手机验证码失效");
				}
	    		else{
					$return = array("code"=>10005, "message"=>"手机验证码错误");
				}
    		}
    	    else{
		    	$return = array("code"=>10006, "message"=>"随机验证码出错");
		    }
	    }
    	else{
    		$return = array("code"=>10007, "message"=>"参数错误！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	/**
	 * 忘记二级密码
	 */
	public function sendForgetSecondSmsCode()
	{
		$this->load->model('sms_model');
		$session_code = $this->session->code;
		$mobile = trim($this->input->post('mobile'));
		$word = strtolower($this->input->post('word'));
		
		if($session_code && $word && $word == $session_code){
			$code = rand(100, 999).rand(100, 999);
		    $tpl = $this->member_model->getSmsTemplateByType(3);
			if($tpl !== null && $tpl['status'] == 1){
			    if($tpl['content'] && strpos($tpl['content'], '{code}') !== false){
	            	$message = $tpl['content'];
		            $message = str_replace('{code}', $code ,$message);
	            }
	            else{
	                $message = "您的验证码为：".$code."，有效期半个小时，请妥善保管，感谢支持。";
	            }
		    	
		    	$type = 3;
				$insert_id = $this->sms_model->insert($mobile, $message, $type);
				if($insert_id){
					$resJson = $this->sms_model->send($mobile, $message);
					$res = json_decode($resJson, true);
					if($res && $res['returnstatus'] &&  strtolower($res['returnstatus']) == 'success'){
						$this->session->smsForgetSecond = array(
						    'code'=>$code,
						    'content'=>$message,
						    'type'=>$type,
						    'created_at'=>time()
						);
						
						//修改发送状态
						$updateInfo = $this->sms_model->update($insert_id);
		        	    $return = array("code"=>0, "message"=>"发送成功！");
					}
				    else{
				    	$return = array("code"=>10001, "message"=>"发送失败！");
				    }
				}
				else{
		        	$return = array("code"=>10002, "message"=>"插入数据失败！");
		        }
			}
		    elseif($tpl !== null && $tpl['status'] == 0){
				$return = array("code"=>10003, "message"=>"短信已关闭");
			}
			else{
				$return = array("code"=>10004, "message"=>"短信模板未配置");
			}
		}
		else{
			$return = array("code"=>10005, "message"=>"验证码错误");
		}
		
		echo json_encode($return);
		exit();
	}
	
    public function submit_forget_second()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
		
		$type = trim($this->input->post('type'));
		$rand_code = trim($this->input->post('rand_code'));
		$mobile = trim($this->input->post('mobile'));
		$mobile_code = strtolower(trim($this->input->post('mobile_code')));
		$password = trim($this->input->post('password'));
		$smsInfo = $this->session->smsForgetSecond;
		
    	if($mobile && $mobile_code && $password && $rand_code){
    		//半小时有效
    		$rand_code_session = $this->session->code;
    		if($rand_code_session == $rand_code){
			    if($smsInfo && $smsInfo['code'] == $mobile_code && (intval($smsInfo['created_at']-time())) <= 30*60){
		    	    $data = array(
		    	        "id"=>$userInfo['id'],
			    	    "password" => $password
					);
		    	    $result = $this->member_model->submit_forget_second($data);
			        if($result){
			        	$userInfo['exchange_pwd'] = $result;
			        	$this->session->user = $userInfo;
			        	$this->session->smsForgetSecond = '';
					    $return = array("code"=>0, "message"=>"修改成功");
					}
					else{
						$return = array("code"=>10003, "message"=>"修改失败");
					}
			    }
	    		elseif($smsInfo && ($smsInfo['created_at']-time()) >= 30*60){
					$return = array("code"=>10004, "message"=>"手机验证码失效");
				}
	    		else{
					$return = array("code"=>10005, "message"=>"手机验证码错误");
				}
    		}
    	    else{
		    	$return = array("code"=>10006, "message"=>"随机验证码出错");
		    }
	    }
    	else{
    		$return = array("code"=>10007, "message"=>"参数错误！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function commision($categoty='total'){
    	$data = array();
    	$this->auth_redirect_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['commision'] = $newlist_userInfo['commision'] > 0 ? $newlist_userInfo['commision'] : 0;
    	$data['userInfo'] = $userInfo;
    	$data['categoty'] = $categoty;
    	
    	$data['title'] = '佣金记录';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['type'] = 'member';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/commision.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('service/commision', $data);
		$this->load->view('footer', $data);
    }
    
    public function commision_data_by_page()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $categoty = $this->input->post('categoty');
	    
	    $this->load->model('service_model');
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
	
    public function getcash($type='member'){
    	$data = array();
    	if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['commision'] = $newlist_userInfo['commision'];
    	$data['userInfo'] = $userInfo;
    	
    	$banklist = $this->member_model->getBankList($userInfo['id']);
    	if($banklist != null){
		    $data['banklist'] = $banklist !== null ? $banklist : array();
	    	$data['title'] = '我要提现';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'personal';
			$data['env'] = $this->env;
			$data['type'] = $type;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/member/getcash.js?v='.rand(1,10).'.'.rand(1,10)
			);
			$data['systemInfo'] = $this->systemInfo;
		    $this->load->view('header', $data);
			$this->load->view('member/getcash', $data);
			$this->load->view('footer', $data);
    	}
    	else{
    		$url = "Location: /member/bank";
    	    header($url);
    	    return;
    	}
    }
    
    public function cashlog($type='member'){
    	$data = array();
        if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
    	
    	$data['title'] = '提现记录';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		$data['type'] = $type;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/cashlog.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/cashlog', $data);
		$this->load->view('footer', $data);
    }
    
    public function do_get_cash()
	{
		$type = trim($this->input->post('type'));
	    if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
    	$userInfo['commision'] = $newlist_userInfo['commision'];
    	
		$money = trim($this->input->post('money'));
		$bank_id = trim($this->input->post('bank_id'));
		$check_key = trim($this->input->post('check_key'));
		
		if($money && $bank_id && $check_key){
			if($money >= 100){
				$log_info = $this->member_model->selectTakecashLog($userInfo['id']);
				if($log_info == null){
					$tax_money = 3500;
					$rate = 0.08;
					if($money > $tax_money){
						$get_money = $tax_money + ($money - $tax_money) * (1 - $rate);
					}
					else{
						$get_money = $money;
					}
					
			    	$hash = $this->check($check_key);
				    if(!$userInfo['exchange_pwd']){
				    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
				    }
			    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
			    		$order_id = $this->createRandNum();
					    $result = $this->member_model->do_get_cash($money, $get_money, $userInfo['id'], $bank_id, $order_id, $tax_money, $rate);
					    if($result){
					    	$return = array("code"=>0, "message"=>"提交成功！");
					    }
					    else{
					    	$return = array("code"=>10001, "message"=>"提交失败！");
					    }
			    	}
			    	else{
			    		$msg = '二级密码错误';
			    		$return = array("code"=>10002, "message"=>$msg);
			    	}
				}
				else{
					$msg = '每月只能提现一次';
			    	$return = array("code"=>10009, "message"=>$msg);
				}
			}
			else{
				$msg = '提现金额必须大于等于100';
				$return = array("code"=>10003, "message"=>$msg);
			}
		}
		else{
			$msg = '参数错误';
			$return = array("code"=>10004, "message"=>$msg);
		}
		
		echo json_encode($return);
		exit();
	}
	
	protected function check($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
	public function cashlog_data_by_page(){
		$type = trim($this->input->post('type'));
	    if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    
        $result = $this->member_model->cashlog_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function wallet($type='member'){
    	$data = array();
	    if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
    	
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['commision'] = $newlist_userInfo['commision'] > 0 ? $newlist_userInfo['commision'] : 0;
    	$data['userInfo'] = $userInfo;
    	
    	$banklist = $this->member_model->getBankList($userInfo['id']);
		$data['bank_num'] = $banklist !== null ? count($banklist) : 0;
		
		$monthCommision = $this->member_model->getMonthCommision($userInfo['id']);
		$totalCommision = $this->member_model->getTotalCommision($userInfo['id']);
		$monthConsume = $this->member_model->getMonthConsume($userInfo['id']);
		$totalConsume = $this->member_model->getTotalConsume($userInfo['id']);
		$totalTakecash = $this->member_model->getTotalTakecash($userInfo['id']);
		$data['monthCommision'] = $monthCommision;
		$data['totalCommision'] = $totalCommision;
		$data['monthConsume'] = $monthConsume;
		$data['totalConsume'] = $totalConsume;
		$data['totalTakecash'] = $totalTakecash;
    	
    	$data['title'] = '钱包';
		$data['page_id'] = 'member-center-page';
		$data['menu'] = 'personal';
		$data['type'] = $type;
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/wallet.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/wallet', $data);
		$this->load->view('footer', $data);
    }
    
    public function walletconsume($categoty='total', $type='member'){
    	$data = array();
        if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
    	
    	$data['title'] = '钱包消费';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		$data['type'] = $type;
		$data['categoty'] = $categoty;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/member/walletconsume.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('member/walletconsume', $data);
		$this->load->view('footer', $data);
    }
    
	public function consumelog_data_by_page(){
		$categoty = $this->input->post('categoty');
		$type = trim($this->input->post('type'));
	    if($type == 'member'){
	    	$this->auth_json_member();
			$userInfo = $this->user;
    	}
    	else{
    		$this->auth_json_service();
			$userInfo = $this->userService;
    	}
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    
        $result = $this->member_model->consumelog_data_by_page($userInfo['id'], $page, $num, $categoty);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
	public function is_parent_exist($mobile, $user_id){
	    $parent = $this->member_model->getDataByMobile($mobile); 	
	    if($parent !== null){
	    	$self = $this->member_model->getDataById($user_id);
	    	$flag = false;
	    	if(strstr($self['router'], ',')){
	    	    $router_array = explode(',', $self['router']);
	    	    $last = count($router_array) - 1;
	    	    unset($router_array[$last]);
	    	
	    	    if(in_array($parent['id'], $router_array)){
	    	    	$flag = true;
	    	    }
	    	    else{
	    	    	$flag = false;
	    	    }
	    	}
	    	else{
	    		$flag = false;
	    	}
	    	
	    	if($flag){
	    		$return = array("code"=>0, "parent"=>$parent, "message"=>"获取成功！");
	    	}
	    	else{
	    		$return = array("code"=>10001, "message"=>"该手机号用户不是当前用户的上级");
	    	}
	    }
	    else{
	    	$return = array("code"=>10002, "message"=>"用户不存在");
	    }
	    
	    return $return;
	}
}