<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Statistics extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('statistics_model');
        $this->load->model('department_model');
    }
	
	public function pieGraphData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$dep = $this->input->post('dep');
			$year = $this->input->post('year');
    	    $data = $this->statistics_model->graphData($dep, $year);
	        if($data !== null){
	        	$pic_size = 0;
	        	$doc_size = 0;
	        	$video_size = 0;
	        	$pic_num = 0;
	        	$doc_num = 0;
	        	$video_num = 0;
	        	foreach($data as $item){
	        		if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
	        			$pic_size = $pic_size + $item['file_size'];
	        			$pic_num++;
	        		}
	        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
	        			$doc_size = $doc_size + $item['file_size'];
	        			$doc_num++;
	        		}
	        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
	        			$video_size = $video_size + $item['file_size'];
	        			$video_num++;
	        		}
	        	}
	        	
	        	$totalSize = $pic_size + $doc_size + $video_size;
	        	$totalNum = $pic_num + $doc_num + $video_num;
	        	$result = array(
	        	    'pic_capacity_rate' => round(($pic_size * 100)/$totalSize, 1),
		        	'doc_capacity_rate' => round(($doc_size * 100)/$totalSize, 1),
		        	'video_capacity_rate' => round(($video_size * 100)/$totalSize, 1),
	        	    'pic_num_rate' => round(($pic_num * 100)/$totalNum, 1),
		        	'doc_num_rate' => round(($doc_num * 100)/$totalNum, 1),
		        	'video_num_rate' => round(($video_num * 100)/$totalNum, 1),
	        	    'pic_size' => $pic_size,
		        	'doc_size' => $doc_size,
		        	'video_size' => $video_size,
	        	    'pic_num' => $pic_num,
		        	'doc_num' => $doc_num,
		        	'video_num' => $video_num
	        	);

			    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
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
	
	public function monthGraphData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$dep = $this->input->post('dep');
			$year = $this->input->post('year');
    	    $data = $this->statistics_model->graphData($dep, $year);
	        if($data !== null){
	        	$result = array(
	        	    'Jan' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Feb' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Mar' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Apr' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'May' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
	        	    'Jun' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Jul' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Aug' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Sep' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Oct' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Nov' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0),
		        	'Dec' => array('pic_num' => 0, 'pic_size' => 0, 'doc_num' => 0, 'doc_size' => 0, 'video_num' => 0, 'video_size' => 0)
	        	);
	        	
	        	foreach($data as $item){
	        		$month = date('m', strtotime($item['created_at']));
	        		switch($month){
	        			case 1:
			        		if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Jan']['pic_num'] = $result['Jan']['pic_num'] + 1;
			        			$result['Jan']['pic_size'] = $result['Jan']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Jan']['doc_num'] = $result['Jan']['doc_num'] + 1;
			        			$result['Jan']['doc_size'] = $result['Jan']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Jan']['video_num'] = $result['Jan']['video_num'] + 1;
			        			$result['Jan']['video_size'] = $result['Jan']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 2:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Feb']['pic_num'] = $result['Feb']['pic_num'] + 1;
			        			$result['Feb']['pic_size'] = $result['Feb']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Feb']['doc_num'] = $result['Feb']['doc_num'] + 1;
			        			$result['Feb']['doc_size'] = $result['Feb']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Feb']['video_num'] = $result['Feb']['video_num'] + 1;
			        			$result['Feb']['video_size'] = $result['Feb']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 3:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Mar']['pic_num'] = $result['Mar']['pic_num'] + 1;
			        			$result['Mar']['pic_size'] = $result['Mar']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Mar']['doc_num'] = $result['Mar']['doc_num'] + 1;
			        			$result['Mar']['doc_size'] = $result['Mar']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Mar']['video_num'] = $result['Mar']['video_num'] + 1;
			        			$result['Mar']['video_size'] = $result['Mar']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 4:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Apr']['pic_num'] = $result['Apr']['pic_num'] + 1;
			        			$result['Apr']['pic_size'] = $result['Apr']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Apr']['doc_num'] = $result['Apr']['doc_num'] + 1;
			        			$result['Apr']['doc_size'] = $result['Apr']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Apr']['video_num'] = $result['Apr']['video_num'] + 1;
			        			$result['Apr']['video_size'] = $result['Apr']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 5:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['May']['pic_num'] = $result['May']['pic_num'] + 1;
			        			$result['May']['pic_size'] = $result['May']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['May']['doc_num'] = $result['May']['doc_num'] + 1;
			        			$result['May']['doc_size'] = $result['May']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['May']['video_num'] = $result['May']['video_num'] + 1;
			        			$result['May']['video_size'] = $result['May']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 6:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Jun']['pic_num'] = $result['Jun']['pic_num'] + 1;
			        			$result['Jun']['pic_size'] = $result['Jun']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Jun']['doc_num'] = $result['Jun']['doc_num'] + 1;
			        			$result['Jun']['doc_size'] = $result['Jun']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Jun']['video_num'] = $result['Jun']['video_num'] + 1;
			        			$result['Jun']['video_size'] = $result['Jun']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 7:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Jul']['pic_num'] = $result['Jul']['pic_num'] + 1;
			        			$result['Jul']['pic_size'] = $result['Jul']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Jul']['doc_num'] = $result['Jul']['doc_num'] + 1;
			        			$result['Jul']['doc_size'] = $result['Jul']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Jul']['video_num'] = $result['Jul']['video_num'] + 1;
			        			$result['Jul']['video_size'] = $result['Jul']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 8:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Aug']['pic_num'] = $result['Aug']['pic_num'] + 1;
			        			$result['Aug']['pic_size'] = $result['Aug']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Aug']['doc_num'] = $result['Aug']['doc_num'] + 1;
			        			$result['Aug']['doc_size'] = $result['Aug']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Aug']['video_num'] = $result['Aug']['video_num'] + 1;
			        			$result['Aug']['video_size'] = $result['Aug']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 9:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Sep']['pic_num'] = $result['Sep']['pic_num'] + 1;
			        			$result['Sep']['pic_size'] = $result['Sep']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Sep']['doc_num'] = $result['Sep']['doc_num'] + 1;
			        			$result['Sep']['doc_size'] = $result['Sep']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Sep']['video_num'] = $result['Sep']['video_num'] + 1;
			        			$result['Sep']['video_size'] = $result['Sep']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 10:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Oct']['pic_num'] = $result['Oct']['pic_num'] + 1;
			        			$result['Oct']['pic_size'] = $result['Oct']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Oct']['doc_num'] = $result['Oct']['doc_num'] + 1;
			        			$result['Oct']['doc_size'] = $result['Oct']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Oct']['video_num'] = $result['Oct']['video_num'] + 1;
			        			$result['Oct']['video_size'] = $result['Oct']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 11:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Nov']['pic_num'] = $result['Nov']['pic_num'] + 1;
			        			$result['Nov']['pic_size'] = $result['Nov']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Nov']['doc_num'] = $result['Nov']['doc_num'] + 1;
			        			$result['Nov']['doc_size'] = $result['Nov']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Nov']['video_num'] = $result['Nov']['video_num'] + 1;
			        			$result['Nov']['video_size'] = $result['Nov']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                case 12:
	        		        if($item['file_type'] == 'jpg' || $item['file_type'] == 'jpeg' || $item['file_type'] == 'png'){
			        			$result['Dec']['pic_num'] = $result['Dec']['pic_num'] + 1;
			        			$result['Dec']['pic_size'] = $result['Dec']['pic_size'] + $item['file_size'];
			        		}
			        		elseif($item['file_type'] == 'doc' || $item['file_type'] == 'docx' || $item['file_type'] == 'pdf'){
			        			$result['Dec']['doc_num'] = $result['Dec']['doc_num'] + 1;
			        			$result['Dec']['doc_size'] = $result['Dec']['doc_size'] + $item['file_size'];
			        		}
			        	    elseif($item['file_type'] == 'mp4' || $item['file_type'] == 'ogg' || $item['file_type'] == 'webm'){
			        			$result['Dec']['video_num'] = $result['Dec']['video_num'] + 1;
			        			$result['Dec']['video_size'] = $result['Dec']['video_size'] + $item['file_size'];
			        		}
		                    break;
		                default:
		                    break;
	        		}
	        	}

			    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
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
	
	public function uploadNumRank()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$dep = $this->input->post('dep');
			$year = $this->input->post('year');
    	    $result = $this->statistics_model->uploadNumRank($dep, $year);
	        if($result !== null){
	        	$this->load->model('admin_model');
	        	foreach($result as $key=>$item){
	        		$result[$key]['name'] = '';
	        		$user = $this->admin_model->selectDataById($item['author_id']);
	        		if($user !== null){
	        			$result[$key]['name'] = $user['name'];
	        		}
	        	}
			    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
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
	
	public function downloadNumRank()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$dep = $this->input->post('dep');
			$year = $this->input->post('year');
    	    $result = $this->statistics_model->downloadNumRank($dep, $year);
	        if($result !== null){
			    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
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
	
	public function labelNumRank()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		$dep = $this->input->post('dep');
			$year = $this->input->post('year');
			
    	    $data = $this->statistics_model->labelNumRank($dep, $year);
	        if($data !== null){
	        	$labelArray = array();
	        	foreach($data as $item){
	        		$labeItemArray = json_decode($item['labellist'], true);
	        		foreach($labeItemArray as $label){
	        			if(!in_array($label['name'], $labelArray)){
	        				$labelArray[] = $label['name'];
	        			}
	        		}
	        	}
	        	
	        	$labelDataArray = array();
	        	foreach($labelArray as $item){
	        		$labelDataArray[$item] = array(
	        		    'name'=>$item,
	        		    'num'=>0
	        		);
	        	}
	        	
	        	foreach($data as $item){
	        		$labeItemArray = json_decode($item['labellist'], true);
	        		foreach($labeItemArray as $label){
	        			$labelDataArray[$label['name']]['num']++;
	        		}
	        	}
	        	
	        	$labelDataFinal = array();
	        	foreach($labelDataArray as $item){
	        		$labelDataFinal[] = $item;
	        	}
	        	
	        	$result = $this->maopaoSort($labelDataFinal);
	        	
			    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
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
					if($numbers[$j]['num'] > $numbers[$i]['num']){//执行交换
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