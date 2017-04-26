<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('admin_model');
        $this->load->model('system_model');
    }
    
    public function index(){
    	echo '<div style="font-size:50px; color:red; padding-top:50px; text-align:center;width:100%;">网站正在建设中……</div>';
    }
    
    public function getPageUrl(){  
	    $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://'; 
	    $url .= $_SERVER['HTTP_HOST'];  
	    $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
	    return $url;  
	} 
	
	public function getPageHost(){  
	    $url = $_SERVER['HTTP_HOST'];  
	    return $url;  
	} 
	
	/**
	 * 获取客户端IP地址
	 * @return string
	 */
	public function get_client_ip() { 
	    if(getenv('HTTP_CLIENT_IP')){ 
	        $client_ip = getenv('HTTP_CLIENT_IP'); 
	    } elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
	        $client_ip = getenv('HTTP_X_FORWARDED_FOR'); 
	    } elseif(getenv('REMOTE_ADDR')) {
	        $client_ip = getenv('REMOTE_ADDR'); 
	    } else {
	        $client_ip = $_SERVER['REMOTE_ADDR'];
	    } 
	    return $client_ip; 
	}   
	/**
	* 获取服务器端IP地址
	 * @return string
	 */
	public function get_server_ip() { 
	    if (isset($_SERVER)) { 
	        if($_SERVER['SERVER_ADDR']) {
	            $server_ip = $_SERVER['SERVER_ADDR']; 
	        } else { 
	            $server_ip = $_SERVER['LOCAL_ADDR']; 
	        } 
	    } else { 
	        $server_ip = getenv('SERVER_ADDR');
	    } 
	    return $server_ip; 
	}
}