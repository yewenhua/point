<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('system_model');
    }
	
    public function editSystem()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	$id = $this->input->post('id');
			$title = $this->input->post('title');
			$week_check = $this->input->post('week_check');
			$month_check = $this->input->post('month_check');
			$privilege_key = $this->input->post('privilege_key');
			$author = $this->input->post('author');
			$site_name = $this->input->post('site_name');
			$site_www = $this->input->post('site_www');
			$keywords = $this->input->post('keywords');
			$description = $this->input->post('description');
			
			$data = array(
				"title"=>$title,
				"week_check"=>$week_check,
			    "month_check"=>$month_check,
			    "author"=>$author,
			    "privilege_key"=>$privilege_key,
				"site_name"=>$site_name,
				"site_www"=>$site_www,
				"keywords"=>$keywords,
				"description"=>$description
			);
			
			if($id != '' && $id != false){
				$extra_param = array(
				    "id"=>$id,
				);
				$data = array_merge($data, $extra_param);
				$result = $this->system_model->update($data);
			}
			else{
				$result = $this->system_model->insert($data);
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
	
    public function getSystemData()
	{
	    $result = $this->system_model->getSystemData();
        if($result !== null){
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
		
    	echo json_encode($return);
		exit();
	}
	
    public function validate()
	{
	    $code = $this->input->post('code');
        $host = $this->getPageHost();
        $hash = md5('cnvp'.$host.'goodluck');
    	if($code == $hash){
    		$system = $this->system_model->getSystemData();
    		if($system !== null){
	    		$res = $this->system_model->update_privilege($system['id'], $code);
	    		if($res){
	    		    $return = array("code"=>0, "message"=>"验证成功！");
	    		}
	    		else{
	    			$return = array("code"=>10001, "message"=>"更新数据库出错！");
	    		}
    		}
    		else{
    			$return = array("code"=>10002, "message"=>"服务器出错！");
    		}
    	}
    	else{
    		$return = array("code"=>10003, "message"=>"验证出错！");
    	}
    	
    	echo json_encode($return);
		exit();
	}
	
	public function getPageHost(){  
	    $url = $_SERVER['HTTP_HOST'];  
	    return $url;  
	} 
    
}
