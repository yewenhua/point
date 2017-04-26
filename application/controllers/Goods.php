<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Goods extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('goods_model');
    }
    
    public function selectPageData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
		$this->load->model('member_model');
    	$path = $this->input->post('path');
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $num = $this->input->post('num');
	    $start = $this->input->post('start');
	    $end = $this->input->post('end');
	    $status = $this->input->post('status');
	    $is_company = $this->input->post('is_company');
        $result = $this->goods_model->selectPageData($status, $path, $start, $end, $searchkey, $offset, $num, $is_company);
        if($result !== null){
            $pwd_key = $this->config->item('pwd_key');
            foreach($result['data'] as $key=>$item){
                $company = null;
        		if($item['company_id'] && $item['company_id'] > 0){
        		    $company = $this->member_model->getDataById($item['company_id']);
        		}
        		$result['data'][$key]['com_name'] = $company !== null ? $company['name'] : '';
	        	$result['data'][$key]['com_mobile'] = $company !== null ? $company['mobile'] : '';
        		
                $encrypt_key = $this->encrypt($item['id'], $pwd_key);
                $result['data'][$key]['key'] = $encrypt_key;
                $url = 'mall/detail?key='.$encrypt_key;
                $result['data'][$key]['url'] = base_url($url);
                $result['data'][$key]['cash_price'] = round($item['cash_price'], 2);
                $result['data'][$key]['market_price'] = round($item['market_price'], 2);
                $result['data'][$key]['share_price'] = round($item['share_price'], 2);
                if($item['is_time_buy'] == 1 && $item['buy_time']){
                	$result['data'][$key]['buy_time'] = substr($item['buy_time'], 0, 16);
                }
                
                $optionData = $item['options'] ? json_decode($item['options'], true) : null;
                if($optionData && $optionData['is_option'] == 1){
                	//包括库存为0的
                    $skuData = $this->goods_model->selectSkuDataByGidWithinZero($item['id']);
                    if($skuData !== null){
                    	foreach($skuData as $skuItem){
                    		foreach($optionData['optionList'] as $listKey=>$listItem){
                    			if($listItem['attr_id'] == $skuItem['attributes']){
                    				$optionData['optionList'][$listKey]['num'] = $skuItem['total'];
                    			}
                    		}
                    	}
                    	$result['data'][$key]['options'] = json_encode($optionData);
                    }
                }
            }
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
	
    public function deleteImg()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $file = $this->input->post('file');
        $id = $this->input->post('id');
        if($id){
            $result = $this->goods_model->deleteGoodsImg($id, $file);
        }

        //新建可以删除原始图片  修改则不能  因为订单中可能要获取下单时的信息
        $flag = false;
        if(!$id){
    	    $file_path_and_name = dirname(dirname(dirname(__FILE__))).'/backend/uploads/'.$file;
		    if(file_exists($file_path_and_name)){
		        @unlink($file_path_and_name);
		        $flag = true;
		    }
        }
        elseif($id && $result){
        	$flag = true;
        }
	    
        if($flag){
		    $return = array("code"=>0, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function save()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
    	$id = $this->input->post('id');
		$data = array(
		    "description"=>$this->input->post('desc'),
		    "name"=>$this->input->post('name'),
			"options"=>$this->input->post('options'),
		    "market_price"=>$this->input->post('market_price'),
			"point_price"=>$this->input->post('point_price'),
		    "cash_price"=>$this->input->post('cash_price'),
			"total"=>$this->input->post('total'),
			"is_release"=>$this->input->post('is_release'),
			"img_data"=>$this->input->post('img_data'),
		    "category"=>$this->input->post('category'),
		    "model"=>$this->input->post('model'),
		    "send_method"=>$this->input->post('send_method'),
		    "weight"=>$this->input->post('weight'),
		    "limit_buy"=>$this->input->post('limit_buy'),
			"company_id"=>$this->input->post('company_id'),
		    "company_get_type"=>$this->input->post('company_get_type'),
			"company_useable_point"=>$this->input->post('company_useable_point'),
		    "send_type"=>$this->input->post('send_type'),
		    "buy_time"=>$this->input->post('buy_time'),
		    "is_time_buy"=>$this->input->post('is_time_buy'),
		    "is_share"=>$this->input->post('is_share'),
		    "share_price"=>$this->input->post('share_price')
		);
		if(!$id){
			$lastRow = $this->goods_model->selectMaxSortRow();
			if($lastRow !== null){
				$sort_id = $lastRow['sort_id'] + 1;
			}
			else{
				$sort_id = 0;
			}
			$data['sort_id'] = $sort_id;
				
		    $result = $this->goods_model->insertRow($data);
		    $operater_desc = '插入商品，ID：'.$result;
		}
		else{
			$result = $this->goods_model->updateRow($data, $id);
			$operater_desc = '修改商品，ID：'.$id;
		}
		
		if($result){
			$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "id"=>$result, "message"=>"操作成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"操作失败");
		}
    	
		echo json_encode($return);
		exit();
	}
	
    public function deleteGoods()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        
        $result = $this->goods_model->deleteRow($id);
        if($result){
		    $return = array("code"=>0, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function deleteBatch()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $idlist = $this->input->post('idlist');
        $idlist = json_decode($idlist);
        $result = $this->goods_model->deleteBatch($idlist);
        if($result){
        	$idlistString = implode(',', $idlist);
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '批量删除商品，idlist：'.$idlistString;
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"删除失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function switch_status()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        
        $result = $this->goods_model->switch_status($id, $value);
        if($result){
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
    public function recommend_status()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        
        $result = $this->goods_model->recommend_status($id, $value);
        if($result){
		    $return = array("code"=>0, "message"=>"推荐成功");
		}
		else{
			$return = array("code"=>10001, "message"=>"推荐失败");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
	public function changeSortEachother()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
        $id = $this->input->post('id');
        $sort_id = $this->input->post('sort_id');
        $offset = $this->input->post('offset');
        $type = $this->input->post('type');
        $theotherData = $this->goods_model->selectDataByOffset($offset);
        if($theotherData !== null){
            $result = $this->goods_model->changeSortEachother($id, $sort_id, $theotherData['id'], $theotherData['sort_id']);
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
        
        $maxRowData = $this->goods_model->selectMaxSortRow();
        if($maxRowData !== null){
        	$new_sort_id = $maxRowData['sort_id'] + 1;
            $result = $this->goods_model->changeSortToNew($id, $new_sort_id);
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
}