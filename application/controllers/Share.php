<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Share extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('goods_model');
        $this->load->model('member_model');
        $this->load->model('share_model');
        $this->load->model('mall_model');
    }
    
    public function preview(){
        if($this->isInWechat()){
	    	$wechat = $this->session->wechat;
	    	if(!$wechat){
	    		$url = "Location: /mall/center";
		    	header($url);
		    	return;
	    	}
	    }
	    
    	header("Content-Type: text/html; charset=utf-8");
    	$data = array();
    	$buy_num = $this->input->post('buy_num');
    	$buy_goods_key = $this->input->post('buy_goods_key');
    	$buy_goods_attr = $this->input->post('buy_goods_attr');
    	$buy_share_key = $this->input->post('buy_share_key');
    	$pwd_key = $this->config->item('pwd_key');
    	$buy_share_id = $this->decrypt($buy_share_key, $pwd_key);
		$buy_goods_id = $this->decrypt($buy_goods_key, $pwd_key);
    	$goods = $this->goods_model->selectDataById($buy_goods_id);
    	$share = $this->mall_model->selectShareById($buy_share_id);
    	if($goods !== null && $share !== null){
    		if($goods['is_time_buy'] == 1 && $goods['buy_time'] && (strtotime($goods['buy_time']) > time())){
    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">该商品暂未开放购买</div>';
    		    exit;
    			die;
    		}
    		
    		$province = '';
    		$userInfo = $this->session->user;
    	    if($userInfo && $userInfo['address']){
    			//取省份前两个字比较， 一个UTF-8的中文字符，会把它当做长度为3来处理
	    		$addressObj = json_decode($userInfo['address'], true);
	    		$province = substr($addressObj['provinceName'], 0, 6);
	    		$addressObj['detailInfo'] = str_replace(array("\r\n", "\r", "\n"), '', $addressObj['detailInfo']);
	    		$userInfo['address'] = json_encode($addressObj);
    		}
    		
    		$logistic_info = '';
    		if($goods['send_method'] > 0){
    			//有设置配送方案 
	    		$logisticData = $this->goods_model->selectLogisticDataById($goods['send_method']);
	    		if($logisticData !== null){
	    			$logistic_info = json_decode($logisticData['template'], true);
	    			$flag = false;
	    			$haveDefault = false;

	    			foreach($logistic_info as $item) {
	    				if($province){
	    					//有地址信息，去匹配
		    				if($item['isDefault']){
		    					//不匹配默认地址
		    					$defaultFirstWeight = $item['first_weight'];
		    					$defaultFirstFee = $item['first_fee'];
		    					$defaultOtherWeight = $item['other_weight'];
		    					$defaultOtherFee = $item['other_fee'];
				    					
		    					$haveDefault = true;
		    					continue;
		    				}
		    				else{
		    					foreach($item['addr_list'] as $addr) {
		    						//取省份前两个字比较， 一个UTF-8的中文字符，会把它当做长度为3来处理
		    						$addr_name = substr($addr['name'], 0, 6);
		    						if($addr_name == $province){
		    							$flag = true;
		    							$firstWeight = $item['first_weight'];
				    					$firstFee = $item['first_fee'];
				    					$otherWeight = $item['other_weight'];
				    					$otherFee = $item['other_fee'];
		    							break;
		    						}
		    					}
		    					
		    					if($flag){
		    						//找到对应省份邮费模板
		    						break;
		    					}
		    				}
	    				}
	    				else{
	    					//没有地址信息，使用默认地址
	    				    if($item['isDefault']){
		    					$firstWeight = $item['first_weight'];
		    					$firstFee = $item['first_fee'];
		    					$otherWeight = $item['other_weight'];
		    					$otherFee = $item['other_fee'];
		    					$flag = true;
		    					$haveDefault = true;
		    					break;
		    				}
		    				else{
		    					//不匹配非默认地址
		    					continue;
		    				}
	    				}
	    			}
	    			
	    			if(!$flag && $haveDefault){
	    				$firstWeight = $defaultFirstWeight;
    					$firstFee = $defaultFirstFee;
    					$otherWeight = $defaultOtherWeight;
    					$otherFee = $defaultOtherFee;
	    			}
		    		
	    			if($flag || $haveDefault){
	    				//计算初始运费
		    		    $totalWeight = $goods['weight'] * $buy_num;
		    		    $totalFee = 0;
		    			if($firstWeight >= $totalWeight){
	    					//小于首重
	    					$totalFee = $firstFee;
	    				}
	    				else{
	    					//大于首重
	    					$leftWeight = $totalWeight - $firstWeight;
	    					$leftFee = ceil($leftWeight/$otherWeight) * $otherFee;
	    					$totalFee = $firstFee + $leftFee;
	    				}

				    	$order_id = $this->createRandNum();
				    	$imgData = json_decode($goods['img_data'], true);
				        $flag = false;
				        foreach($imgData as $imgkey=>$imgitem){
				        	if($imgitem['selected']){
				        		$goods['face'] = $imgitem['file'];
				        		$flag = true;
				        	}
				        }
				        if(!$flag){
				        	$goods['face'] = $imgData[0]['file'];
				        }
				        
				        if(strpos($goods['send_type'], ',') !== false){
				        	$goods['send_type_array'] = explode(',', $goods['send_type']);
				        }
				        else{
				        	$goods['send_type_array'] = array($goods['send_type']);
				        }
				        
				        //规格
				        $size_op = '';
				    	if($buy_goods_attr){
			    			if(strpos($buy_goods_attr, ';') !== false){
			    				$attr_arr = explode(';', $buy_goods_attr);
			    			}
			    			else{
			    				$attr_arr = array($buy_goods_attr);
			    			}
			    			
			    			$options = json_decode($goods['options'], true);
			    			$option_arr = $options['options'];
			    			foreach($option_arr as $op){
			    				foreach($op['child'] as $child_item){
				    				foreach($attr_arr as $attr){
				    					if($child_item['attr_id'] == $attr){
				    						$size_op .= "&nbsp;&nbsp;".$op['title'].'：'.$child_item['title'];
				    					}
				    				}
			    				}
			    			}
				    	}
				    	$data['size_op'] = $size_op;
				        $data['userInfo'] = $userInfo;
				        $data['logistic_fee'] = $totalFee;
				        $data['logistic'] = $logistic_info;
				        $data['buy_goods_key'] = $buy_goods_key;
				        $data['buy_goods_attr'] = $buy_goods_attr;
				    	$data['buy_num'] = $buy_num;
				    	$data['order_id'] = $order_id;
				    	$data['created_at'] = date('Y-m-d H:i');
				    	$data['goods'] = $goods;
				    	$data['share'] = $share;
				    	$data['buy_share_key'] = $buy_share_key;
				    	$data['total_point'] = $buy_num  * $goods['point_price'];
				    	$data['share_price'] = $buy_num  * $goods['share_price'];
				    	
				    	$data['title'] = '订单预览';
						$data['page_id'] = 'personal-page';
						$data['menu'] = 'order';
						$data['env'] = $this->env;
						
						$data['page_css'] = array();
						$data['page_js'] = array();
						
						//页面底部最后加载的js
						$data['page_detail_js'] = array(
						    '/media/js/share/preview.js?v='.rand(1,10).'.'.rand(1,10)
						);
						
						$url = base_url("index.php/share/preview");
						$data['url'] = $url;
				        $jsapi = $this->jsapi($url);
				        $data = array_merge($data, $jsapi);
				        $data['systemInfo'] = $this->systemInfo;
					    $this->load->view('header', $data);
						$this->load->view('share/preview', $data);
						$this->load->view('footer', $data);
	    			}
	    			else{
	    				echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">未匹配到配送方案</div>';
    		            die;
	    			}
				}
	    		else{
	    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">获取配送方案出错</div>';
    		        exit;
	    		}
    		}
    		else{
    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">配送方案未设置</div>';
    		    exit;
    		}
    	}
    	else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
    }
    
    public function create(){
    	$data = array();
    	$address = trim($this->input->post('address'));
    	$comment = $this->input->post('comment');
    	$buy_num = $this->input->post('buy_num');
    	$order_id = $this->input->post('order_id');
    	$pay_type = $this->input->post('pay_type');
    	$send_type = $this->input->post('send_type');
    	$buy_goods_key = $this->input->post('buy_goods_key');
    	$buy_goods_attr = $this->input->post('buy_goods_attr');
    	$buy_share_key = $this->input->post('buy_share_key');
    	$pwd_key = $this->config->item('pwd_key');
    	$buy_share_id = $this->decrypt($buy_share_key, $pwd_key);
		$buy_goods_id = $this->decrypt($buy_goods_key, $pwd_key);
    	$goods = $this->goods_model->selectDataById($buy_goods_id);
    	$share = $this->mall_model->selectShareById($buy_share_id);

    	if(($share !== null && $goods !== null && $goods['is_release'] == 1 && $goods['total'] > 0 && !$buy_goods_attr && $share['rest_num'] > 0 && $share['rest_num'] >= $buy_num) || ($share !== null && $goods !== null && $goods['is_release'] == 1 && $buy_goods_attr && $share['rest_num'] > 0 && $share['rest_num'] >= $buy_num)){
	    	$sku_id = '';
    		if($buy_goods_attr){
    			//有规格商品
    			$sku = $this->goods_model->selectSkuRow($buy_goods_id, $buy_goods_attr);
    			$sku_id = $sku['id'];
    			if($sku === null){
    				echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">抱歉，手慢无</div>';
    		        die;
    			}
    			else{
    				if($sku['total'] < $buy_num){
    					echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">抱歉，库存不足</div>';
    		            die;
    				}
    			}
    		}
    		
    		$addressObj = json_decode($address, true);
    		$userInfo = $this->session->user;
    		if(!$userInfo){
    		    $userInfo = $this->member_model->getDataByMobile($addressObj['telNumber']);
    		}
    		else{
    			$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	        $userInfo['commision'] = $newlist_userInfo['commision'];
    		}
    		
    		$province = substr($addressObj['provinceName'], 0, 6);
    		$logisticFee = 0;
    		if($send_type == 'logistic'){
    		    $logisticFee = $this->countLogisticFee($goods, $buy_num, $province);
    		}
    		
	    	$total_cash_price = $buy_num * $goods['share_price'] + $logisticFee;
	    	
	    	$imgData = json_decode($goods['img_data'], true);
        	$flag = false;
        	foreach($imgData as $imgkey=>$imgitem){
        		if($imgitem['selected']){
        			$goods['face'] = $imgitem['file'];
        			$flag = true;
        		}
        	}
        	if(!$flag){
        		$goods['face'] = $imgData[0]['file'];
        	}
        	
        	$res = false;
        	if($total_cash_price > 0){
        		if($pay_type == 'wallet'){
        			if($userInfo){
	        			//钱包支付
	        			if($userInfo['commision'] < $total_cash_price){
			    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">钱包余额不足</div>';
		    		        die;
			    		}
			    		else{
			    			$pay_no = $this->createRandNum('walletpay');
	        			    $res = $this->share_model->createShareByHavePayWallet($sku_id, $share, $buy_num, $goods, $total_cash_price, $logisticFee, $comment, $userInfo['id'], $order_id, $address, $pay_no);
			    		}
        			}
        			else{
        				echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">用户不存在</div>';
		    		    die;
        			}
        		}
        		else{
        			//微信支付
        		    $res = $this->share_model->createShareByHavePay($sku_id, $share, $buy_num, $goods, $total_cash_price, $logisticFee, $comment, $order_id, $address);
        		}
        	}
        	
	    	if($res){
	    	    if($pay_type == 'wallet' || $total_cash_price <= 0){
	    	    	$this->session->user = $userInfo;
    		        $url = "Location: /orders/success";
    			}
    			else{
    				$order_key = $this->encrypt($order_id, $pwd_key);
	    			$url = "Location: /share/pay?key=".$order_key;
    			}
	    	}
	    	else{
	    		$url = "Location: /orders/fail";
	    	}
	    	header($url);
		    return;
    	}
    	else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误或库存不足</div>';
    		exit;
    	}
    }
    
    private function createRandNum($str = '')
    {
        return date('YmdHis') . rand(100000, 999999) . $str;
    }
    
	public function jsapi($param_url){
		$this->load->model('weixin_model');
		$jsapi_ticket = $this->weixin_model->getJsapiTicket();
    	$appid = $this->config->item('appId');
    	$params = array();
    	$params["url"] = $param_url;
        $params["timestamp"] = time();
        $noncestr = rand(1000000, 9999999);
        $params["noncestr"] = "$noncestr";
        $params["jsapi_ticket"] = $jsapi_ticket;
        ksort($params);
        $paramString = $this->ToUrlParams($params);
        $addrSign = sha1($paramString);

        $data = array(
            "signature" => $addrSign,
            "appId" => $appid,
            "timeStamp" => $params["timestamp"],
            "nonceStr" => $params["noncestr"],
        );
        return $data;
	}
	
	private function countLogisticFee($goods, $buy_num, $province){
	    $logisticData = $this->goods_model->selectLogisticDataById($goods['send_method']);
    	if($logisticData !== null){
    		$logistic_info = json_decode($logisticData['template'], true);
    		$flag = false;
    		$haveDefault = false;

    		foreach($logistic_info as $item) {
    			if($province){
    				//有地址信息，去匹配
    				if($item['isDefault']){
    					//不匹配默认地址
    					$defaultFirstWeight = $item['first_weight'];
    					$defaultFirstFee = $item['first_fee'];
    					$defaultOtherWeight = $item['other_weight'];
    					$defaultOtherFee = $item['other_fee'];
		    					
    					$haveDefault = true;
    					continue;
    				}
    				else{
    					foreach($item['addr_list'] as $addr) {
    						//取省份前两个字比较， 一个UTF-8的中文字符，会把它当做长度为3来处理
    						$addr_name = substr($addr['name'], 0, 6);
    						if($addr_name == $province){
    							$flag = true;
    							$firstWeight = $item['first_weight'];
		    					$firstFee = $item['first_fee'];
		    					$otherWeight = $item['other_weight'];
		    					$otherFee = $item['other_fee'];
    							break;
    						}
    					}
    					
    					if($flag){
    						//找到对应省份邮费模板
    						break;
    					}
    				}
    			}
    			else{
    				//没有地址信息，使用默认地址
    			    if($item['isDefault']){
    					$firstWeight = $item['first_weight'];
    					$firstFee = $item['first_fee'];
    					$otherWeight = $item['other_weight'];
    					$otherFee = $item['other_fee'];
    					$flag = true;
    					$haveDefault = true;
    					break;
    				}
    				else{
    					//不匹配非默认地址
    					continue;
    				}
    			}
    		}
    		
    		if(!$flag && $haveDefault){
    			$firstWeight = $defaultFirstWeight;
    			$firstFee = $defaultFirstFee;
    			$otherWeight = $defaultOtherWeight;
    			$otherFee = $defaultOtherFee;
    		}
    		
    		$totalFee = 0;
    		$totalWeight = $goods['weight'] * $buy_num;
    		if($flag || $haveDefault){
    			//计算初始运费
    			if($firstWeight >= $totalWeight){
    				//小于首重
    				$totalFee = $firstFee;
    			}
    			else{
    				//大于首重
    				$leftWeight = $totalWeight - $firstWeight;
    				$leftFee = ceil($leftWeight/$otherWeight) * $otherFee;
    				$totalFee = $firstFee + $leftFee;
    			}
    		}
    		
    		return $totalFee;
    	}
    	else{
    		return 10;
    	}
	}
	
    public function pay(){
    	$data = array();
    	$this->load->model('orders_model');
    	$userInfo = $this->session->user;
    	if($userInfo){
    	    //有session
    	    $newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
	    	if(isset($userInfo['openid']) && isset($newlist_userInfo['openid']) && $userInfo['openid'] != $newlist_userInfo['openid']){
	    		//已在别处登录过
	    		$url = "Location: /member/logout";
		    	header($url);
		    	return;
	    	}
	    	elseif(isset($newlist_userInfo['openid'])){
	    		$openid = $newlist_userInfo['openid'];
	    	}
	    	else{
	    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		    exit;
	    	}
    	}
    	else{
    		//没session
    	    $wechat = $this->session->wechat;
    		if($wechat){
    			$openid = $wechat['openid'];
    		}
    		else{
    			$url = "Location: /mall/center";
		    	header($url);
		    	return;
    		}
    	}
    	
    	$order_key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$order_id = $this->decrypt($order_key, $pwd_key);
		$order = $this->orders_model->selectDataByOrderid($order_id);
		$addressObj = json_decode($order['order_address'], true);
		if(!$userInfo){
			$userInfo = $this->member_model->getDataByMobile($addressObj['telNumber']);
		}
		$data['is_new'] = $userInfo ? 0 : 1;
		
		if($userInfo){
		    $original_parentid = $this->config->item('original_parentid');
			if($userInfo['parent_id'] == $original_parentid){
				$userInfo['parent_name'] = '系统';
				$userInfo['is_system'] = 1;
			}
			else{
				$parent = $this->member_model->getDataById($userInfo['parent_id']);
				$userInfo['parent_name'] = $parent['name'];
				$userInfo['is_system'] = 0;
			}
			
			$type = 'member';
			$token = $this->setToken($userInfo['id'], $type);
			$userInfo['token'] = $token;
			$this->user = $userInfo;
		}
		$this->session->user = $userInfo;
		
    	if($order !== null){
    		$data['title'] = '订单支付';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'order';
			$data['order'] = $order;
			$data['env'] = $this->env;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    
			);
			
			$this->load->model('wxpay_model');
	    	$type = 'JSAPI';
	    	$fee = $order['total_cash_price'] * 100; //订单总金额，单位为分
	    	$prepayInfo = $this->wxpay_model->prepay($order['order_id'], $openid, $type, $fee);
	    	
	    	log_message('info', '统一订单接口返回结果：'.var_export($prepayInfo, true));
	    	if($prepayInfo !== null){
	    		$prepay_id = $prepayInfo["prepay_id"];
	    		$jsapiParameters = $this->wxpay_model->getJsapiParameters($prepay_id);
	    		$data['jsapiParameters'] = $jsapiParameters;
	    		log_message('info', '前端支付参数：'.var_export($jsapiParameters, true));
	    	}
	    	else{
	    		// 'prepay fail';
	    		$url = "Location: /orders/fail";
		    	header($url);
		    	return;
	    	}
	    	
	    	$url = base_url("share/pay?showwxpaytitle=1");
	        $jsapi = $this->jsapi($url);
	        $data = array_merge($data, $jsapi);
			$data['systemInfo'] = $this->systemInfo;
    		$this->load->view('header', $data);
			$this->load->view('share/pay', $data);
			$this->load->view('footer', $data);
    	}
        else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
    }
    
    /**
     * 判断是否在微信中浏览
     */
    public function isInWechat()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}