<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Privilege extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('privilege_model');
    }
	
    public function editPrivilege()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
			$id = $this->input->post('id');
			$page_name = $this->input->post('page_name');
			$page_url = $this->input->post('page_url');
			$page_desc = $this->input->post('page_desc');
			$temp_page_state_array = explode("/", $page_url);
			$page_state_array = array();
			//去除左右两端的/符号
			foreach($temp_page_state_array as $item){
				if($item != ''){
					$page_state_array[] = $item;
				}
			}

			$page_state = implode(".", $page_state_array);
			
			$data = array(
				"page_name" => $page_name,
				"page_url" => $page_url,
			    "page_state" => $page_state,
			    "page_desc" => $page_desc,
			);
			
			if($id != '' && $id != false){
				$extra_param = array(
				    "id"=>$id,
				);
				$data = array_merge($data, $extra_param);
				$result = $this->privilege_model->update($data);
			}
			else{
				$result = $this->privilege_model->insert($data);
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
	
    public function selectPageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $num = $this->input->post('num');
    	    $result = $this->privilege_model->selectPageData($searchkey, $offset, $num);
	        if($result !== null){
			    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function deletePrivilege()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $result = $this->privilege_model->delete($id);
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
	
	public function selectAllData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $result = $this->privilege_model->selectAllData();
	        if($result !== null){
			    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	public function editRole()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
			$id = $this->input->post('id');
			$name = $this->input->post('name');
			$desc = $this->input->post('desc');
			$privilege_list = $this->input->post('privilege_list');
			
			$data = array(
				"name" => $name,
				"desc" => $desc,
			    "privilege_list" => $privilege_list,
			);
			
			if($id != '' && $id != false){
				$extra_param = array(
				    "id"=>$id,
				);
				$data = array_merge($data, $extra_param);
				$result = $this->privilege_model->updateRole($data);
			}
			else{
				$result = $this->privilege_model->insertRole($data);
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
	
	public function deleteRole()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $result = $this->privilege_model->deleteRole($id);
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
	
	public function selectRolePageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $num = $this->input->post('num');
    	    $result = $this->privilege_model->selectRolePageData($searchkey, $offset, $num);
	        if($result !== null){
			    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	public function getAllRoleData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $result = $this->privilege_model->getAllRoleData();
	        if($result !== null){
			    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	public function getRoleByKey()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $roleList= $this->privilege_model->getRoleByKey();
	        if($roleList !== null){
	        	$roleUserList = array();
	        	foreach($roleList as $item){
	        	    $roleUserItem = $this->privilege_model->getRoleUserByRoleId($item['id']);
	        	    if($roleUserItem !== null){
	        	        $roleUserList[] = $roleUserItem;
	        	    }
	        	}
	        	
	        	$userList = array();
	        	if(!empty($roleUserList)){
		        	foreach($roleUserList as $item){
		        		foreach($item as $user){
		        			$userList[] = $user;
		        		}
		        	}
	        	}
	        	$userList = $this->maopaoSort($userList);
	        	foreach($userList as $key=>$item){
	        		if($item['is_lock'] == 1){
	        			$userList[$key]['name'] = $item['name'].'(锁定)';
	        		}
	        	}
			    $return = array("code"=>0, "data"=>$userList, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    //冒泡排序  锁定用户排到最后
    private function maopaoSort(array $numbers){
    	$cnt=count($numbers);
    	if($cnt == 0){
    		return array();
    	}
    	elseif($cnt == 1){
    		return $numbers;
    	}
    	else{
			for($i=0; $i<$cnt-1; $i++){//循环比较
				for($j=$i+1; $j<$cnt; $j++){
					if($numbers[$j]['is_lock'] < $numbers[$i]['is_lock']){//执行交换
						$temp = $numbers[$i];
						$numbers[$i] = $numbers[$j];
						$numbers[$j] = $temp;
					}
				}
			}
    	}
		return $numbers;
    }
    
}
