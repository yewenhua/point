<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('point_model');
        $this->load->model('setting_model');
        $this->load->model('member_model');
    }
    
    public function wait($type='member'){
    	$data = array();
    	$this->auth_redirect_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['wait_point'] = $newlist_userInfo['wait_point'];
    	$data['userInfo'] = $userInfo;
    	$data['type'] = $type;
    	
    	$data['title'] = '待用积分';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/wait.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/wait', $data);
		$this->load->view('footer', $data);
    }
    
    public function exchange($type='member'){
    	$data = array();
    	if($type == 'member'){
    	    $this->auth_redirect_member();
    	    $userInfo = $this->user;
    	}
    	else{
    		$this->auth_redirect_service();
    		$userInfo = $this->userService;
    	}
    	
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_point'] = $newlist_userInfo['exchange_point'];
    	$data['userInfo'] = $userInfo;
    	$data['type'] = $type;
    	
    	$data['title'] = '购物券';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/exchange.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/exchange', $data);
		$this->load->view('footer', $data);
    }
    
    public function share($type='member'){
    	$data = array();
    	if($type == 'member'){
    	    $this->auth_redirect_member();
    	    $userInfo = $this->user;
    	}
    	else{
    		$this->auth_redirect_service();
    		$userInfo = $this->userService;
    	}
    	
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['share_point'] = $newlist_userInfo['share_point'];
    	$data['userInfo'] = $userInfo;
    	$data['type'] = $type;
    	
    	$data['title'] = '分享积分';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/share.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/share', $data);
		$this->load->view('footer', $data);
    }
    
    public function useable($type='member'){
    	$data = array();
    	$this->auth_redirect_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['useable_point'] = $newlist_userInfo['useable_point'];
    	$data['userInfo'] = $userInfo;
    	$data['type'] = $type;
    	
    	$data['title'] = '可用积分';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/useable.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/useable', $data);
		$this->load->view('footer', $data);
    }
    
    public function repeat(){
    	$this->auth_redirect_member();
		$userInfo = $this->user;
    	$data = array();
    	$key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$id = $this->decrypt($key, $pwd_key);
    	
    	$repeatInfo = $this->point_model->getRepeatById($id);
    	if($repeatInfo !== null){
    		$repeatInfo['key'] = $key;
    		$userInfo = $this->member_model->getDataById($userInfo['id']);
    		$data['repeatInfo'] = $repeatInfo;
    		$data['userInfo'] = $userInfo;
    		$data['title'] = '积分复投';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'personal';
			$data['env'] = $this->env;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/point/repeat.js?v='.rand(1,10).'.'.rand(1,10)
			);
			$data['systemInfo'] = $this->systemInfo;
		    $this->load->view('header', $data);
		    $this->load->view('point/repeat', $data);
		    $this->load->view('footer', $data);
    	}
    	else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">数据出错</div>';
    		exit;
    	}
    }
    
    public function abletoconsume(){
    	$data = array();
    	$this->auth_redirect_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['useable_point'] = $newlist_userInfo['useable_point'];
    	$data['userInfo'] = $userInfo;
    	$data['title'] = '可用积分转消费积分';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/abletoconsume.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/abletoconsume', $data);
		$this->load->view('footer', $data);
    }
    
    public function consume($type='member'){
    	$data = array();
    	if($type == 'member'){
    	    $this->auth_redirect_member();
    	    $userInfo = $this->user;
    	}
    	else{
    		$this->auth_redirect_service();
    		$userInfo = $this->userService;
    	}
    	
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$data['userInfo'] = $userInfo;
    	$data['type'] = $type;
    	
    	$data['title'] = '消费积分';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/consume.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/consume', $data);
		$this->load->view('footer', $data);
    }
    
    public function consumetransfer(){
    	$data = array();
    	$this->auth_redirect_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$data['userInfo'] = $userInfo;
    	
    	$data['title'] = '消费积分转让';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/point/consumetransfer.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('point/consumetransfer', $data);
		$this->load->view('footer', $data);
    }
    
    public function do_repeat()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
		
		$check_key = $this->input->post('check_key');
    	$hash = $this->check($check_key);
	    if(!$userInfo['exchange_pwd']){
	    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
	    }
    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
    		$key = $this->input->post('key');
	    	$pwd_key = $this->config->item('pwd_key');
			$id = $this->decrypt($key, $pwd_key);
			
    		$repeatInfo = $this->point_model->getRepeatById($id);
    	    if($repeatInfo !== null && time() >= strtotime($repeatInfo['begin_time']) && time() <= strtotime($repeatInfo['end_time'])){
	    		$userInfo = $this->member_model->getDataById($userInfo['id']); 	
			    $shoot_point = intval($this->input->post('shoot_point'));
			    if($shoot_point){
				    if($userInfo['useable_point'] >= $shoot_point){
				    	$rest_point = $repeatInfo['limit_point'] - $repeatInfo['already_shot_point'];
				    	if($rest_point >= $shoot_point){
						    $future_point = $shoot_point * $repeatInfo['rate'];
						    $active_timestamp = time() + $repeatInfo['day'] * 24 * 60 * 60;
						    $active_time = date('Y-m-d H:i:s', $active_timestamp);
						    $order_id = $this->createRandNum();
					        $result = $this->point_model->do_repeat($repeatInfo, $shoot_point, $future_point, $active_time, $userInfo['id'], $order_id);
					        if($result !== null){
							    $return = array("code"=>0, "message"=>"操作成功！");
							}
							else{
								$return = array("code"=>10001, "message"=>"操作失败！");
							}
				    	}
				    	else{
				    		$return = array("code"=>10004, "message"=>"复投积分不能大于可用额度");
				    	}
				    }
				    else{
				    	$return = array("code"=>10002, "message"=>"可用积分不能大于复投积分");
				    }
			    }
			    else{
			    	$return = array("code"=>10003, "message"=>"参数错误！");
			    }
    	    }
    	    else{
    	    	$return = array("code"=>10009, "message"=>"获取数据出错");
    	    }
    	}
    	else{
    		$return = array("code"=>10006, "message"=>"二级密码错误");
    	}
    	
    	echo json_encode($return);
		exit();
	}
	
    public function do_able_consume()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
		$point = intval($this->input->post('point'));
		$check_key = $this->input->post('check_key');
    	$hash = $this->check($check_key);
    	
	    if(!$userInfo['exchange_pwd']){
	    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
	    }
    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
	    	if($point){   
	    		$userInfo = $this->member_model->getDataById($userInfo['id']); 	
			    if($userInfo['useable_point'] >= $point){
			    	$order_id = $this->createRandNum();
			        $result = $this->point_model->do_able_consume($order_id, $point, $userInfo['id']);
			        if($result !== null){
					    $return = array("code"=>0, "message"=>"操作成功！");
					}
					else{
						$return = array("code"=>10001, "message"=>"操作失败！");
					}
			    }
			    else{
			    	$return = array("code"=>10002, "message"=>"可用积分不能大于待转积分");
			    }
	    	}
	    	else{
	    		$return = array("code"=>10003, "message"=>"参数错误！");
	    	}
    	}
    	else{
    		$return = array("code"=>10006, "message"=>"二级密码错误");
    	}
    	echo json_encode($return);
		exit();
	}
	
    private function createRandNum($str = '')
    {
        return $str.date('YmdHis') . rand(100000, 999999);
    }
    
    public function useable_data_by_page()
	{
		$this->auth_json_member_or_service();
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $type = $this->input->post('type');
	    if($type == 'member'){
	    	$userInfo = $this->user;
	    }
	    else{
	    	$userInfo = $this->userService;
	    }
	    
        $result = $this->point_model->useable_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function do_transfer_consume()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
		$point = intval($this->input->post('point'));
		$mobile = $this->input->post('mobile');
		$check_key = $this->input->post('check_key');
		
		if($point && $mobile && $check_key){
	    	$hash = $this->check($check_key);
		    if(!$userInfo['exchange_pwd']){
		    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
		    }
	    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
			    $transfer_user = $this->member_model->getDataByMobile($mobile);
	
		    	if($transfer_user !== null){
		    		if($transfer_user['level'] > 0){
			    		$order_id = $this->createRandNum();
			    		$userInfo = $this->member_model->getDataById($userInfo['id']); 	
						    
					    if($userInfo['consume_point'] >= $point){
					        $result = $this->point_model->do_transfer_consume($point, $userInfo['id'], $transfer_user['id'], $order_id);
					        if($result !== null){
							    $return = array("code"=>0, "message"=>"操作成功！");
							}
							else{
								$return = array("code"=>10001, "message"=>"操作失败！");
							}
					    }
					    else{
					    	$return = array("code"=>10002, "message"=>"转让积分不足");
					    }
		    		}
		    		else{
		    			$return = array("code"=>10003, "message"=>"转让用户不能是普通会员");
		    		}
		    	}
		    	else{
		    		$return = array("code"=>10004, "message"=>"转让用户不存在！");
		    	}
	    	}
	    	else{
	    		$return = array("code"=>10005, "message"=>"二级密码错误");
	    	}
		}
		else{
			$return = array("code"=>10006, "message"=>"参数错误！");
		}
    	echo json_encode($return);
		exit();
	}
	
	protected function check($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
    public function consume_data_by_page()
	{
		$this->auth_json_member_or_service();
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $type = $this->input->post('type');
	    if($type == 'member'){
	    	$userInfo = $this->user;
	    }
	    else{
	    	$userInfo = $this->userService;
	    }
		
        $result = $this->point_model->consume_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function wait_data_by_page()
	{
		$this->auth_json_member_or_service();
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $type = $this->input->post('type');
	    if($type == 'member'){
	    	$userInfo = $this->user;
	    }
	    else{
	    	$userInfo = $this->userService;
	    }
	    
        $result = $this->point_model->wait_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function share_data_by_page()
	{
		$this->auth_json_member_or_service();
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $type = $this->input->post('type');
	    if($type == 'member'){
	    	$userInfo = $this->user;
	    }
	    else{
	    	$userInfo = $this->userService;
	    }
	    
        $result = $this->point_model->share_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function exchange_data_by_page()
	{
		$this->auth_json_member_or_service();
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $type = $this->input->post('type');
	    if($type == 'member'){
	    	$userInfo = $this->user;
	    }
	    else{
	    	$userInfo = $this->userService;
	    }
	    
        $result = $this->point_model->exchange_data_by_page($userInfo['id'], $page, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
	public function repeatlist(){
    	$this->auth_redirect_member();
		$userInfo = $this->user;
    	$data = array();
    	
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
	    		$data['schedule'] = $schedule;
	    		$data['userInfo'] = $userInfo;
	    		$data['title'] = '积分复投列表';
				$data['page_id'] = 'personal-page';
				$data['menu'] = 'personal';
				$data['env'] = $this->env;
				
				$data['page_css'] = array();
				$data['page_js'] = array();
				
				//页面底部最后加载的js
				$data['page_detail_js'] = array(
				    '/media/js/point/repeatlist.js?v='.rand(1,10).'.'.rand(1,10)
				);
				$data['systemInfo'] = $this->systemInfo;
			    $this->load->view('header', $data);
			    $this->load->view('point/repeatlist', $data);
			    $this->load->view('footer', $data);
    		}
    		else{
    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">复投设置不一致</div>';
    		    exit;
    		}
    	}
    	else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">请先设置排期</div>';
    		exit;
    	}
    }
    
    public function repeat_data_by_page()
	{
		$this->auth_json_member();
		$type = $this->input->post('type');
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $userInfo = $this->user;

        $result = $this->point_model->repeat_data_by_page($userInfo['id'], $page, $num, $type);
        if($result !== null){
        	foreach($result['data'] as $key=>$item){
        		$result['data'][$key]['rate'] = round($item['rate'], 2);
        		$pwd_key = $this->config->item('pwd_key');
		    	$result['data'][$key]['key'] = $this->encrypt($item['id'], $pwd_key);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
}