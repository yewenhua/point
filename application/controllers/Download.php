<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Download extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('download_model');
    }
    
    public function selectPageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $num = $this->input->post('num');
    	    $result = $this->download_model->selectPageData($searchkey, $offset, $num, $userInfo['id']);
	        if($result !== null){
	        	$this->load->model('department_model');
	        	foreach($result['data'] as $key=>$item){
	        		$dep_name = '';
	        	    if(strpos($item['dep_id'], ',') !== false){
        		    	//多个部门
        		    	$user_dep_list = explode(',', $item['dep_id']);
        		    	foreach($user_dep_list as $dep_item){
        		    		$dep = $this->department_model->selectDataById($item['author_dep']);
		        		    if($dep !== null){
			        			$dep_name .= $dep['name'].'，';
			        		}
        		    	}
        		    	if(strpos($dep_name, '，') !== false){
        		    		//去除最后一个逗号
        		    		$length = strlen($dep_name) - 1;
        		    		$dep_name = substr($dep_name, 0, $length);
        		    	}
        			}
        			else{
	        		    $dep = $this->department_model->selectDataById($item['author_dep']);
	        		    if($dep !== null){
	        		        $dep_name = $dep['name'];
	        		    }
        			}
	        		$result['data'][$key]['dep_name'] = $dep_name;
	        		
	        		$labelArray = json_decode($item['labellist'], true);
	        		$label_name_list = '';
	        		foreach($labelArray as $label){
	        			if($label_name_list == ''){
	        				$label_name_list = $label['name'];
	        			}
	        			else{
	        				$label_name_list .= '，'.$label['name'];
	        			}
	        		}
	        		$result['data'][$key]['label_name_list'] = $label_name_list;
	        		
	        	    //分类
	        	    $result['data'][$key]['category_name'] = '';
	        		if(($item['status'] == 1 || $item['status'] == 3) && $item['category'] != 0){
	        			$this->load->model('tree_model');
	        			$category = $this->tree_model->selectRowData($item['category']);
	        			if($category !== null){
	        				$result['data'][$key]['category_name'] = $category['label'];
	        			}
	        		}
	        	}
			    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"没有数据！");
			}
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	public function execute()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$id = $this->input->get('id');
    		if($id){
    			$this->load->model('upload_model');
	    		$fileInfo = $this->upload_model->selectDataById($id);
	    		if($fileInfo !== null){
		    		$fileArray = explode('.', $fileInfo['save_file_name']);
		    		$outputName = $fileInfo['setting_name'].'.'.$fileArray[1];
					$file_path_and_name = dirname(dirname(dirname(__FILE__))).'/backend/uploads/'.$fileInfo['save_file_name'];   
					if(file_exists($file_path_and_name)){//文件存在  
					    //下载次数加1  
					    $num = $fileInfo['download_num'] + 1;
					    $this->download_model->download($id, $num, $userInfo['id']);
					    
					    //打开文件  
					    $file = @fopen($file_path_and_name, "r");   
					    $file_size = filesize($file_path_and_name);   
					      
					    header("content_type:application/octet_stream");  
					    header("Accept-Ranges:bytes");  
					    header("Accept-length:".$file_size);  
					    header("content-Disposition:attachment;filename=".$outputName);  
					    $buffer = 1024;  
		
					    while(!feof($file)){  
					        $file_data = fread($file, $buffer);  
					        echo $file_data;  
					    }  
					      
					    fclose($file);   
					    exit;   
					}
					else{   
					    echo '文件不存在';   
					    die;
					}  
	    		}
	    		else{
	    			echo '数据不存在';   
				    die;
	    		}
    		}
    		else{
    			echo '参数错误';   
				die;
    		}
    	}
    	else{
    		$url = "Location: /";
	    	header($url);
	    	return;
    	}
	}
	
}