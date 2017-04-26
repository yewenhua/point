<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('setting_model');
    }
    
    public function editSchedule()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$id = $this->input->post('id');
		$multiple = trim($this->input->post('multiple'));
		$day = trim($this->input->post('day'));
		$deadtime = trim($this->input->post('deadtime'));
		$stoptime = trim($this->input->post('stoptime'));
		$is_open = trim($this->input->post('is_open'));
		$operater_desc = '';

		$data = array(
		    "multiple"=>$multiple,
		    "day"=>$day,
		    "deadtime"=>$deadtime,
		    "stoptime"=>$stoptime,
		    "is_open"=>$is_open
		);
		
		if($id != '' && $id != false){
			$extra_param = array(
			    "id"=>$id,
			);
			$data = array_merge($data, $extra_param);
			$result = $this->setting_model->updateSchedule($data);
			$operater_desc = '更新排期，ID：'.$id;
		}
		else{
			$result = $this->setting_model->insertSchedule($data);
			$operater_desc = '插入排期，ID：'.$result;
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
    	
		echo json_encode($return);
		exit();
	}
	
    public function getScheduleData()
	{
	    $result = $this->setting_model->getScheduleData();
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
		
    	echo json_encode($return);
		exit();
	}
	
    public function commisionSetting()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$id = $this->input->post('id');
		$junior_first = trim($this->input->post('junior_first'));
		$junior_second = trim($this->input->post('junior_second'));
		$junior_third = trim($this->input->post('junior_third'));
		$middle_first = trim($this->input->post('middle_first'));
		$middle_second = trim($this->input->post('middle_second'));
		$middle_third = trim($this->input->post('middle_third'));
		$advanced_first = trim($this->input->post('advanced_first'));
		$advanced_second = trim($this->input->post('advanced_second'));
		$advanced_third = trim($this->input->post('advanced_third'));
		$operater_desc = '';

		$data = array(
		    "junior_first"=>$junior_first,
		    "junior_second"=>$junior_second,
		    "junior_third"=>$junior_third,
		    "middle_first"=>$middle_first,
		    "middle_second"=>$middle_second,
			"middle_third"=>$middle_third,
			"advanced_first"=>$advanced_first,
			"advanced_second"=>$advanced_second,
			"advanced_third"=>$advanced_third
		);
		
		if($id != '' && $id != false){
			$extra_param = array(
			    "id"=>$id,
			);
			$data = array_merge($data, $extra_param);
			$result = $this->setting_model->updateCommisionSetting($data);
			$operater_desc = '更新佣金设置，ID：'.$id;
		}
		else{
			$result = $this->setting_model->insertCommisionSetting($data);
			$operater_desc = '插入佣金设置，ID：'.$result;
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
    	
		echo json_encode($return);
		exit();
	}
	
    public function getCommisionSetting()
	{
	    $result = $this->setting_model->getCommisionSetting();
        if($result !== null){
        	$result['junior_first'] = round($result['junior_first'], 1);
        	$result['junior_second'] = round($result['junior_second'], 1);
        	$result['junior_third'] = round($result['junior_third'], 1);
        	$result['middle_first'] = round($result['middle_first'], 1);
        	$result['middle_second'] = round($result['middle_second'], 1);
        	$result['middle_third'] = round($result['middle_third'], 1);
        	$result['advanced_first'] = round($result['advanced_first'], 1);
        	$result['advanced_second'] = round($result['advanced_second'], 1);
        	$result['advanced_third'] = round($result['advanced_third'], 1);
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"没有数据！");
		}
		
    	echo json_encode($return);
		exit();
	}
}