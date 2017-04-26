<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('admin_model');
    }
    
    public function index(){
    	$this->load->model('system_model');
    	$host = $this->getPageHost();
    	$system = $this->system_model->getSystemData();
    	$hash_host = md5('cnvp'.$host.'goodluck');

    	$data = array();
    	if($system && isset($system['privilege_key']) && $system['privilege_key'] == $hash_host){
    		$data['title'] = $system['title'];
	    	$data['debug'] = $this->env;
			$this->load->view('admin/index', $data);
    	}
    	else{
    		$this->load->view('admin/noprivilege', $data);
    	}
    }
    
   /**
	 * signin.
	 */
	public function signin()
	{
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		$data = array(
			"name"=>$name,
			"password"=>$password
		);
		
		$result = $this->admin_model->signin($data);
		if($result != null){
			if($result['is_lock'] == 0){
				$operater_id = $result['id'];
			    $operater_name = $result['name'];
			    $operater_desc = $result['role_name'].'登录';
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
	
				$this->session->admin = $result;
			    $return = array("code"=>0,"data"=>$result, "message"=>"登录成功！");
			}
			elseif($result['is_lock'] == 1){
				$return = array("code"=>10009, "message"=>"该账号已被锁定！");
			}
		}
		else{
			$this->session->admin = '';
			$return = array("code"=>10001,"data"=>$result, "message"=>"登录失败！");
		}
		
		echo json_encode($return);
		exit();
	}
	
   /**
	 * 登出页面.
	 * 管理员退出
	 */
	public function logout()
	{
		$this->session->admin = '';
    	$return = array("code"=>0, "message"=>"管理员退出成功！");
    	echo json_encode($return);
		exit();
	}
	
    public function chgpwd()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $password = $this->input->post('password');
    	    $newpassword = $this->input->post('newpassword');
    	    $data = array(
				"id"=>$id,
				"password"=>$password,
    	        "newpassword"=>$newpassword
			);
    	    $result = $this->admin_model->chgpwd($data);
	        if($result){
			    $return = array("code"=>0, "message"=>"修改成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"修改失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function selectAdminPageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $num = $this->input->post('num');
    	    $result = $this->admin_model->selectAdminPageData($searchkey, $offset, $num);
	        if($result !== null){
	        	$lastRow = $this->admin_model->selectLastRow();
	        	if($lastRow !== null){
			        $return = array("code"=>0,"data"=>$result, "last"=>$lastRow, "message"=>"获取成功！");
	        	}
	        	else{
	        		$return = array("code"=>10001, "data"=>null, "message"=>"获取失败！");
	        	}
			}
			else{
				$return = array("code"=>10002, "data"=>null, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function editAdmin()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
			$id = $this->input->post('id');
			$name = $this->input->post('name');
			$mobile = $this->input->post('mobile');
			$password = $this->input->post('password');
			$role_id = $this->input->post('role_id');
			$dep_id = $this->input->post('dep_id');
			$is_lock = $this->input->post('is_lock');
			
			$data = array(
				"name" => $name,
				"mobile" => $mobile,
			    "password" => $password,
			    "role_id" => $role_id,
			    "dep_id" => $dep_id,
			    "is_lock" => $is_lock
			);
			
			if($id != '' && $id != false){
				$extra_param = array(
				    "id"=>$id,
				);
				$data = array_merge($data, $extra_param);
				$result = $this->admin_model->updateAdmin($data);
			}
			else{
				if(!$password){
					$data['password'] = '123456';
				}
				$result = $this->admin_model->insertAdmin($data);
			}
			
			if($result){
			    $return = array("code"=>0,"data"=>$result, "message"=>"修改成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>$result, "message"=>"修改失败！");
			}
    	}
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
		echo json_encode($return);
		exit();
	}
	
    public function deleteAdmin()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $result = $this->admin_model->delete($id);
	        if($result){
			    $return = array("code"=>0, "message"=>"删除成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"删除失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}

    public function chgPersonal()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $mobile = $this->input->post('mobile');
    	    $name = $this->input->post('name');
    	    $data = array(
				"id"=>$id,
				"mobile"=>$mobile,
    	        "name"=>$name
			);
    	    $result = $this->admin_model->chgPersonal($data);
	        if($result){
			    $return = array("code"=>0, "message"=>"修改成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"修改失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function isExist(){
		$name = $this->input->post('name');
		$mobile = $this->input->post('mobile');
		$data = array(
		    'name'=>$name,
		    'mobile'=>$mobile
		);
		$result = $this->admin_model->isExist($data);
		if($result){
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>$result, "message"=>"没有数据！");
		}
		echo json_encode($return);
		exit();
	}
	
    public function getNewMessage()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
    		
    	$waitSendCount = $this->admin_model->getWaitSendData();
    	$waitRefundCount = $this->admin_model->getWaitRefundData();
    	$return = array("code"=>0, "waitSendCount"=>$waitSendCount, "waitRefundCount"=>$waitRefundCount, "message"=>"获取成功！");
	    
    	echo json_encode($return);
		exit();
	}
	
	public function thisWeekMonday()
	{
		$weekday = intval(date('w'), time());
		switch ($weekday){
		    case 1:
		    	$monday = date('Y-m-d', time());
		    	break;
		    case 2:
		    	$monday = date('Y-m-d', time() - 24 * 60 * 60);
		    	break;
		    case 3:
		    	$monday = date('Y-m-d', time() - 2 * 24 * 60 * 60);
		    	break;
		    case 4:
		    	$monday = date('Y-m-d', time() - 3 * 24 * 60 * 60);
		    	break;
		    case 5:
		    	$monday = date('Y-m-d', time() - 4 * 24 * 60 * 60);
		    	break;
		    case 6:
		    	$monday = date('Y-m-d', time() - 5 * 24 * 60 * 60);
		    	break;
		    case 0:
		    	$monday = date('Y-m-d', time() - 6 * 24 * 60 * 60);
		    	break;
            default :
            	$monday = date('Y-m-d', time());
                break;
		}
		return $monday;
	}

   /**
	 * 管理员获取轮播图列表记录.
	 */
    public function allBannerData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $result = $this->admin_model->allBannerData();
        if($result !== null){
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}

	public function deleteBanner()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $result = $this->admin_model->deleteBanner($id);
        if($result){
		    $return = array("code"=>0,"data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function editBanner()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$url = $this->input->post('url');
		$order_id = $this->input->post('order_id');
		$link = $this->input->post('link');
		$data = array(
		    "name"=>$name,
		    "url"=>$url,
		    "order_id"=>$order_id,
		    "link"=>$link,
		);
		
		if($id != '' && $id != false){
			$extra_param = array(
			    "id"=>$id,
			);
			$data = array_merge($data, $extra_param);
			$result = $this->admin_model->updateBanner($data);
		}
		else{
			$result = $this->admin_model->insertBanner($data);
		}
		
		if($result){
		    $return = array("code"=>0,"data"=>$result, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>$result, "message"=>"修改失败！");
		}
    	
		echo json_encode($return);
		exit();
	}
	
    public function getCashPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
	    $userid = $this->input->post('userid');
        $result = $this->admin_model->getCashPageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
            foreach($result['data'] as $key=>$item){
        		$result['data'][$key]['money'] = round($item['money'] , 2);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteCashBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteCashBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除现金记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getCommisionPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $userid = $this->input->post('userid');
	    $type = $this->input->post('type');
        $result = $this->admin_model->getCommisionPageData($userid, $start, $end, $searchkey, $offset, $num, $type);
        if($result !== null){
            foreach($result['data'] as $key=>$item){
        		$result['data'][$key]['commision'] = round($item['commision'] , 2);
        		$result['data'][$key]['order_money'] = round($item['order_money'] , 2);
        		$result['data'][$key]['pay_money'] = round($item['pay_money'] , 2);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteCommisionBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteCommisionBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除佣金记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getExchangePageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$userid = $this->input->post('userid');
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
        $result = $this->admin_model->getExchangePageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteExchangeBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteExchangeBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除购物券记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getConsumePageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
	    $userid = $this->input->post('userid');
        $result = $this->admin_model->getConsumePageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteConsumeBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteConsumeBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除消费积分，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getUseablePageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
	    $userid = $this->input->post('userid');
        $result = $this->admin_model->getUseablePageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteUseableBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteUseableBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除可用积分记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getSharePageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
	    $userid = $this->input->post('userid');
        $result = $this->admin_model->getSharePageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteShareBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteShareBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除分享积分记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getWaitPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
	    $userid = $this->input->post('userid');
        $result = $this->admin_model->getWaitPageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteWaitBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteWaitBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除待用积分记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function clearWaitPoint()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$this->load->model('point_model');
        $id = $this->input->post('id');
        $data = $this->point_model->getWaitPointById($id);
        if($data !== null && $data['status'] == 0){
	        $result = $this->point_model->do_clear_wait_point($data);
	        if($result){
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '结算待用积分，id：'.$id;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "data"=>$result, "message"=>"结算成功");
			}
			else{
				$return = array("code"=>10001, "message"=>"结算失败");
			}
        }
        else{
        	$return = array("code"=>10002, "message"=>"数据不存在");
        }
	    
    	echo json_encode($return);
		exit();
	}
	
   /**
	 * 所有订单数据.
	 */
    public function allOrderStatusData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $result = $this->admin_model->allOrderStatusData();
        if($result !== null){
        	$waitPay = 0;
        	$alreadyPay = 0;
        	$alreadySend = 0;
        	$completed = 0;
        	$closed = 0;
        	$refund = 0;
        	$monthOrder = array();
        	$begin = date('Y-m-d', time()-30*24*60*60);
        	
        	foreach($result as $item){
        		if($item['status'] == 0){
        			$waitPay++;
        		}
        		elseif($item['status'] == 1){
        			$alreadyPay++;
        		}
        	    elseif($item['status'] == 2){
        			$alreadySend++;
        		}
        	    elseif($item['status'] == 3){
        			$completed++;
        		}
        	    elseif($item['status'] == 4){
        			$closed++;
        		}
        	    elseif($item['status'] == 7){
        			$refund++;
        		}
        		
        		$itemTime = strtotime($item['created_at']);
        		$start = strtotime(date('Y-m-d', time() - 30*24*60*60).' 00:00:00');
        	    $end = strtotime(date('Y-m-d', time() - 24*60*60).' 23:59:59');
        		if($itemTime >= $start && $itemTime <= $end && $item['status'] != 0){
        			$monthOrder[] = $item;
        		}
        	}
        	
            $monthData = array();
        	if(!empty($monthOrder)){
        		for($i=0; $i<30; $i++){
        			$index = date('m-d', strtotime($begin) + 24 * 60 * 60 * $i);
        			$monthData[$index] = 0;
        		}
        		
        		foreach ($monthOrder as $item){
        			$index = substr($item['created_at'], 5, 5);
        			$monthData[$index]++;
        		}
        	}

        	$orderNum = array(
        	    "waitPay"=>$waitPay,
	        	"alreadyPay"=>$alreadyPay,
	        	"alreadySend"=>$alreadySend, 
	        	"completed"=>$completed, 
	        	"closed"=>$closed,
        	    "refund"=>$refund
        	);
		    $return = array("code"=>0, "data"=>$result, "allOrderNum"=>count($result), "statusNum"=>$orderNum, "monthData"=>$monthData, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
	/**
	 * 过去一个月现金流
	 */
    public function welcomeCashData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$start = date('Y-m-d', time()-30*24*60*60);
		
		$monthOrderData = array();
        $resOrder = $this->admin_model->welcomeOrderData();
        if($resOrder !== null){
        	if(!empty($resOrder)){
        		for($i=0; $i<30; $i++){
        			$index = date('m-d', strtotime($start) + 24 * 60 * 60 * $i);
        			$monthOrderData[$index] = 0;
        		}
        		
        		foreach ($resOrder as $key=>$item){
        			$index = substr($item['created_at'], 5, 5);
        			$monthOrderData[$index] = $monthOrderData[$index] + round($item['money'], 2);
        		}
        	}
		}
	
		$result = array(
            "monthOrderData"=>$monthOrderData
        );
        
        $return = array("code"=>0, "data"=>$result);
	   
    	echo json_encode($return);
		exit();
	}
	
   /**
	 * 管理员获取所有现金.
	 */
    public function allCashData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $result = $this->admin_model->allCashData();
        if($result !== null){
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
	public function getPageHost(){  
	    $url = $_SERVER['HTTP_HOST'];  
	    return $url;  
	} 
	
    public function getUpgradePageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $time = $this->input->post('time');
	    $num = $this->input->post('num');
        $result = $this->admin_model->getUpgradePageData($time, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteUpgradeBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteUpgradeBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除升级记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function quickDeclaration()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $mobile = $this->input->post('mobile');
        $money = $this->input->post('money');
        
        $this->load->model('member_model');
        $this->load->model('setting_model');
        $recharge_user = $this->member_model->getDataByMobile($mobile);
		if($recharge_user !== null){
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
		    		$order_id = $this->createRandNum();
			        $result = $this->admin_model->quickDeclaration($mobile, $money, $schedule, $recharge_user['router'], $recharge_user['id'], $order_id, $commisionSetting);
			        if($result){
			        	$operater_id = $userInfo['id'];
					    $operater_name = $userInfo['name'];
					    $operater_desc = '快速报单，mobile：'.$mobile.',monry:'.$money;
					    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
					    $return = array("code"=>0, "message"=>"报单成功！");
					}
					else{
						$return = array("code"=>10001, "message"=>"报单失败！");
					}
	    		}
	    		else{
	    			$return = array("code"=>10004, "message"=>"复投设置不一致");
	    		}
	    	}
	    	else{
	    		$return = array("code"=>10002, "message"=>"请先设置排期");
	    	}
		}
		else{
			$return = array("code"=>10003, "message"=>"该手机号码用户不存在");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    private function createRandNum($str = '')
    {
        return $str.date('YmdHis') . rand(100000, 999999);
    }
    
	public function batchMemberImport()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$url = $this->input->post('url');
		
		if(!isset($url) || $url == '' || $url == null){
			$message = "没有文件！";
			$return = array("code"=>10004, "message"=>$message);
		}
		else{
			if(strpos($url, '.') !== false){
				$file_types = explode( ".", $url );
                $file_type = $file_types [count ( $file_types ) - 1];
                if($file_type == 'xlsx' || $file_type == 'xls'){
                	$this->load->library('PHPExcel');
		            $this->load->library('PHPExcel/IOFactory');
                    $uploadfile = APPPATH.'/../'.$url;
                    if($file_type == 'xlsx'){
					    $objReader = IOFactory::createReader('Excel2007');
                    }
                    elseif($file_type == 'xls'){
                    	$objReader = IOFactory::createReader('Excel5');
                    }
					$objPHPExcel = $objReader->load($uploadfile);
					$sheet = $objPHPExcel->getSheet(0);
					$highestRow = $sheet->getHighestRow(); // 取得总行数
					$highestColumn = $sheet->getHighestColumn(); // 取得总列数
					if($highestRow >=2){
						//循环读取excel文件,读取一条,插入一条
						$data = array();
						for($j=2; $j<=$highestRow; $j++)
						{
							$str = '';
							$i = 0;
							for($k='A'; $k<=$highestColumn; $k++)
							{
								if($i < 26){
									$i++;
									$value = $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();//读取单元格
									if($k == 'M'){
										//日期
										$value = gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP($value));
									}
									if($i < 26){
									   $str .= $value.'\\';
									}
									else{
										$str .= $value;
									}
								}
								else{
									break;
								}
							}
							$strArray = explode("\\",$str);
							if(isset($strArray) && !empty($strArray) && $strArray[0]){
							    $data[] = $strArray;
							}
						}

						if(count($data) > 0){
							$this->load->model('member_model');
							$mobileArray = array();
							$successOrderNum = 0;
							$failOrderNum = 0;
							$totalOrderNum = count($data);
							foreach($data as $key=>$item){
								//上级
                                $parent = $this->member_model->getDataByMobile($item[4]);
                                $isHave = $this->member_model->getDataByMobile($item[2]);

								if($parent !== null && $isHave === null){
									$insertResult = $this->member_model->insertImportUser($item, $parent);
									if($insertResult === null){
										$failOrderNum++;
										$failRow = $key+1;
										continue;
									}
									else{
										$successOrderNum++;
									}
								}
								else{
									$failOrderNum++;
									continue;
								}
							}
							
							if($successOrderNum > 0){
								if($failOrderNum == 0){
								    $message = "总导入数据".$totalOrderNum."条，".$successOrderNum."条数据导入成功！";
								}
								else{
									$message = "总导入数据".$totalOrderNum."条，".$successOrderNum."条数据导入成功，".$failOrderNum."条数据导入失败！";
								}
								$return = array("code"=>0, "message"=>$message);
							}
							else{
								$message = "导入失败,请检查数据是否设置正确！";
								$return = array("code"=>10001, "message"=>$message);
							}
						}
						else{
							$message = "没有数据！";
						    $return = array("code"=>10002, "message"=>$message);
						}
					}
					else{
						$message = "没有数据！";
						$return = array("code"=>10003, "message"=>$message);
					}
                }
                else{
                	$message = "文件不是excel文件！";
			        $return = array("code"=>10006, "message"=>$message);
                }
			}
			else{
			    $message = "文件格式出错！";
			    $return = array("code"=>10005, "message"=>$message);
			}
		}
    	
    	echo json_encode($return);
		exit();
	}
	
    public function manager_status()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        
        $result = $this->admin_model->manager_status($id, $value);
        if($result){
		    $return = array("code"=>0, "message"=>"修改成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function company_status()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        
        $result = $this->admin_model->company_status($id, $value);
        if($result){
		    $return = array("code"=>0, "message"=>"修改成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getShareholderGraphData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		set_time_limit(30);
        $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    
	    $graphData = array(
	        'online'=>0,
	        'backend_upgrade'=>0,
	        'quick_declaration'=>0
	    );
	    $shareholderList = $this->admin_model->getSshareholderList();
	    if($shareholderList !== null){
	    	$this->load->model('member_model');
	    	foreach($shareholderList as $key=>$item){
	    		$total = 0;
                $quick_declaration = 0;
                $backend_upgrade = 0;
                $online_declaration = 0;
                
                $all_children = $this->member_model->selectAllChildrenContainSelf($item['router']);
                if($all_children !== null){
                	$childrenIdList = array();
                	foreach($all_children as $child){
                		array_push($childrenIdList, $child['id']);
                	}
		    	    
		    	    foreach($childrenIdList as $uid){
		                $upgradeDeclarationBackend = $this->admin_model->getUpgradeDeclarationBackend($uid, $start, $end);
		                $upgradeDeclarationQuick = $this->admin_model->getUpgradeDeclarationQuick($uid, $start, $end);
		                $onlineDeclaration = $this->admin_model->getOnlineDeclarationData($uid, $start, $end);
		                
		                //后台升级
		                $backend_upgrade = $backend_upgrade + $upgradeDeclarationBackend;
		                
		                //快速报单
		                $quick_declaration = $quick_declaration + $upgradeDeclarationQuick;
		                
		                //在线报单
		                $online_declaration = $online_declaration + $onlineDeclaration;
		    	        $total = $total + $onlineDeclaration + $backend_upgrade + $quick_declaration;
		    	    }
                }
	    	    
	    	    $shareholderList[$key]['total'] = $total;
	    	    $shareholderList[$key]['quick_declaration'] = $quick_declaration;
	    	    $shareholderList[$key]['backend_upgrade'] = $backend_upgrade;
	    	    $shareholderList[$key]['online_declaration'] = $online_declaration;
	    	    unset($shareholderList[$key]['openid']);
	    	    unset($shareholderList[$key]['address']);
	    	    unset($shareholderList[$key]['business_bind_time']);
	    	    unset($shareholderList[$key]['card_no']);
	    	    unset($shareholderList[$key]['exchange_pwd']);
	    	    unset($shareholderList[$key]['password']);
	    	    unset($shareholderList[$key]['headimgurl']);
	    	    unset($shareholderList[$key]['router']);
	    	    unset($shareholderList[$key]['service_bind_time']);
	    	    unset($shareholderList[$key]['service_openid']);
	    	}
	    	
	        $return = array("code"=>0, "data"=>$shareholderList, "message"=>"获取成功！");
	    }
	    else{
	    	$return = array("code"=>10001, "message"=>"没有股东");
	    }
    
    	echo json_encode($return);
		exit();
	}
	
	public function serviceDeclaration()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$this->load->model('member_model');
        $this->load->model('setting_model');
        $service_mobile = $this->input->post('service_mobile');
        $vip_mobile = $this->input->post('vip_mobile');
        $point = $this->input->post('point');
        $money = $this->input->post('money');
        
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
		        $service_user = $this->member_model->getDataByMobile($service_mobile);
		        if($service_user !== null && $service_user['level'] >= 11){
		        	$recharge_user = $this->member_model->getDataByMobile($vip_mobile);
		        	if($recharge_user !== null){
		        		$flag = false;
				    	if(strstr($recharge_user['router'], ',')){
				    	    $router_array = explode(',', $recharge_user['router']);
				    	    $last = count($router_array) - 1;
				    	    unset($router_array[$last]);
				    	
				    	    if(in_array($service_user['id'], $router_array)){
				    	    	$flag = true;
				    	    }
				    	    else{
				    	    	$flag = false;
				    	    }
				    	}
				    	
					    if($flag){
			        		$order_id = $this->createRandNum();
			        		if($service_user['consume_point'] >= $point){
								$rate_point = $money * $schedule['use_consume_most_rate'] * 0.01;
								if(!$point || ($point && $rate_point >= $point)){
									$result = $this->admin_model->serviceDeclaration($point, $money, $service_user['id'], $recharge_user, $order_id, $schedule, $commisionSetting);
									if($result !== null){
									    $return = array("code"=>0, "message"=>"报单成功");
									}
									else{
										$return = array("code"=>10001, "message"=>"报单失败");
									}
								}
			        		    else{
						    		$msg = '消费积分比例不能大于'.$schedule['use_consume_most_rate'].'%';
						    		$return = array("code"=>10006, "message"=>$msg);
						    	}
			        		}
			        		else{
			        			$return = array("code"=>10002, "message"=>"消费积分不足");
			        		}
					    }
					    else{
					    	$return = array("code"=>10009, "message"=>"报单人必须是服务中心的下级");
					    }
		        	}
		        	else{
		        		$return = array("code"=>10003, "message"=>"报单人不存在");
		        	}
		        }
		        else{
		        	$return = array("code"=>10004, "message"=>"服务中心号码错误");
		        }
    		}
    		else{
    			$return = array("code"=>10006, "message"=>"复投设置不一致");
    		}
        }
        else{
        	$return = array("code"=>10005, "message"=>"排期未设置");
        }
        
        echo json_encode($return);
		exit();
	}
	
    public function getTakecashPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $userid = $this->input->post('userid');
	    $status = $this->input->post('status');
        $result = $this->admin_model->getTakecashPageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteTakecashBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteTakecashBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除提现记录idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function checkTakecash()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $result = $this->admin_model->checkTakecash($id, $status);
        if($result){
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $status_value = '';
		    if($status == 1){
		    	$status_value = '通过';
		    }
            elseif($status == 2){
		    	$status_value = '不通过';
		    }
		    
		    $operater_desc = '审核提现id：'.$id.'，结果：'.$status_value;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "message"=>"提交成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"提交失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getWalletPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$userid = $this->input->post('userid');
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
        $result = $this->admin_model->getWalletPageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
            foreach($result['data'] as $key=>$item){
        		$result['data'][$key]['money'] = round($item['money'] , 2);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteWalletBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteWalletBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除支出记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getMessagePageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$userid = $this->input->post('userid');
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $time = $this->input->post('time');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
        $result = $this->admin_model->getMessagePageData($userid, $status, $time, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteMessageBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteMessageBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除短信记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getLogisticPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
        $result = $this->admin_model->getLogisticPageData($status, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteLogisticBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteLogisticBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除邮费设置，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function updateLogistic()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $is_lock = $this->input->post('is_lock');
        $template = $this->input->post('template');
        if($id){
            $result = $this->admin_model->updateLogistic($id, $name, $is_lock, $template);
        }
        else{
        	$result = $this->admin_model->insertLogistic($name, $is_lock, $template);
        }
        
        if($result){
		    $return = array("code"=>0, "data"=>$result, "message"=>"操作成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"操作失败");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getLogisticList()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;

        $result = $this->admin_model->getLogisticList();
        if($result){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function quickBusiness()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $mobile = $this->input->post('mobile');
        $company_get_type = $this->input->post('company_get_type');
        $point = $this->input->post('point');
        $mark = $this->input->post('mark');
        
        $this->load->model('member_model');
        $recharge_user = $this->member_model->getDataByMobile($mobile);
		if($recharge_user !== null){
			if($recharge_user['is_company'] == 1){
			    $order_id = $this->createRandNum();
			    if($company_get_type == 2 || $company_get_type == 3){
			        $result = $this->admin_model->quickBusiness($company_get_type, $point, $recharge_user['parent_id'], $recharge_user['id'], $order_id, $mark);
			        if($result){
			        	$operater_id = $userInfo['id'];
					    $operater_name = $userInfo['name'];
					    $operater_desc = '商家结算，mobile：'.$mobile.',point:'.$point;
					    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
					    $return = array("code"=>0, "message"=>"结算成功！");
					}
					else{
						$return = array("code"=>10001, "message"=>"结算失败！");
					}
			    }
			    else{
			    	$return = array("code"=>10002, "message"=>"结算类型错误");
			    }
			}
			else{
				$return = array("code"=>10003, "message"=>"该手机号码用户不是商家");
			}
		}
		else{
			$return = array("code"=>10004, "message"=>"该手机号码用户不存在");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
	public function configDeclaration()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $config = $this->input->post('config');
        $result = $this->admin_model->configDeclaration($config);
	    if($result){
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '报单设置';
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "message"=>"设置成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"设置失败");
		}
		
		echo json_encode($return);
		exit();
	}
	
    public function getConfigDeclaration()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $result = $this->admin_model->getConfigDeclaration();
        if($result !== null){
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
    public function posterData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$type = $this->input->post('type');
        $result = $this->admin_model->posterData($type);
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "data"=>null, "message"=>"没有数据！");
		}

    	echo json_encode($return);
		exit();
	}
	
    public function editPoster()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$id = $this->input->post('id');
		$json_data = json_encode($this->input->post('json_data'));
		$bg_url = $this->input->post('bg_url');
		$type = $this->input->post('type');
        $result = $this->admin_model->editPoster($id, $json_data, $bg_url, $type);
        if($result !== null){
		    $return = array("code"=>0, "message"=>"保存成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"保存失败");
		}

    	echo json_encode($return);
		exit();
	}
	
    public function getMerchantStatisticPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $sort_word = $this->input->post('sort_word');
	    $sort_type = $this->input->post('sort_type');
	    $all_data = array();
	    $merchantList = $this->admin_model->getMerchantList($searchkey);
	    if($merchantList !== null){
	    	foreach($merchantList as $key=>$item){
	    		$merchantOrder = $this->admin_model->getMerchantOrder($item['id'], $start, $end);
	    		if($merchantOrder !== null){
	    			$temp = array(
	    			    "total" => 0,
		    			"exchange_point" => 0,
		    			"consume_point" => 0,
	    			    "merchantUseablePoint" => 0,
	    			    "merchantConsumePoint" => 0,
		    			"logistic_fee" => 0,
	    			    "total_cash" => 0,
	    			    "cash" => 0
	    			);
	    			foreach($merchantOrder as $dataKey => $dataItem){
	    				$temp['total'] = $dataItem['total'] + $temp['total'];
	    				$temp['logistic_fee'] = $dataItem['logistic'] + $temp['logistic_fee'];
	    				$temp['total_cash'] = $dataItem['cash'] + $temp['total_cash'];
	    				if($dataItem['goods_model'] == 1){
	    				    $temp['exchange_point'] = $dataItem['point'] + $temp['exchange_point'];
	    				}
	    				else{
	    				    $temp['consume_point'] = $dataItem['point'] + $temp['consume_point'];
	    				}
	    			}
	    			
	    			if($temp['total'] > 0){
	    				$merchantUseablePoint = $this->admin_model->getMerchantUseablePoint($item['id'], $start, $end);
	    				$merchantConsumePoint = $this->admin_model->getMerchantConsumePoint($item['id'], $start, $end);
	    				$temp['merchantUseablePoint'] = $merchantUseablePoint;
	    				$temp['merchantConsumePoint'] = $merchantConsumePoint;
	    				$temp['cash'] = $temp['total_cash'] - $temp['logistic_fee'];
	    				$temp['user_id'] = $item['id'];
	    				$temp['name'] = $item['name'];
	    				$temp['mobile'] = $item['mobile'];
	    			    $all_data[] = $temp;
	    			}
	    		}
	    	}
	    }

        if(!empty($all_data)){
        	$all_data = $this->maopaoSortMerchant($all_data, $sort_word, $sort_type);
        	$result = array();
        	$result['count'] = count($all_data);
        	$result['data'] = array();
        	$result['total_order'] = 0;
        	$result['total_consume'] = 0;
        	$result['total_exchange'] = 0;
        	$result['total_cash'] = 0;
        	$result['total_logistic'] = 0;
        	$result['total_com_useable'] = 0;
        	$result['total_com_consume'] = 0;
        	foreach($all_data as $key=>$item){
        		if($key >= $offset && ($key < ($offset + $num))){
        			$result['data'][] = $item;
        		}
        		$result['total_order'] = $result['total_order'] + $item['total'];
	        	$result['total_consume'] = $result['total_consume'] + $item['consume_point'];
	        	$result['total_exchange'] = $result['total_exchange'] + $item['exchange_point'];
	        	$result['total_cash'] = $result['total_cash'] + $item['cash'];
	        	$result['total_logistic'] = $result['total_logistic'] + $item['logistic_fee'];
	        	$result['total_com_useable'] = $result['total_com_useable'] + $item['merchantUseablePoint'];
	        	$result['total_com_consume'] = $result['total_com_consume'] + $item['merchantConsumePoint'];
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
	private function maopaoSortMerchant(array $numbers, $sort_word='total', $sort_type='desc'){
    	$cnt = count($numbers);
    	if($cnt == 0){
    		return array();
    	}
    	elseif($cnt == 1){
    		return $numbers;
    	}
    	else{
			for($i=0; $i<$cnt-1; $i++){//循环比较
				for($j=$i+1; $j<$cnt; $j++){
					if($sort_type == 'desc'){
						if($numbers[$j][$sort_word] > $numbers[$i][$sort_word]){//执行交换
							$temp = $numbers[$i];
							$numbers[$i] = $numbers[$j];
							$numbers[$j] = $temp;
						}
					}
					else{
					    if($numbers[$j][$sort_word] < $numbers[$i][$sort_word]){//执行交换
							$temp = $numbers[$i];
							$numbers[$i] = $numbers[$j];
							$numbers[$j] = $temp;
						}
					}
				}
			}
    	}
		return $numbers;
    }
    
    public function insertCompensation()
	{
		$this->auth_json_admin();
		$this->load->model('member_model');
		$userInfo = $this->admin;
		$mobile = $this->input->post('mobile');
		$limit_point = trim($this->input->post('limit_point'));
		$rate = trim($this->input->post('rate'));
		$day = trim($this->input->post('day'));
		$begin_time = trim($this->input->post('begin_time'));
		$end_time = trim($this->input->post('end_time'));
		$auto_to_consume = trim($this->input->post('auto_to_consume'));
		$operater_desc = '';
		$user = $this->member_model->getDataByMobile($mobile);
		if($user !== null){
			$order_id = $this->createRandNum();
			$data = array(
			    "user_id"=>$user['id'],
			    "limit_point"=>$limit_point,
			    "order_id"=>$order_id,
			    "rate"=>$rate,
			    "begin_time"=>$begin_time.' 00:00:00',
			    "end_time"=>$end_time.' 23:59:59',
			    "day"=>$day,
			    "type"=>4, //4补偿
			    "auto_to_consume"=>$auto_to_consume
			);
			
			$result = $this->admin_model->insertCompensation($data);
			$operater_desc = '增加复投机会，ID：'.$result;
			
			if($result){
				$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "message"=>"保存成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"保存失败！");
			}
		}
		else{
			$return = array("code"=>10002, "message"=>"用户不存在");
		}
    	
		echo json_encode($return);
		exit();
	}
	
    public function getRepeatPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $userid = $this->input->post('userid');
	    $is_delete = $this->input->post('is_delete');
	    $type = $this->input->post('type');
        $result = $this->admin_model->getRepeatPageData($userid, $start, $end, $searchkey, $offset, $num, $is_delete, $type);
        if($result !== null){
            foreach($result['data'] as $key=>$item){
        		$result['data'][$key]['rate'] = round($item['rate'] , 2);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteRepeatBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteRepeatBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除复投记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function smsTemplate()
	{
		$this->auth_json_admin();
        $result = $this->admin_model->smsTemplate();
        if($result !== null){
        	$new_reuslt = array();
        	$pattern = '/\{[a-zA-Z_]+\}/';
        	foreach($result as $item){
        		preg_match_all($pattern, $item['placeholder'], $matches);
        		$item['keys'] = $matches[0];
        		$new_reuslt[] = $item;
        	}
		    $return = array("code"=>0, "data"=>$new_reuslt, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function updateTemplate()
	{
		$this->auth_json_admin();
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$type = $this->input->post('type');
		$content = $this->input->post('content');
		$data = array(
		    "type" => $type,
		    "name" => $name,
			"content" => $content,
		);
		
        if($id != '' && $id != false){
			$extra_param = array(
			    "id"=>$id,
			);
			$data = array_merge($data, $extra_param);
			$result = $this->admin_model->updateTemplate($data);
		}
		else{
			$result = $this->admin_model->insertTemplate($data);
		}
					
		if($result){
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
    	
		echo json_encode($return);
		exit();
	}
	
    public function switchTemplateStatus()
	{
		$this->auth_json_admin();
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$data = array(
			"status" => $status,
		    "id"=>$id
		);
		
        $result = $this->admin_model->switchTemplateStatus($data);
		if($result){
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
    	
		echo json_encode($return);
		exit();
	}
	
    public function select_page_article()
	{
		$this->auth_json_admin();
	    $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $num = $this->input->post('num');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
        $result = $this->admin_model->selectPageArticle($start, $end, $searchkey, $offset, $num);
        if($result !== null){
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
    public function edit_article()
	{
		$this->auth_json_admin();
		$this->load->model('article_model');
		$id = $this->input->post('id');
		$title = $this->input->post('title');
		$content = $this->input->post('content');
		$abstracts = $this->input->post('abstracts');
		$img_url = $this->input->post('img_url');
		$second_string = substr(date("Y-m-d H:i:s"), 10);

		$data = array(
			"title"=>$title,
		    "content"=>$content,
			"abstracts"=>$abstracts,
		    "img_url"=>$img_url
		);
		
		if($id != '' && $id != false){
			$extra_param = array(
			    "id"=>$id
			);
			$data = array_merge($data, $extra_param);
			$result = $this->article_model->updateRow($data);
		}
		else{
		    $lastRow = $this->article_model->selectMaxSortRow();
			if($lastRow !== null){
				$sort_id = $lastRow['sort_id'] + 1;
			}
			else{
				$sort_id = 0;
			}
			$data['sort_id'] = $sort_id;
			$result = $this->article_model->insertRow($data);
		}
		
		if($result){
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}

		echo json_encode($return);
		exit();
	}
	
    public function deleteArticleBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteArticleBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除文章，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getMerchantOrderPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $all_data = array();
	    $merchantList = $this->admin_model->getMerchantList($searchkey);
	    if($merchantList !== null){
	    	foreach($merchantList as $key=>$item){
	    		$merchantOrder = $this->admin_model->getMerchantRankingOrder($item['id'], $start, $end);
	    		if($merchantOrder !== null){
	    			$temp = array(
	    			    "uid" => $item['id'],
	    			    "name" => $item['name'],
	    			    "mobile" => $item['mobile'],
	    			    "total" => count($merchantOrder),
		    			"wait_send" => 0,
		    			"already_send" => 0,
	    			    "completed" => 0,
	    			    "refunded" => 0,
		    			"closed" => 0
	    			);
	    			foreach($merchantOrder as $dataKey => $dataItem){
	    				if($dataItem['status'] == 1){
	    				    $temp['wait_send'] = $temp['wait_send'] + 1;
	    				}
	    				elseif($dataItem['status'] == 2){
	    				    $temp['already_send'] = $temp['already_send'] + 1;
	    				}
	    			    elseif($dataItem['status'] == 3){
	    				    $temp['completed'] = $temp['completed'] + 1;
	    				}
	    			    elseif($dataItem['status'] == 4){
	    				    $temp['closed'] = $temp['closed'] + 1;
	    				}
	    			    elseif($dataItem['status'] == 6){
	    				    $temp['refunded'] = $temp['refunded'] + 1;
	    				}
	    			}
	    			
	    			$all_data[] = $temp;
	    		}
	    	}
	    }

        if(!empty($all_data)){
        	$all_data = $this->maopaoSortMerchantWaitSend($all_data);
        	$result = array();
        	$result['count'] = count($all_data);
        	$result['data'] = array();
        	$result['total_order'] = 0;
        	$result['total_wait_send'] = 0;
        	$result['total_already_send'] = 0;
        	$result['total_completed'] = 0;
        	$result['total_closed'] = 0;
        	$result['total_refunded'] = 0;
        	foreach($all_data as $key=>$item){
        		if($key >= $offset && ($key < ($offset + $num))){
        			$result['data'][] = $item;
        		}
        		$result['total_order'] = $result['total_order'] + $item['total'];
	        	$result['total_wait_send'] = $result['total_wait_send'] + $item['wait_send'];
	        	$result['total_already_send'] = $result['total_already_send'] + $item['already_send'];
	        	$result['total_completed'] = $result['total_completed'] + $item['completed'];
	        	$result['total_closed'] = $result['total_closed'] + $item['closed'];
	        	$result['total_refunded'] = $result['total_refunded'] + $item['refunded'];
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    private function maopaoSortMerchantWaitSend(array $numbers){
    	$cnt = count($numbers);
    	if($cnt == 0){
    		return array();
    	}
    	elseif($cnt == 1){
    		return $numbers;
    	}
    	else{
			for($i=0; $i<$cnt-1; $i++){//循环比较
				for($j=$i+1; $j<$cnt; $j++){
					if($numbers[$j]['wait_send'] > $numbers[$i]['wait_send']){//执行交换
						$temp = $numbers[$i];
						$numbers[$i] = $numbers[$j];
						$numbers[$j] = $temp;
					}
				}
			}
    	}
		return $numbers;
    }
    
    public function getSaleRankData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $year = $this->input->post('year');
	    $month = $this->input->post('month');
	    $data = array();
	    if($month != 99){
	    	$result = $this->admin_model->getSaleOrderYearMonthData($year, $month);
	    	if($result !== null){
			    $month_string = '';
		    	if(intval($month) > 10){
		    		$month_string = $month;
		    	}
		    	else{
		    		$month_string = '0'.$month;
		    	}
	    		$begin = $year.'-'.$month_string.'-01 00:00:00';
			    //加一个月
			    $endMonthTimestamp = strtotime("+1 months", strtotime($begin)) - 1;
			    $end_date = intval(date('d', $endMonthTimestamp)); 
			    for($i=1; $i<=$end_date; $i++){
			    	$data[$i]['order_num'] = 0;
		        	$data[$i]['goods_num'] = 0;
		        	$data[$i]['exchange_point'] = 0;
		        	$data[$i]['consume_point'] = 0;
		        	$data[$i]['total_cash'] = 0;
		        	$data[$i]['logistic_fee'] = 0;
		        	$data[$i]['back_useable'] = 0;
		        	$data[$i]['back_consume'] = 0;
		        	$data[$i]['all_cash_within_logistic'] = 0;
			    }
	    
		    	foreach($result as $item){
		    		//日期
			        $index = intval(substr($item['created_at'], 8, 2));
			        $data[$index]['order_num'] = $data[$index]['order_num'] + 1;
		        	$data[$index]['goods_num'] = $data[$index]['goods_num'] + $item['num'];
		        	if($item['goods_model'] == 1){
		        		$data[$index]['exchange_point'] = $data[$index]['exchange_point'] + $item['total_point_price'];
		        	}
		        	else{
		        		$data[$index]['consume_point'] = $data[$index]['consume_point'] + $item['total_point_price'];
		        	}
		        	$data[$index]['all_cash_within_logistic'] = $data[$index]['all_cash_within_logistic'] + $item['total_cash_price'];
		        	$data[$index]['logistic_fee'] = $data[$index]['logistic_fee'] + $item['logistic_fee'];
		        	$tempUseablePoint = $this->admin_model->getOrderUseablePoint($item['order_id']);
		        	$tempConsumePoint = $this->admin_model->getOrderConsumePoint($item['order_id']);
		        	$data[$index]['back_useable'] = $data[$index]['back_useable'] + $tempUseablePoint;
		        	$data[$index]['back_consume'] = $data[$index]['back_consume'] + $tempConsumePoint;
		        	$data[$index]['total_cash'] = $data[$index]['all_cash_within_logistic'] - $data[$index]['logistic_fee'];
		    	}
	    	}
	    }
	    else{
	        $result = $this->admin_model->getSaleOrderYearData($year);
	        if($result !== null){
	        	for($i=1; $i<=12; $i++){
			    	$data[$i]['order_num'] = 0;
		        	$data[$i]['goods_num'] = 0;
		        	$data[$i]['exchange_point'] = 0;
		        	$data[$i]['consume_point'] = 0;
		        	$data[$i]['total_cash'] = 0;
		        	$data[$i]['logistic_fee'] = 0;
		        	$data[$i]['back_useable'] = 0;
		        	$data[$i]['back_consume'] = 0;
		        	$data[$i]['all_cash_within_logistic'] = 0;
	        	}
	        	
		        foreach($result as $item){
		        	//月份
		        	$index = intval(substr($item['created_at'], 5, 2));
		        	$data[$index]['order_num'] = $data[$index]['order_num'] + 1;
		        	$data[$index]['goods_num'] = $data[$index]['goods_num'] + $item['num'];
		        	if($item['goods_model'] == 1){
		        		$data[$index]['exchange_point'] = $data[$index]['exchange_point'] + $item['total_point_price'];
		        	}
		        	else{
		        		$data[$index]['consume_point'] = $data[$index]['consume_point'] + $item['total_point_price'];
		        	}
		        	$data[$index]['all_cash_within_logistic'] = $data[$index]['all_cash_within_logistic'] + $item['total_cash_price'];
		        	$data[$index]['logistic_fee'] = $data[$index]['logistic_fee'] + $item['logistic_fee'];
		        	$tempUseablePoint = $this->admin_model->getOrderUseablePoint($item['order_id']);
		        	$tempConsumePoint = $this->admin_model->getOrderConsumePoint($item['order_id']);
		        	$data[$index]['back_useable'] = $data[$index]['back_useable'] + $tempUseablePoint;
		        	$data[$index]['back_consume'] = $data[$index]['back_consume'] + $tempConsumePoint;
		        	$data[$index]['total_cash'] = $data[$index]['all_cash_within_logistic'] - $data[$index]['logistic_fee'];
		        }
	        }
	    }
	    
        if(!empty($data)){
        	$rtnData = array();
        	$total = array(
        	    'total_order_num'=>0,
	        	'total_goods_num'=>0,
	        	'total_exchange_point'=>0,
	        	'total_consume_point'=>0,
	        	'total_total_cash'=>0,
	        	'total_logistic_fee'=>0,
	        	'total_back_useable'=>0,
	        	'total_back_consume'=>0,
        	    'total_all_cash_within_logistic'=>0
        	);
        	
        	foreach($data as $key=>$item){
        		$total['total_order_num'] = $total['total_order_num'] + $item['order_num'];
        		$total['total_goods_num'] = $total['total_goods_num'] + $item['goods_num'];
        		$total['total_exchange_point'] = $total['total_exchange_point'] + $item['exchange_point'];
        		$total['total_consume_point'] = $total['total_consume_point'] + $item['consume_point'];
        		$total['total_total_cash'] = $total['total_total_cash'] + $item['total_cash'];
        		$total['total_logistic_fee'] = $total['total_logistic_fee'] + $item['logistic_fee'];
        		$total['total_back_useable'] = $total['total_back_useable'] + $item['back_useable'];
        		$total['total_back_consume'] = $total['total_back_consume'] + $item['back_consume'];
        		$total['total_all_cash_within_logistic'] = $total['total_all_cash_within_logistic'] + $item['all_cash_within_logistic'];
        		
        		$rtnData[] = array(
        		    'index'=>$key,
        		    'data'=>$item
        		);
        	}
		    $return = array("code"=>0, "data"=>$rtnData, "total"=>$total, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function message_status()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        
        $result = $this->admin_model->message_status($id, $value);
        if($result){
		    $return = array("code"=>0, "message"=>"修改成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function getMerchantGoodsPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $all_data = array();
	    $merchantList = $this->admin_model->getMerchantList($searchkey);
	    if($merchantList !== null){
	    	foreach($merchantList as $key=>$item){
	    		$merchantGoods = $this->admin_model->getMerchantRankingGoods($item['id'], $start, $end);
	    		if($merchantGoods !== null){
	    			$temp = array(
	    			    "uid" => $item['id'],
	    			    "name" => $item['name'],
	    			    "mobile" => $item['mobile'],
	    			    "total" => count($merchantGoods),
		    			"release" => 0,
		    			"down" => 0,
	    			    "recommend" => 0,
	    			    "timebuy" => 0
	    			);
	    			foreach($merchantGoods as $dataKey => $dataItem){
	    				if($dataItem['is_release'] == 1){
	    				    $temp['release'] = $temp['release'] + 1;
	    				}
	    				elseif($dataItem['is_release'] == 0){
	    				    $temp['down'] = $temp['down'] + 1;
	    				}
	    			    if($dataItem['is_recommend'] == 1){
	    				    $temp['recommend'] = $temp['recommend'] + 1;
	    				}
	    			    if($dataItem['is_time_buy'] == 1 && $dataItem['buy_time']){
	    				    $temp['timebuy'] = $temp['timebuy'] + 1;
	    				}
	    			}
	    			
	    			$all_data[] = $temp;
	    		}
	    	}
	    }

        if(!empty($all_data)){
        	$all_data = $this->maopaoSortMerchantReleaseSend($all_data);
        	$result = array();
        	$result['count'] = count($all_data);
        	$result['data'] = array();
        	$result['total_all'] = 0;
        	$result['total_release'] = 0;
        	$result['total_down'] = 0;
        	$result['total_recommend'] = 0;
        	$result['total_timebuy'] = 0;
        	foreach($all_data as $key=>$item){
        		if($key >= $offset && ($key < ($offset + $num))){
        			$result['data'][] = $item;
        		}
        		$result['total_all'] = $result['total_all'] + $item['total'];
        		$result['total_release'] = $result['total_release'] + $item['release'];
	        	$result['total_down'] = $result['total_down'] + $item['down'];
	        	$result['total_recommend'] = $result['total_recommend'] + $item['recommend'];
	        	$result['total_timebuy'] = $result['total_timebuy'] + $item['timebuy'];
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    private function maopaoSortMerchantReleaseSend(array $numbers){
    	$cnt = count($numbers);
    	if($cnt == 0){
    		return array();
    	}
    	elseif($cnt == 1){
    		return $numbers;
    	}
    	else{
			for($i=0; $i<$cnt-1; $i++){//循环比较
				for($j=$i+1; $j<$cnt; $j++){
					if($numbers[$j]['release'] > $numbers[$i]['release']){//执行交换
						$temp = $numbers[$i];
						$numbers[$i] = $numbers[$j];
						$numbers[$j] = $temp;
					}
				}
			}
    	}
		return $numbers;
    }
    
    public function getGoodsStatisticPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $sort_word = $this->input->post('sort_word');
	    $sort_type = $this->input->post('sort_type');
	    
	    $datalist = $this->admin_model->getGoodsStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type);
	    if($datalist !== null){
	    	$total = array();
        	$total['total_num'] = 0;
        	$total['total_exchange_point'] = 0;
        	$total['total_consume_point'] = 0;
        	$total['total_cash'] = 0;
        	$total['total_logistic_fee'] = 0;
	    	foreach($datalist['data'] as $key=>$item){
	    		$datalist['data'][$key]['cash'] = $item['total_cash_price'] - $item['logistic_fee'];
	    		$total['total_num'] = $total['total_num'] + $item['num'];
	    		$total['total_exchange_point'] = $total['total_exchange_point'] + $item['exchange_point'];
	    		$total['total_consume_point'] = $total['total_consume_point'] + $item['consume_point'];
	    		$total['total_cash'] = $total['total_cash'] + $datalist['data'][$key]['cash'];
	    		$total['total_logistic_fee'] = $total['total_logistic_fee'] + $item['logistic_fee'];
	    	}
        	
		    $return = array("code"=>0, "data"=>$datalist, "total"=>$total, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function getConsumeStatisticPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $sort_word = $this->input->post('sort_word');
	    $sort_type = $this->input->post('sort_type');
	    
	    $datalist = $this->admin_model->getConsumeStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type);
	    if($datalist !== null){
	    	$total = array();
        	$total['total_num'] = 0;
        	$total['total_exchange_point'] = 0;
        	$total['total_consume_point'] = 0;
        	$total['total_cash'] = 0;
        	$total['total_logistic_fee'] = 0;
	    	foreach($datalist['data'] as $key=>$item){
	    		$datalist['data'][$key]['cash'] = $item['total_cash_price'] - $item['logistic_fee'];
	    		$total['total_num'] = $total['total_num'] + $item['num'];
	    		$total['total_exchange_point'] = $total['total_exchange_point'] + $item['exchange_point'];
	    		$total['total_consume_point'] = $total['total_consume_point'] + $item['consume_point'];
	    		$total['total_cash'] = $total['total_cash'] + $datalist['data'][$key]['cash'];
	    		$total['total_logistic_fee'] = $total['total_logistic_fee'] + $item['logistic_fee'];
	    	}
        	
		    $return = array("code"=>0, "data"=>$datalist, "total"=>$total, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function getDeclarationStatisticPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $sort_word = $this->input->post('sort_word');
	    $sort_type = $this->input->post('sort_type');
	    
	    $datalist = $this->admin_model->getDeclarationStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type);
	    if($datalist !== null){
	    	$total = array();
        	$total['total_num'] = 0;
        	$total['total_point'] = 0;
        	$total['total_recharge_money'] = 0;
        	$total['total_pay_money'] = 0;
	    	foreach($datalist['data'] as $key=>$item){
	    		$datalist['data'][$key]['recharge_money'] = round($item['recharge_money'], 2);
	    		$datalist['data'][$key]['pay_money'] = round($item['pay_money'], 2);
	    		$total['total_num'] = $total['total_num'] + $item['num'];
	    		$total['total_point'] = $total['total_point'] + $item['point'];
	    		$total['total_recharge_money'] = $total['total_recharge_money'] + $item['recharge_money'];
	    		$total['total_pay_money'] = $total['total_pay_money'] + $item['pay_money'];
	    	}
        	
		    $return = array("code"=>0, "data"=>$datalist, "total"=>$total, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function getMemberStatisticPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $sort_word = $this->input->post('sort_word');
	    $sort_type = $this->input->post('sort_type');
	    
	    $datalist = $this->admin_model->getMemberStatisticPageData($searchkey, $offset, $num, $start, $end, $sort_word, $sort_type);
	    if($datalist !== null){
	    	$this->load->model('member_model');
	    	$total = array();
        	$total['total_num'] = 0;
        	$total['total_consume_point'] = $datalist['total']['total_consume_point'];
        	$total['total_exchange_point'] = $datalist['total']['total_exchange_point'];
        	$total['total_useable_point'] = $datalist['total']['total_consume_point'];
        	$total['total_wait_point'] = $datalist['total']['total_wait_point'];
        	$total['total_share_point'] = $datalist['total']['total_share_point'];
        	$total['total_commision'] = round($datalist['total']['total_commision'], 2);
        	$total['total_member'] = 0;
        	$total['total_vip'] = 0;
	    	foreach($datalist['data'] as $key=>$item){
	    		$member = $this->member_model->getDirectChildrenNumById($item['id']);
	    		$vip = $this->member_model->getChildrenVipNum($item['id']);
	    		$datalist['data'][$key]['member'] = $member;
	    		$datalist['data'][$key]['vip'] = $vip;
	    		$datalist['data'][$key]['commision'] = round($item['commision'], 2);
	    	}
        	
		    $return = array("code"=>0, "data"=>$datalist, "total"=>$total, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function getSharemanagerPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $num = $this->input->post('num');
	    $status = $this->input->post('status');
	    $userid = $this->input->post('userid');
        $result = $this->admin_model->getSharemanagerPageData($userid, $status, $start, $end, $searchkey, $offset, $num);
        if($result !== null){
        	$this->load->model('goods_model');
            foreach($result['data'] as $key=>$item){
            	$goods = $this->goods_model->selectDataById($item['goods_id']);
        		$result['data'][$key]['goods_name'] = $goods ? $goods['name'] : '--';
        		$salelist = $this->admin_model->selectShareOrderById($item['id']);
        		$result['data'][$key]['salelist'] = $salelist ? $salelist : array();
        		$result['data'][$key]['share_price'] = round($item['share_price'], 2);
        		
        		$used_point = 0;
	        	$geted_commision = 0;
                $back_point = 0;
	        	if($item['status'] == 1 && $item['rest_num'] > 0){
	        		$back_point = $item['single_point'] * $item['rest_num'];
	        	}
	        	if($salelist !== null){
	                foreach($salelist as $sale){
	        			if($sale['status'] == 3 && $sale['is_clear_share'] == 1){
	        				$used_point = $used_point + $sale['point_price'] * $sale['num'];
						    $total_cash_price = $sale['cash_price'] * $sale['num'];
							$total_share_price = $sale['share_price'] * $sale['num'];
							if($total >= $total_share_price){
							    $commision = $total_cash_price - $total_share_price;
							}
							else{
								$commision = $total_share_price - $total_cash_price;
							}
	        				$geted_commision = $geted_commision + $commision;
	        			}
	        		}
	        	}
	        	$result['data'][$key]['used_point'] = $used_point;
	        	$result['data'][$key]['geted_commision'] = $geted_commision;
	        	$result['data'][$key]['back_point'] = $back_point;
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteSharemanagerBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->admin_model->deleteSharemanagerBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除分享管理记录，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function dealSharemanager()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $this->load->model('share_model');
        $share = $this->share_model->selectShareById($id);
	    if($share !== null && $share['status'] == 0 && $share['rest_num'] > 0){
	        $result = $this->share_model->dealSharemanager($share);
	        if($result){
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '结算分享管理，id：'.$id;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "data"=>$result, "message"=>"操作成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"操作失败！");
			}
        }
        else{
        	$return = array("code"=>10002, "message"=>"参数错误！");
        }
	    
    	echo json_encode($return);
		exit();
	}
}