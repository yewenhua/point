<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tree extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('tree_model');
    }
	
    public function edit_tree()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
			$node_id = $this->input->post('node_id');
			$path = $this->input->post('path');
			$label = $this->input->post('label');
			$is_root = $this->input->post('is_root');
			$orderby = $this->input->post('orderby');
			$img_url = $this->input->post('img_url');
			$is_open = $this->input->post('is_open');
			$data = array(
				"path"=>$path,
				"label"=>$label,
			    "is_root"=>$is_root,
				"orderby"=>$orderby,
			    "img_url"=>$img_url,
			    "is_open"=>$is_open
			);
			
			if($node_id != '' && $node_id != false){
				$extra_param = array(
				    "node_id"=>$node_id,
				);
				$data = array_merge($data, $extra_param);
				$result = $this->tree_model->update($data);
			}
			else{
				$result = $this->tree_model->insert($data);
				if($result){
					$pwd_key = $this->config->item('pwd_key');
	        		$url = 'mall/goodslist?key='.$this->encrypt($data['path'], $pwd_key);
		    	    $result['url'] = base_url($url);
				}
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
	
	public function delete_tree()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('node_id');
    	    $result = $this->tree_model->delete($id);
	        if($result){
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
	
    public function select_array_tree()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $result = $this->tree_model->selectArrayData();
	        if($result !== null){
	        	foreach($result as $key=>$item){
	        		$pwd_key = $this->config->item('pwd_key');
	        		$url = 'mall/goodslist?key='.$this->encrypt($item['path'], $pwd_key);
		    	    $result[$key]['url'] = base_url($url);
	        	}
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
	
    public function select_tree_category()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $secondLevelTree = $this->tree_model->select_tree_second_level();
	        if($secondLevelTree !== null){
	        	foreach($secondLevelTree as $key=>$item){
	        		$path = $item['path'].'/';
	        		$childrenTree = $this->tree_model->select_tree_by_path($path);
	        		if($childrenTree !== null){
	        			$secondLevelTree[$key]['children'] = $childrenTree;
	        		}
	        		else{
	        			$secondLevelTree[$key]['children'] = array();
	        		}
	        	}
			    $return = array("code"=>0, "data"=>$secondLevelTree, "message"=>"获取成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"获取失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
}
