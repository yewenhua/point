<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('goods_model');
        $this->load->model('member_model');
    }
    
    public function preview(){
    	header("Content-Type: text/html; charset=utf-8");
    	$this->auth_redirect_member();
    	$data = array();
    	$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['commision'] = $newlist_userInfo['commision'];
    	$userInfo['exchange_point'] = $newlist_userInfo['exchange_point'];
    	$userInfo['address'] = $newlist_userInfo['address'];
    	
    	$buy_num = $this->input->post('buy_num');
    	$buy_goods_key = $this->input->post('buy_goods_key');
    	$buy_goods_attr = $this->input->post('buy_goods_attr');
    	$pwd_key = $this->config->item('pwd_key');
		$buy_goods_id = $this->decrypt($buy_goods_key, $pwd_key);
    	$goods = $this->goods_model->selectDataById($buy_goods_id);
    	if($goods !== null){
    		if($goods['is_time_buy'] == 1 && $goods['buy_time'] && (strtotime($goods['buy_time']) > time())){
    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">该商品暂未开放购买</div>';
    		    exit;
    			die;
    		}
    		
    		$province = '';
    		if($userInfo['address']){
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
				    	$data['total_point'] = $buy_num  * $goods['point_price'];
				    	$data['total_cash'] = $buy_num  * $goods['cash_price'];
				    	
				    	$data['title'] = '订单预览';
						$data['page_id'] = 'personal-page';
						$data['menu'] = 'order';
						$data['env'] = $this->env;
						
						$data['page_css'] = array();
						$data['page_js'] = array();
						
						//页面底部最后加载的js
						$data['page_detail_js'] = array(
						    '/media/js/orders/preview.js?v='.rand(1,10).'.'.rand(1,10)
						);
						
						$url = base_url("index.php/orders/preview");
						$data['url'] = $url;
				        $jsapi = $this->jsapi($url);
				        $data = array_merge($data, $jsapi);
				        $data['systemInfo'] = $this->systemInfo;
					    $this->load->view('header', $data);
						$this->load->view('orders/preview', $data);
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
    
    public function pay(){
    	$this->auth_redirect_member();
    	$data = array();
    	$userInfo = $this->user;
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	if(isset($userInfo['openid']) && isset($newlist_userInfo['openid']) && $userInfo['openid'] != $newlist_userInfo['openid']){
    		$url = "Location: /member/logout";
	    	header($url);
	    	return;
    	}
    	
    	$userInfo['openid'] = $newlist_userInfo['openid'];
    	$data['userInfo'] = $userInfo;
    	
    	$order_key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$order_id = $this->decrypt($order_key, $pwd_key);
		$order = $this->orders_model->selectDataByOrderid($order_id);
		
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
			$openid = $userInfo['openid'];
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
	    	
	    	$url = base_url("orders/pay?showwxpaytitle=1");
	        $jsapi = $this->jsapi($url);
	        $data = array_merge($data, $jsapi);
			$data['systemInfo'] = $this->systemInfo;
    		$this->load->view('header', $data);
			$this->load->view('orders/pay', $data);
			$this->load->view('footer', $data);
    	}
        else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
    }
    
    public function notify(){
    	$this->load->model('wxpay_model');
        $return = $this->wxpay_model->notify();
        $callbackData = $return['callback'];
		if(isset($callbackData['return_code']) && $callbackData['return_code'] == 'SUCCESS' && isset($callbackData['result_code']) && $callbackData['result_code'] == 'SUCCESS')
		{
			$order_id = $callbackData['out_trade_no'];
			$status = 1;
			$pay_time = date('Y-m-d H:i:s', strtotime($callbackData['time_end']));
			$res = $this->orders_model->changeOrderPayStatus($order_id, $status, $callbackData['transaction_id'], $pay_time);
			if($res){
				log_message('info', '回调=>支付成功'.var_export($callbackData, true));
				echo $return['xml'];
			}
			else{
				echo '';
			}
		}
		else{
			log_message('info', '回调=>支付失败');
			echo '';
		}
		exit;
    }
    
    public function lists(){
    	$this->auth_redirect_member();
    	$data = array();
    	$data['title'] = '订单列表';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'order';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/orders/lists.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('orders/lists', $data);
		$this->load->view('footer', $data);
    }
    
    public function detail(){
    	$this->auth_redirect_member();
    	$data = array();
        $key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$id = $this->decrypt($key, $pwd_key);
    	$order = $this->orders_model->selectDataById($id);
    	if($order !== null){
    		$order['total_cash_price'] = round($order['total_cash_price'], 2);
    		$order['cash_price'] = round($order['cash_price'], 2);
	    	$address = json_decode($order['order_address'], true);
	    	$info = array();
    		if($order['status'] == 2 || $order['status'] == 3){
    			$ShipperCode = '';
    			$LogisticCode = $address['logisticNo'];
    			if(is_numeric($LogisticCode)){
    				$this->load->model('logistic_model');
	    			$LogisticRes = $this->logistic_model->code($LogisticCode);
	    			if($LogisticRes->Success == true){
						if(isset($LogisticRes->Shippers))
						{
							foreach($LogisticRes->Shippers as $key => $val){
							    $ShipperCode = $val->ShipperCode;
							}
						}
						
						if($ShipperCode != ''){
							$LogisticDetail = $this->logistic_model->query($ShipperCode, $LogisticCode);
							if($LogisticDetail->Success == true){
								if(isset($LogisticDetail->Traces))
								{
									foreach($LogisticDetail->Traces as $key => $val)
									{
										$info[$key]['time']    = $val->AcceptTime;
										$info[$key]['station'] = $val->AcceptStation;
									}
								}
							}
						}
		    		}
    			}
    		}
    		if(!empty($info)){
    		    $info = array_reverse($info);
    		}
    		
    		$size_op = '';
	    	if($order['sku_id']){
	    		$sku = $this->goods_model->selectSkuRowById($order['sku_id']);
	    		if($sku !== null){
	    			if(strpos($sku['attributes'], ';') !== false){
	    				$attr_arr = explode(';', $sku['attributes']);
	    			}
	    			else{
	    				$attr_arr = array($sku['attributes']);
	    			}
	    			
	    			$goods = $this->goods_model->selectDataById($order['goods_id']);
	    			if($goods !== null){
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
	    		}
	    	}
	    	$order['size_op'] = $size_op;
    		
    		$data['info'] = $info;
    		if($order['share_id'] != 0){
    			$url = 'mall/share?key='.$this->encrypt($order['share_id'], $pwd_key);
    		}
    		else{
    			$url = 'mall/detail?key='.$this->encrypt($order['goods_id'], $pwd_key);
    		}
    		
    		$order['url'] = base_url($url);
	    	$data['order'] = $order;
	    	$data['address'] = $address;
	    	$data['key'] = $this->encrypt($order['order_id'], $pwd_key);
	    	$data['title'] = '订单详情';
			$data['page_id'] = 'personal-page';
			$data['menu'] = 'order';
			$data['env'] = $this->env;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/orders/detail.js?v='.rand(1,10).'.'.rand(1,10)
			);
			$data['systemInfo'] = $this->systemInfo;
		    $this->load->view('header', $data);
			$this->load->view('orders/detail', $data);
			$this->load->view('footer', $data);
    	}
    	else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
    }
    
    private function createRandNum($str = '')
    {
        return date('YmdHis') . rand(100000, 999999) . $str;
    }
    
    public function create(){
    	$data = array();
    	$this->auth_redirect_member();
    	$userInfo = $this->user;
    	$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	$userInfo['exchange_point'] = $newlist_userInfo['exchange_point'];
    	$userInfo['commision'] = $newlist_userInfo['commision'];
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
    	
    	$check_key = $this->input->post('check_key');
    	$hash = $this->check($check_key);
	    if(!$userInfo['exchange_pwd']){
	    	$message = 'no_pwd_wrong';
    		$url = "Location: /orders/fail/".$message;
	    	header($url);
	    	return;
	    }
    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
    		$address = trim($this->input->post('address'));
    		$comment = $this->input->post('comment');
	    	$buy_num = $this->input->post('buy_num');
	    	$order_id = $this->input->post('order_id');
	    	$pay_type = $this->input->post('pay_type');
	    	$send_type = $this->input->post('send_type');
	    	$buy_goods_key = $this->input->post('buy_goods_key');
	    	$buy_goods_attr = $this->input->post('buy_goods_attr');
	    	$pwd_key = $this->config->item('pwd_key');
			$buy_goods_id = $this->decrypt($buy_goods_key, $pwd_key);
	    	$goods = $this->goods_model->selectDataById($buy_goods_id);

	    	if(($goods !== null && $goods['is_release'] == 1 && $goods['total'] > 0 && !$buy_goods_attr) || ($goods !== null && $goods['is_release'] == 1 && $buy_goods_attr)){
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
	    		$province = substr($addressObj['provinceName'], 0, 6);
	    		$logisticFee = 0;
	    		if($send_type == 'logistic'){
	    		    $logisticFee = $this->countLogisticFee($goods, $buy_num, $province);
	    		}
	    		
		    	$total_point_price = $buy_num * $goods['point_price'];
		    	$total_cash_price = $buy_num * $goods['cash_price'] + $logisticFee;
		    	if($goods['model'] == 1){
		    		if($userInfo['exchange_point'] < $total_point_price){
		    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">购物券不足</div>';
	    		        die;
		    		}
		    	}
	    	    elseif($goods['model'] == 2 || $goods['model'] == 3){
		    		if($userInfo['consume_point'] < $total_point_price){
		    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">消费积分不足</div>';
	    		        die;
		    		}
		    	}
	    	
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
        		
        		if($total_cash_price > 0){
        			if($pay_type == 'wallet'){
        				//钱包支付
	        			if($userInfo['commision'] < $total_cash_price){
			    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">钱包余额不足</div>';
		    		        die;
			    		}
			    		else{
			    			$pay_no = $this->createRandNum('walletpay');
        				    $res = $this->orders_model->createByHavePayWallet($sku_id, $buy_num, $goods, $total_point_price, $total_cash_price, $logisticFee, $comment, $userInfo['id'], $order_id, $address, $pay_no);
			    		}
        			}
        			else{
        				//微信支付
        			    $res = $this->orders_model->createByHavePay($sku_id, $buy_num, $goods, $total_point_price, $total_cash_price, $logisticFee, $comment, $userInfo['id'], $order_id, $address);
        			}
        		}
        		else{
		    	    $res = $this->orders_model->createByNoPay($sku_id, $buy_num, $goods, $total_point_price, $total_cash_price, $comment, $userInfo['id'], $order_id, $address);
        		}
        		
		    	if($res){
		    	    if($pay_type == 'wallet' || $total_cash_price <= 0){
	    		        $url = "Location: /orders/success";
	    			}
	    			else{
	    				$order_key = $this->encrypt($order_id, $pwd_key);
		    			$url = "Location: /orders/pay?key=".$order_key;
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
    	else{
    		$message = 'pwd_wrong';
    		$url = "Location: /orders/fail/".$message;
	    	header($url);
	    	return;
    	}
    }
    
    public function success(){
    	$data = array();
		$this->load->view('orders/success', $data);
    }
    
    public function fail($message = null){
    	$data = array();
    	$data['message'] = $message;
		$this->load->view('orders/fail', $data);
    }
    
	protected function check($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
    public function order_data_by_page()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
	    $searchkey = $this->input->post('searchkey');
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    
        $result = $this->orders_model->order_data_by_page($userInfo['id'], $searchkey, $page, $num);
        if($result !== null){
        	foreach($result['data'] as  $key=>$value){
        		$pwd_key = $this->config->item('pwd_key');
		    	$result['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
		    	$result['data'][$key]['total_cash_price'] = round($value['total_cash_price'], 2);
		    	$result['data'][$key]['cash_price'] = round($value['cash_price'], 2);
		    	
		    	$size_op = '';
		    	if($value['sku_id']){
		    		$sku = $this->goods_model->selectSkuRowById($value['sku_id']);
		    		if($sku !== null){
		    			if(strpos($sku['attributes'], ';') !== false){
		    				$attr_arr = explode(';', $sku['attributes']);
		    			}
		    			else{
		    				$attr_arr = array($sku['attributes']);
		    			}
		    			
		    			$goods = $this->goods_model->selectDataById($value['goods_id']);
		    			if($goods !== null){
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
		    		}
		    	}
		    	$result['data'][$key]['size_op'] = $size_op;
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
	/*
	 * admin
	 */
    public function selectPageData()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    		
    		$status = $this->input->post('status');
    	    $searchkey = $this->input->post('searchkey');
		    $offset = $this->input->post('offset');
		    $start = $this->input->post('start');
		    $end = $this->input->post('end');
		    $num = $this->input->post('num');
		    $comid = $this->input->post('comid');
		    $type = $this->input->post('type');
    	    $result = $this->orders_model->selectPageData($status, $start, $end, $searchkey, $offset, $num, $comid, $type);
	        if($result !== null){
	        	foreach($result['data'] as  $key=>$value){
	        		$company = null;
	        		if(isset($value['company_id']) && $value['company_id'] && $value['company_id'] > 0){
	        		    $company = $this->member_model->getDataById($value['company_id']);
	        		}
	        		$result['data'][$key]['com_name'] = $company !== null ? $company['name'] : '';
	        		$result['data'][$key]['com_mobile'] = $company !== null ? $company['mobile'] : '';
	        		$result['data'][$key]['cash_price'] = round($value['cash_price'], 2);
	        		$result['data'][$key]['total_cash_price'] = round($value['total_cash_price'], 2);
	        		
	        		$size_op = '';
			    	if($value['sku_id']){
			    		$sku = $this->goods_model->selectSkuRowById($value['sku_id']);
			    		if($sku !== null){
			    			if(strpos($sku['attributes'], ';') !== false){
			    				$attr_arr = explode(';', $sku['attributes']);
			    			}
			    			else{
			    				$attr_arr = array($sku['attributes']);
			    			}
			    			
			    			$goods = $this->goods_model->selectDataById($value['goods_id']);
			    			if($goods !== null){
				    			$options = json_decode($goods['options'], true);
				    			$option_arr = $options['options'];
				    			foreach($option_arr as $op){
				    				foreach($op['child'] as $child_item){
					    				foreach($attr_arr as $attr){
					    					if($child_item['attr_id'] == $attr){
					    						$size_op .= $op['title'].'：'.$child_item['title'].'   ';
					    					}
					    				}
				    				}
				    			}
			    			}
			    		}
			    	}
			    	$result['data'][$key]['size_op'] = $size_op;
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
	
    public function deleteBatch()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $idlist = $this->input->post('idlist');
    	    $idlist = json_decode($idlist);
    	    $result = $this->orders_model->deleteBatch($idlist);
	        if($result){
	        	$idlistString = implode(',', $idlist);
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '批量删除订单，idlist：'.$idlistString;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "data"=>$result, "message"=>"删除成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"删除失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
	/*
	 * 此处设计状态12345
	 */
    public function changeStatus()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $status = $this->input->post('status');
    	    $orderData = $this->orders_model->selectDataById($id);
		    if($orderData !== null){
		    	$return_money = false;
	        	if($status == 0){
	        		$valueMemo = '订单创建';
	        	}
	        	elseif($status == 1){
	        		$valueMemo = '订单支付';
	        	}
	            elseif($status == 2){
	        		$valueMemo = '订单发货';
	        	}
	            elseif($status == 3){
	        		$valueMemo = '订单完成';
	        	}
	            elseif($status == 4){
	            	/*
					 * 待发货的订单或者已发货的订单 关闭，需要退回用户所用掉的积分
					 */
		            if(($orderData['status'] == 1 || $orderData['status'] == 2) && $orderData['total_cash_price'] > 0){
		            	//待发货的订单或者已发货的订单 关闭，需要退回用户所用掉的积分及现金
	        		    $valueMemo = '订单有退款';
	        		    $return_money = true;
		            }
		            else{
		            	$valueMemo = '订单关闭';
		            }
	        	}
	        	else{
	        		$valueMemo ='--';
	        	}
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '修改订单ID：'.$orderData['order_id'].'，新状态'.$valueMemo;
			    $rtn_res = false;
			    
			    if($return_money){
			    	if(!strpos($orderData['pay_no'], 'walletpay')){
			    		//微信支付退款
				    	$status = 5;
				    	$total_fee = $orderData['total_cash_price'] * 100;
				    	$refund_fee = $total_fee;
				    	$this->load->model('wxpay_model');
				    	$update_res = $this->orders_model->changeStatus($id, $status);
				    	if($update_res){
				    		//退款申请成功，先改状态，再提出申请
				    	    $refund_res = $this->wxpay_model->refund($orderData['pay_no'], $orderData['order_id'], $total_fee, $refund_fee);
				    	    if($refund_res !== null){
				    	    	$rtn_res = true;
				    	    }
				    	}
			    	}
			    	else{
			    		//钱包支付退款
			    		if($orderData['share_id'] == 0){
			    			//非分享商品
			    		    $rtn_res = $this->orders_model->walletRefundOrder($orderData);
			    		}
			    		else{
			    			//分享商品
			    			$this->load->model('share_model');
			    			$rtn_res = $this->share_model->walletRefundOrderShare($orderData);
			    		}
			    	}
			    }
			    else{
			    	$rtn_res = $this->orders_model->changeStatus($id, $status);
			    }
			    
			    if($rtn_res){
				    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
				    $return = array("code"=>0, "message"=>"操作成功！");
			    }
			    else{
			    	$return = array("code"=>10001, "message"=>"操作失败");
			    }
		    }
		    else{
		    	$return = array("code"=>10002, "message"=>"订单不存在");
		    }
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
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
	
    public function submitLogisticsInfo()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $order_address = $this->input->post('order_address');
    	    $order_id = $this->input->post('order_id');
    	    $result = $this->orders_model->submitLogisticsInfo($id, $order_address);
	        if($result){
	        	$valueMemo = '订单发货';
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '修改订单ID：'.$order_id.'，新状态'.$valueMemo;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "message"=>"操作成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"操作失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function modifyLogisticsInfo()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $order_address = $this->input->post('order_address');
    	    $order_id = $this->input->post('order_id');
    	    $result = $this->orders_model->modifyLogisticsInfo($id, $order_address);
	        if($result){
	        	$valueMemo = '修改发货信息';
	        	$operater_id = $userInfo['id'];
			    $operater_name = $userInfo['name'];
			    $operater_desc = '修改订单ID：'.$order_id.'，新状态'.$valueMemo;
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "message"=>"操作成功！");
			}
			else{
				$return = array("code"=>10001, "message"=>"操作失败！");
			}
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
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
	
    public function refund()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
        $key = $this->input->post('key');
        $pwd_key = $this->config->item('pwd_key');
		$order_id = $this->decrypt($key, $pwd_key);

        $orderData = $this->orders_model->selectDataByOrderid($order_id);
	    if($orderData !== null && $orderData['status'] == 1 && $userInfo['id'] == $orderData['user_id']){
	    	$id = $orderData['id'];
	    	$status = 7;
	        $valueMemo = '退款申请';
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '关闭订单id：'.$id.'，新状态：'.$valueMemo;		    
		    $rtn_res = $this->orders_model->changeStatus($id, $status);
		    
		    if($rtn_res){
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "message"=>"操作成功！");
		    }
		    else{
		    	$return = array("code"=>10001, "message"=>"操作失败");
		    }
	    }
	    elseif($orderData !== null && $orderData['status'] != 1){
	    	$return = array("code"=>10002, "message"=>"该订单不能申请退款");
	    }
	    else{
	    	$return = array("code"=>10003, "message"=>"订单不存在");
	    }

    	echo json_encode($return);
		exit();
	}
	
	public function sureorder()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
        $key = $this->input->post('key');
        $pwd_key = $this->config->item('pwd_key');
		$order_id = $this->decrypt($key, $pwd_key);

        $orderData = $this->orders_model->selectDataByOrderid($order_id);
	    if($orderData !== null && $orderData['status'] == 2 && $userInfo['id'] == $orderData['user_id']){
	    	$id = $orderData['id'];
	    	$status = 3;
	    	$valueMemo = '确认';
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '确认订单id：'.$id.'，新状态：'.$valueMemo;
		    $rtn_res = $this->orders_model->changeStatus($id, $status);
	        if($rtn_res){
			    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
			    $return = array("code"=>0, "message"=>"操作成功！");
		    }
		    else{
		    	$return = array("code"=>10001, "message"=>"操作失败");
		    }
	    }
	    elseif($orderData !== null && $orderData['status'] != 2){
	    	$return = array("code"=>10002, "message"=>"该订单不能确认");
	    }
	    else{
	    	$return = array("code"=>10003, "message"=>"订单不存在");
	    }

    	echo json_encode($return);
		exit();
	}
	
    public function submitRefundOrder()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $order_id = $this->input->post('order_id');
    	    $orderData = $this->orders_model->selectDataById($id);
		    if($orderData !== null && ($orderData['status'] == 1 || $orderData['status'] == 2)){
		    	$rtn_res = false;
		    	$valueMemo = '';
		    	if($orderData['total_cash_price'] > 0){
		    		//有退款
		    	    if(!strpos($orderData['pay_no'], 'walletpay')){
			    		//微信支付退款
				    	$status = 5;
				    	$total_fee = $orderData['total_cash_price'] * 100;
				    	$refund_fee = $total_fee;
				    	$this->load->model('wxpay_model');
				    	$update_res = $this->orders_model->changeStatus($id, $status);
				    	if($update_res){
				    		//退款申请成功，先改状态，再提出申请
				    	    $refund_res = $this->wxpay_model->refund($orderData['pay_no'], $orderData['order_id'], $total_fee, $refund_fee);
				    	    if($refund_res !== null){
				    	    	$valueMemo = '退款中';
				    	    	$rtn_res = true;
				    	    }
				    	}
			    	}
			    	else{
			    		//钱包支付退款
			    		$valueMemo = '已退款';
			    		if($orderData['share_id'] == 0){
			    			//非分享商品
			    		    $rtn_res = $this->orders_model->walletRefundOrder($orderData);
			    		}
			    		else{
			    			//分享商品
			    			$this->load->model('share_model');
			    			$rtn_res = $this->share_model->walletRefundOrderShare($orderData);
			    		}
			    	}
		    	}
		    	else{
		    		//无退款，只退积分
		    		$status = 6;
		    		$valueMemo = '已退款';
		    		$rtn_res = $this->orders_model->changeStatus($id, $status);
		    	}
		    	
		        if($rtn_res){
		        	$operater_id = $userInfo['id'];
				    $operater_name = $userInfo['name'];
				    $operater_desc = '修改订单ID：'.$orderData['order_id'].'，新状态'.$valueMemo;
				    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
				    $return = array("code"=>0, "message"=>"操作成功！");
			    }
			    else{
			    	$return = array("code"=>10001, "message"=>"操作失败");
			    }
		    }
		    else{
		    	$return = array("code"=>10002, "message"=>"数据有误");
		    }
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
	
    public function checkRefundOrder()
	{
		$userInfo = $this->session->admin;
    	if($userInfo){
    	    $id = $this->input->post('id');
    	    $order_id = $this->input->post('order_id');
    	    $type = $this->input->post('type');
    	    $orderData = $this->orders_model->selectDataById($id);
		    if($orderData !== null && $orderData['status'] == 7){
		    	$valueMemo = '';
		    	$rtn_res = false;
		    	if($type == 'agree'){
			    	if($orderData['total_cash_price'] > 0){
			    		//有退款
			    	    if(!strpos($orderData['pay_no'], 'walletpay')){
				    		//微信支付退款
					    	$status = 5;
					    	$total_fee = $orderData['total_cash_price'] * 100;
					    	$refund_fee = $total_fee;
					    	$this->load->model('wxpay_model');
					    	$update_res = $this->orders_model->changeStatus($id, $status);
					    	if($update_res){
					    		//退款申请成功，先改状态，再提出申请
					    	    $refund_res = $this->wxpay_model->refund($orderData['pay_no'], $orderData['order_id'], $total_fee, $refund_fee);
					    	    if($refund_res !== null){
					    	    	$valueMemo = '退款中';
					    	    	$rtn_res = true;
					    	    }
					    	}
				    	}
				    	else{
				    		//钱包支付退款
				    		$valueMemo = '已退款';
					    	if($orderData['share_id'] == 0){
				    			//非分享商品
				    		    $rtn_res = $this->orders_model->walletRefundOrder($orderData);
				    		}
				    		else{
				    			//分享商品
				    			$this->load->model('share_model');
				    			$rtn_res = $this->share_model->walletRefundOrderShare($orderData);
				    		}
				    	}
			    	}
			    	else{
			    		//无退款，只退积分
			    		$status = 6;
			    		$valueMemo = '已退款';
			    		$rtn_res = $this->orders_model->changeStatus($id, $status);
			    	}
		    	}
		    	else{
		    		$status = 1;
		    		$valueMemo = '待发货';
		    		$rtn_res = $this->orders_model->changeStatus($id, $status);
		    	}
		    	
		        if($rtn_res){
		        	$operater_id = $userInfo['id'];
				    $operater_name = $userInfo['name'];
				    $operater_desc = '修改订单ID：'.$orderData['order_id'].'，新状态'.$valueMemo;
				    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
				    $return = array("code"=>0, "message"=>"操作成功！");
			    }
			    else{
			    	$return = array("code"=>10001, "message"=>"操作失败");
			    }
		    }
		    else{
		    	$return = array("code"=>10002, "message"=>"数据有误");
		    }
	    }
    	else{
    		$return = array("code"=>9999, "message"=>"您还没有登录！");
    	}
    	echo json_encode($return);
		exit();
	}
}