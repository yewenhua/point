<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Department extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('department_model');
    }
   /**
	 * 按页获取记录.
	 * 包含锁定部门
	 */
    public function selectPageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $num = $this->input->post('num');
    	    $result = $this->department_model->selectPageData($searchkey, $offset, $num);
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
	
    public function selectAllData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $result = $this->department_model->selectAllData();
	        if($result !== null){
	        	foreach($result as $key=>$item){
	        		if($item['is_lock'] == 1){
	        			$result[$key]['name'] = $item['name'].'(锁定)';
	        		}
			        $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
	        	}
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
	
    public function editDepartment()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
			$id = $this->input->post('id');
			$name = $this->input->post('name');
			$is_lock = $this->input->post('is_lock');
			$operater_desc = '';

			$data = array(
			    "name"=>$name,
			    "is_lock"=>$is_lock
			);
			
			if($id != '' && $id != false){
				$extra_param = array(
				    "id"=>$id,
				);
				$data = array_merge($data, $extra_param);
				$result = $this->department_model->updateRow($data);
				$operater_desc = '更新部门，ID：'.$id;
			}
			else{
				$lastRow = $this->department_model->selectMaxSortRow();
				if($lastRow !== null){
					$sort_id = $lastRow['sort_id'] + 1;
				}
				else{
					$sort_id = 0;
				}
				$data['sort_id'] = $sort_id;
				$result = $this->department_model->insertRow($data);
				$operater_desc = '插入部门，ID：'.$result;
			}
			
			if($result){
				$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
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
	
    public function deleteRow()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $result = $this->department_model->deleteRow($id);
	        if($result){
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '删除部门，ID：'.$id;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0,"data"=>$result, "message"=>"删除成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"删除失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function deleteBatch()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $idlist = $this->input->post('idlist');
    	    $idlist = json_decode($idlist);
    	    $result = $this->department_model->deleteBatch($idlist);
	        if($result){
	        	$idlistString = implode(',', $idlist);
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '批量删除部门，idlist：'.$idlistString;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0,"data"=>$result, "message"=>"删除成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"删除失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function lockBatch()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $idlist = $this->input->post('idlist');
    	    $idlist = json_decode($idlist);
    	    $result = $this->department_model->lockBatch($idlist);
	        if($result){
			    $return = array("code"=>0,"data"=>$result, "message"=>"锁定成功！");
			}
			else{
				$return = array("code"=>10001,"data"=>null, "message"=>"锁定失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999,"data"=>array(), "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function changeSortEachother()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $sort_id = $this->input->post('sort_id');
    	    $offset = $this->input->post('offset');
    	    $type = $this->input->post('type');
    	    $theotherData = $this->department_model->selectDataByOffset($offset);
    	    if($theotherData !== null){
    	        $result = $this->department_model->changeSortEachother($id, $sort_id, $theotherData['id'], $theotherData['sort_id']);
		        if($result){
				    $return = array("code"=>0, "message"=>"操作成功！");
				}
				else{
					$return = array("code"=>10001, "message"=>"操作失败！");
				}
    	    }
    	    else{
    	    	if($type == 'up'){
    	    		$message = '没有上一条数据';
    	    	}
    	    	else{
    	    		$message = '没有下一条数据';
    	    	}
    	        $return = array("code"=>10002, "message"=>$message);
    	    }
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
}