<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crontab extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('wxpay_model');
        $this->load->model('qqpay_model');
    	$this->load->model('setting_model');
    	$this->load->model('service_model');
        $this->load->model('member_model');
        $this->load->model('point_model');
        $this->load->model('crontab_model');
    }
    
    /*
     * 订单补单机制
     */
    public function order_auto_query_do(){
    	$noPayOrder = $this->orders_model->selectNotPayCompleteOrder();
    	if($noPayOrder !== null){
    		foreach($noPayOrder as $item){
    			if(!strpos($item['pay_no'], 'walletpay')){
	    			$out_trade_no = $item['order_id'];
	    			if($item['pay_no']){
	    				$transaction_id = $item['pay_no'];
	    			}
	    			else{
	    				$transaction_id = '';
	    			}
	    			
			        $callbackData = $this->wxpay_model->orderquery($transaction_id, $out_trade_no);
					if($callbackData && isset($callbackData['return_code']) && $callbackData['return_code'] == 'SUCCESS' && isset($callbackData['result_code']) && $callbackData['result_code'] == 'SUCCESS' && $callbackData['trade_state'] == 'SUCCESS')
					{
						//实际已经支付成功，但数据库未更改订单
						$order_id = $callbackData['out_trade_no'];
						$status = 1;
						$pay_time = date('Y-m-d H:i:s', strtotime($callbackData['time_end']));
						$this->orders_model->changeOrderPayStatus($order_id, $status, $callbackData['transaction_id'], $pay_time);
						log_message('info', '补单机制处理'.var_export($callbackData, true));
					}
    			}
    		}
    	}
    	exit;
    }
    
    /*
     * 报单补单机制
     */
    public function online_auto_query_do(){
    	$noPayOrder = $this->service_model->selectNotPayCompleteOrder();
    	if($noPayOrder !== null){
    		$schedule = $this->setting_model->getConfigDeclaration();
    		if($schedule !== null){
	    		foreach($noPayOrder as $item){
	    			if(!strpos($item['pay_no'], 'walletpay')){
		    			$recharge_user = $this->member_model->getDataById($item['recharge_user_id']);
		    			if($recharge_user !== null){
			    			$out_trade_no = $item['order_id'];
			    			if($item['pay_no']){
			    				$transaction_id = $item['pay_no'];
			    			}
			    			else{
			    				$transaction_id = '';
			    			}
			    			
					        $callbackData = $this->qqpay_model->orderquery($transaction_id, $out_trade_no);
							if($callbackData && isset($callbackData['return_code']) && $callbackData['return_code'] == 'SUCCESS' && isset($callbackData['result_code']) && $callbackData['result_code'] == 'SUCCESS' && $callbackData['trade_state'] == 'SUCCESS')
							{
								//实际已经支付成功，但数据库未更改报单
								$pay_time = date('Y-m-d H:i:s', strtotime($callbackData['time_end']));
								$res = $this->service_model->online_callback($item, $callbackData['transaction_id'], $pay_time, $schedule, $recharge_user['router']);
								if($res){
									log_message('info', '服务中心补单成功'.var_export($callbackData, true));
									echo $return['xml'];
								}
								else{
									echo '';
								}
							}
		    			}
	    			}
	    		}
    		}
    	}
    	exit;
    }
    
	/*
	 * 自动结算待用积分
	 */
	public function auto_clear_wait_point()
	{
		$success_num = 0;
		$fail_num = 0;
		$list = $this->point_model->get_clear_wait_point();
		if($list != null){
			foreach($list as $item){
			    $res = $this->point_model->do_clear_wait_point($item);
			    if($res){
			    	log_message('info', '自动结算待用积分'.var_export($item['id'], true));
			    	$success_num++;
			    }
			    else{
			    	$fail_num++;
			    }
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
	/*
	 * 发货15天自动确认订单
	 */
	public function auto_sure_order()
	{
		$success_num = 0;
		$fail_num = 0;
		$list = $this->orders_model->selectNotSureOrder();
		if($list != null){
			foreach($list as $item){
				$status = 3;
			    $res = $this->orders_model->changeStatus($item['id'], $status);
			    if($res){
			    	log_message('info', '发货7天自动确认订单'.var_export($item['id'], true));
			    	$success_num++;
			    }
			    else{
			    	$fail_num++;
			    }
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
	/*
	 * 2小时未支付自动关闭订单
	 */
	public function auto_close_order()
	{
		$success_num = 0;
		$fail_num = 0;
		$list = $this->orders_model->selectNotPayOrder();
		if($list != null){
			foreach($list as $item){
				$status = 4;
			    $res = $this->orders_model->changeStatus($item['id'], $status);
			    if($res){
			    	log_message('info', '2小时未支付自动关闭订单'.var_export($item['id'], true));
			    	$success_num++;
			    }
			    else{
			    	$fail_num++;
			    }
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
	/*
	 * 2小时未支付自动关闭报单
	 */
	public function auto_close_declaration()
	{
		$success_num = 0;
		$fail_num = 0;
		$list = $this->service_model->selectNotPayOrder();
		if($list != null){
			foreach($list as $item){
			    $res = $this->service_model->closeDeclaration($item['id']);
			    if($res){
			    	log_message('info', '2小时未支付自动关闭报单'.var_export($item['id'], true));
			    	$success_num++;
			    }
			    else{
			    	$fail_num++;
			    }
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
	/*
	 * 自动处理退款订单
	 */
    public function auto_process_refund_order()
	{
		$success_num = 0;
		$fail_num = 0;
		$list = $this->orders_model->selectRefundOrder();
		if($list != null){
			$this->load->model('wxpay_model');
			foreach($list as $item){
				if(!strpos($item['pay_no'], 'walletpay')){
					$query_res = $this->wxpay_model->orderquery($item['pay_no'], $item['order_id']);
					if($query_res['trade_state'] == 'REFUND'){
					    //退款成功  关闭订单,退回积分
					    $res = $this->orders_model->processRefundOrder($item, $query_res);
					    if($res){
					    	log_message('info', '退款已经成功，关闭订单'.var_export($item['id'], true));
					    	$success_num++;
					    }
					    else{
					    	$fail_num++;
					    }
					}
					elseif($query_res['trade_state'] == 'SUCCESS'){
						//重新发起退款申请
						$refund_res = $this->wxpay_model->refund($orderData['pay_no'], $orderData['order_id'], $total_fee, $refund_fee);
					    if($refund_res){
					    	log_message('info', '重新发起退款申请'.var_export($item['id'], true));
					    	$success_num++;
					    }
					    else{
					    	$fail_num++;
					    }
					}
					else{
						//查询退款结果
						$refund_res = $this->wxpay_model->refundquery($item['pay_no'], $item['order_id'], '', '');
						if($refund_res !== null){
							//有退款申请
							if($refund_res['refund_status_0'] == 'SUCCESS'){
								//退款成功  关闭订单
							    $res = $this->orders_model->processRefundOrder($item, $refund_res);
							    if($res){
							    	log_message('info', '退款成功'.var_export($item['id'], true));
							    	$success_num++;
							    }
							    else{
							    	$fail_num++;
							    }
							}
							elseif($refund_res['refund_status_0'] == 'FAIL'){
								//退款失败，回复原状态
								$res = $this->orders_model->changeStatus($item['id'], $item['last_status']);
							    if($res){
							    	log_message('info', '退款失败'.var_export($item['id'], true));
							    	$success_num++;
							    }
							    else{
							    	$fail_num++;
							    }
							}
						}
					}
				}
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
    //保留一个礼拜日志
    public function remove_old_file(){
    	header("content-type:text/html; charset=UTF-8");  
        $log_path = dirname(dirname(__FILE__)).'/logs/';
		$current_dir = @opendir($log_path);
		while ($filename = @readdir($current_dir))
		{
			if($filename != '.' && $filename != '..'){
				$file_base_array = explode('.', $filename);
				$file_base_time = substr($file_base_array[0], 4, 10);
				$file_time = strtotime($file_base_time.' 00:00:00') + 7*60*60*24;
				if($file_time < time()){
				    @unlink($log_path.$filename);
				}
			}
		}

		@closedir($current_dir);
    }
    
	/*
	 * 商家结算可用积分自动生成复投记录
	 * 上个月的所有未统计过的记录
	 */
	public function auto_generate_merchant_repeat()
	{
		$success_num = 0;
		$fail_num = 0;
		$day = intval(date('d', time()));
		if($day == 1){
			$thisMonthBeginTimestamp = strtotime(date('Y-m', time())."-01 00:00:00");
	        $lastMonthBeginTimestamp = strtotime("-1 months", $thisMonthBeginTimestamp);
	        $lastMonthEndTimestamp = $thisMonthBeginTimestamp - 1;
	        $beginTimestamp = strtotime(date('Y-m', $lastMonthBeginTimestamp)."-21 00:00:00");
	        $endTimestamp = $lastMonthEndTimestamp;
		}
		elseif($day == 11){
			$beginTimestamp = strtotime(date('Y-m', time())."-01 00:00:00");
	        $endTimestamp = $beginTimestamp + 10 * 24 * 60 * 60 - 1;
		}
	    elseif($day == 21){
			$beginTimestamp = strtotime(date('Y-m', time())."-11 00:00:00");
	        $endTimestamp = $beginTimestamp + 10 * 24 * 60 * 60 - 1;
		}

		$schedule = $this->setting_model->getConfigDeclaration();
    	if($schedule !== null && isset($beginTimestamp) && $beginTimestamp && isset($endTimestamp) && $endTimestamp){
    		$setting_res = false;
    		if($schedule['merchant_repeat_rate'] && strpos($schedule['merchant_repeat_rate'], '/') === false && $schedule['merchant_repeat_period'] && strpos($schedule['merchant_repeat_period'], '/') === false){
    			//只有一轮
    			$setting_res = true;
    			$merchant_repeat_rate_arr = array($schedule['merchant_repeat_rate']);
    		    $merchant_repeat_period_arr = array($schedule['merchant_repeat_period']);
    		}
    	    elseif(strpos($schedule['merchant_repeat_rate'], '/') !== false && strpos($schedule['merchant_repeat_period'], '/') !== false){
    	    	//同为多轮
    	    	$merchant_repeat_rate_arr = explode('/', $schedule['merchant_repeat_rate']);
    	    	$merchant_repeat_period_arr = explode('/', $schedule['merchant_repeat_period']);
    	    	if(count($merchant_repeat_rate_arr) == count($merchant_repeat_period_arr)){
    	    		//轮数设置相同
    			    $setting_res = true;
    	    	}
    		}
    		
    		if($setting_res){
    			$start = date('Y-m-d H:i:s', $beginTimestamp);
	            $end = date('Y-m-d H:i:s', $endTimestamp);
				$list = $this->point_model->getMerchantNotRepeatData($start, $end);
				if($list != null){
					foreach($list as $item){
						$order_id = $this->createRandNum();
						$res = $this->point_model->generate_merchant_repeat($order_id, $item['user_id'], $item['point'], $merchant_repeat_rate_arr, $merchant_repeat_period_arr, $start, $end);
					    if($res){
					    	log_message('info', '商家结算可用积分自动生成复投记录user_id：'.var_export($item['user_id'], true));
					    	$success_num++;
					    }
					    else{
					    	$fail_num++;
					    }
					}
				}
    		}
    	}
    	
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
    private function createRandNum($str = '')
    {
        return $str.date('YmdHis') . rand(100000, 999999);
    }
    
    /*
	 * 自动发送短信
	 */
	public function auto_send_msg()
	{
		$success_num = 0;
		$tpl = $this->crontab_model->smsTemplate();
		if($tpl !== null){
			foreach($tpl as $item){
				//充值提醒
				if($item['type'] == 4 && $item['status'] == 1){
					$chargeData = $this->crontab_model->getChargeData();
					if($chargeData !== null){
						foreach($chargeData as $charge){
							if($item['content'] && strpos($item['content'], '{money}') !== false){
								$user = $this->member_model->getDataById($charge['recharge_user_id']);
								if($user !== null){
					            	$message = $item['content'];
						            $message = str_replace('{money}', $charge['recharge_money'] ,$message);
						            $res = $this->send(4, $message, $user['mobile']);
						            if($res['code'] == 0){
						            	$this->crontab_model->update_charge_send_status($charge['id'], 1);
						            	$success_num++;
						            }
								}
				            }
						}
					}
				}
				
				//待用积分结算提醒
				if($item['type'] == 5 && $item['status'] == 1){
				    $clearData = $this->crontab_model->getClearData();
				    if($clearData !== null){
						foreach($clearData as $clear){
						    $user = $this->member_model->getDataById($clear['user_id']);
							if($user !== null){
				            	$message = $item['content'];
					            $res = $this->send(5, $message, $user['mobile']);
					            if($res['code'] == 0){
					            	$this->crontab_model->update_clear_send_status($clear['id'], 1);
					            	$success_num++;
					            }
							}
						}
				    }
				}
				
				//复投开启提醒
				if($item['type'] == 6 && $item['status'] == 1){
				    $repeatOpenData = $this->crontab_model->getRepeatOpenData();
				    if($repeatOpenData !== null){
						foreach($repeatOpenData as $open){
						    $user = $this->member_model->getDataById($open['user_id']);
							if($user !== null){
				            	$message = $item['content'];
					            $res = $this->send(6, $message, $user['mobile']);
					            if($res['code'] == 0){
					            	$this->crontab_model->update_repeat_send_open_status($open['id'], 1);
					            	$success_num++;
					            }
							}
						}
				    }
				}
				
				//复投结束前一礼拜提醒
				if($item['type'] == 7 && $item['status'] == 1){
				    $repeatWeekData = $this->crontab_model->getRepeatWeekData();
			 	    if($repeatWeekData !== null){
						foreach($repeatWeekData as $week){
						    $user = $this->member_model->getDataById($week['user_id']);
							if($user !== null){
				            	$message = $item['content'];
					            $res = $this->send(7, $message, $user['mobile']);
					            if($res['code'] == 0){
					            	$this->crontab_model->update_repeat_send_week_status($week['id'], 1);
					            	$success_num++;
					            }
							}
						}
				    }
				}
				
				//复投结束前一天提醒
				if($item['type'] == 8 && $item['status'] == 1){
				    $repeatDayData = $this->crontab_model->getRepeatDayData();
				    if($repeatDayData !== null){
						foreach($repeatDayData as $day){
						    $user = $this->member_model->getDataById($day['user_id']);
							if($user !== null){
				            	$message = $item['content'];
					            $res = $this->send(8, $message, $user['mobile']);
					            if($res['code'] == 0){
					            	$this->crontab_model->update_repeat_send_day_status($day['id'], 1);
					            	$success_num++;
					            }
							}
						}
				    }
				}
				
				//订单发货提醒
				if($item['type'] == 9 && $item['status'] == 1){
				    $orderSendData = $this->crontab_model->getOrderSendData();
				    if($orderSendData !== null){
						foreach($orderSendData as $order){
							if($order['order_address']){
								$mobile = '';
								$order_add_arr = json_decode($order['order_address'], true);
								if($order_add_arr['telNumber']){
									$mobile = $order_add_arr['telNumber'];
								}
								else{
									$user = $this->member_model->getDataById($order['user_id']);
									if($user !== null){
									    $mobile = $user['mobile'];
									}
								}
								
								if($mobile){
					            	$message = $item['content'];
						            $res = $this->send(9, $message, $mobile);
						            if($res['code'] == 0){
						            	$this->crontab_model->update_order_send_status($order['id'], 1);
						            	$success_num++;
						            }
								}
							}
						}
				    }
				}
				
			    //订单支付提醒
				if($item['type'] == 10 && $item['status'] == 1){
				    $orderPayData = $this->crontab_model->getOrderPayData();
				    if($orderPayData !== null){
				    	$this->load->model('goods_model');
						foreach($orderPayData as $order){
							$goods = $this->goods_model->selectDataById($order['goods_id']);
							if($goods !== null && $goods['company_id']){
								$mobile = '';
							    $user = $this->member_model->getDataById($goods['company_id']);
								if($user !== null && $user['is_message'] == 1){
								    $mobile = $user['mobile'];
								}
								
								if($mobile){
					            	$message = $item['content'];
					            	if($item['content'] && strpos($item['content'], '{good}') !== false){
						            	$message = str_replace('{good}', $order['goods_name'] ,$message);
							            $res = $this->send(10, $message, $mobile);
							            if($res['code'] == 0){
							            	$this->crontab_model->update_order_send_pay_status($order['id'], 1);
							            	$success_num++;
							            }
					            	}
								}
							}
						}
				    }
				}
			}
		}
		
		echo $success_num.' success';
		exit;
	}
	
	private function send($type, $message, $mobile){
		$this->load->model('sms_model');
		$insert_id = $this->sms_model->insert($mobile, $message, $type);
		if($insert_id){
			$resJson = $this->sms_model->send($mobile, $message);
			$res = json_decode($resJson, true);
			if($res && $res['returnstatus'] && strtolower($res['returnstatus']) == 'success'){
				//修改发送状态
				$updateInfo = $this->sms_model->update($insert_id);
        	    $return = array("code"=>0, "message"=>"发送成功！");
			}
		    else{
		    	$return = array("code"=>10001, "message"=>"发送失败！");
		    }
		}
		else{
        	$return = array("code"=>10002, "message"=>"插入数据失败！");
        }
        
        return $return;
	}
	
	public function auto_share_goods_commision()
	{
		$success_num = 0;
		$fail_num = 0;
		$list = $this->orders_model->selectNotClearShare();
		if($list != null){
			$this->load->model('goods_model');
			foreach($list as $item){
				if($item['share_uid'] != 0){
					$share_user = $this->member_model->getDataById($item['share_uid']);
					if($share_user !== null){
						$goods = $this->goods_model->selectDataById($item['goods_id']);
						if($goods !== null){
						    $res = $this->orders_model->clearShare($item, $goods, $share_user);
						    if($res){
						    	log_message('info', '自动结算确认订单的分享佣金'.var_export($item['id'], true));
						    	$success_num++;
						    }
						    else{
						    	$fail_num++;
						    }
						}
					}
				}
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
	
	public function auto_clear_share_goods()
	{
		$this->load->model('share_model');
		$success_num = 0;
		$fail_num = 0;
		$list = $this->share_model->selectNotClearTimeoutShare();
		if($list != null){
			foreach($list as $item){
			    $res = $this->share_model->dealSharemanager($item);
			    if($res){
			    	log_message('info', '自动结算分享记录'.var_export($item['id'], true));
			    	$success_num++;
			    }
			    else{
			    	$fail_num++;
			    }
			}
		}
		echo $success_num.' success,'.$fail_num.' fail.';
		exit;
	}
}