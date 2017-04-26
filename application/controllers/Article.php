<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Article extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('article_model');
    }

    public function changeSortEachother()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $sort_id = $this->input->post('sort_id');
        $offset = $this->input->post('offset');
        $type = $this->input->post('type');
        $theotherData = $this->article_model->selectDataByOffset($offset);
        if($theotherData !== null){
            $result = $this->article_model->changeSortEachother($id, $sort_id, $theotherData['id'], $theotherData['sort_id']);
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
	    
    	echo json_encode($return);
		exit();
	}
	
	public function changeSortTop()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        
        $maxRowData = $this->article_model->selectMaxSortRow();
        if($maxRowData !== null){
        	$new_sort_id = $maxRowData['sort_id'] + 1;
            $result = $this->article_model->changeSortToNew($id, $new_sort_id);
	        if($result){
			    $return = array("code"=>0, "message"=>"操作成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"操作失败！");
			}
        }
        else{
            $return = array("code"=>10002, "message"=>'数据有误');
        }
	    
    	echo json_encode($return);
		exit();
	}
	
	public function news()
	{
    	$data = array();
    	$data['userInfo'] = $this->user;
    	
    	$data['title'] = '新闻公告';
		$data['page_id'] = 'personal-page';
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		$data['menu'] = 'news';
		$data['env'] = $this->env;
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/mall/news.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('mall/news', $data);
		$this->load->view('footer', $data);
	}
	
	public function detail()
	{
		$key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$id = $this->decrypt($key, $pwd_key);
		$news = $this->article_model->selectDataById($id);
		if($news !== null){
			$data = array();
			$data['news'] = $news;
			$data['userInfo'] = $this->user;
    	    $data['title'] = '公告详情';
			$data['page_id'] = 'news-detail-page';
			$data['page_css'] = array();
			$data['page_js'] = array();
			$data['menu'] = 'news';
			$data['env'] = $this->env;
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    
			);
			$data['systemInfo'] = $this->systemInfo;
			$this->load->view('header', $data);
			$this->load->view('mall/news_detail', $data);
			$this->load->view('footer', $data);
		}
		else{
			echo '非法访问';
			exit;
		}
	}
	
	public function getNewsList()
	{
	    $page = $this->input->post('page');
		$num = $this->input->post('num');
		$page = $page ? $page : 1;
		$num = $num ? $num : 10;
    	$offset = ($page - 1) * $num;
        $result = $this->article_model->getNewsList($offset, $num);
        if($result !== null){
        	$pwd_key = $this->config->item('pwd_key');
        	foreach($result['data'] as  $key=>$value){
		    	$result['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "data"=>null, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
}