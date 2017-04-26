<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('log_model');
    }
    
   /**
	 * 管理员获取日志管理记录.
	 */
    public function logPageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$time = $this->input->post('time');
    	    $searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $num = $this->input->post('num');
		    $time = $time ? $time : '';
    	    $result = $this->log_model->logPageData($time, $searchkey, $offset, $num);
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
	
}