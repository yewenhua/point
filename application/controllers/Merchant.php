<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Merchant extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('merchant_model');
        $this->load->model('orders_model');
    }
    
    public function index(){
    	$this->load->model('system_model');
    	$host = $this->getPageHost();
    	$system = $this->system_model->getSystemData();
    	$hash_host = md5('cnvp'.$host.'goodluck');

    	$data = array();
    	if($system && isset($system['privilege_key']) && $system['privilege_key'] == $hash_host){
    		$data['title'] = $system['title'];
	    	$data['debug'] = 'dev';
			$this->load->view('admin/business', $data);
    	}
    	else{
    		$this->load->view('admin/noprivilege', $data);
    	}
    }
    
   /**
	 * signin.
	 */
	public function signin()
	{
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		$data = array(
			"name"=>$name,
			"password"=>$password
		);
		
		$result = $this->merchant_model->dologin($data);
		if($result != null){
			$operater_id = $result['id'];
		    $operater_name = $result['name'];
		    $operater_desc = $result['name'].'商家登录';
		    $this->log_model->insert_operate_log($operater_id, $operater_name, $operater_desc);
		    unset($result['open_id']);
		    unset($result['service_openid']);
		    unset($result['router']);

			$this->session->merchant = $result;
		    $return = array("code"=>0,"data"=>$result, "message"=>"登录成功！");
		}
		else{
			$this->session->merchant = '';
			$return = array("code"=>10001,"data"=>$result, "message"=>"登录失败！");
		}
		
		echo json_encode($return);
		exit();
	}
	
   /**
	 * 登出页面.
	 * 管理员退出
	 */
	public function logout()
	{
		$this->session->merchant = '';
    	$return = array("code"=>0, "message"=>"商家退出成功！");
    	echo json_encode($return);
		exit();
	}
	
    public function chgpwd()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
        $id = $this->input->post('id');
        $password = $this->input->post('password');
        $newpassword = $this->input->post('newpassword');
        $data = array(
			"id"=>$id,
			"password"=>$password,
            "newpassword"=>$newpassword
		);
        $result = $this->merchant_model->chgpwd($data);
        if($result){
		    $return = array("code"=>0, "message"=>"修改成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"修改失败！");
		}
	    
    	echo json_encode($return);
		exit();
	}
	
	public function getPageHost(){  
	    $url = $_SERVER['HTTP_HOST'];  
	    return $url;  
	} 
	
   /**
	 * 所有订单数据.
	 */
    public function allOrderStatusData()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
        $result = $this->merchant_model->allOrderStatusData($userInfo['id']);
        if($result !== null){
        	$waitPay = 0;
        	$alreadyPay = 0;
        	$alreadySend = 0;
        	$completed = 0;
        	$closed = 0;
        	$refund = 0;
        	$monthOrder = array();
        	$begin = date('Y-m-d', time()-30*24*60*60);
        	
        	foreach($result as $item){
        		if($item['status'] == 0){
        			$waitPay++;
        		}
        		elseif($item['status'] == 1){
        			$alreadyPay++;
        		}
        	    elseif($item['status'] == 2){
        			$alreadySend++;
        		}
        	    elseif($item['status'] == 3){
        			$completed++;
        		}
        	    elseif($item['status'] == 4){
        			$closed++;
        		}
        	    elseif($item['status'] == 5){
        			$refund++;
        		}
        		
        		$itemTime = strtotime($item['created_at']);
        		$start = strtotime(date('Y-m-d', time() - 30*24*60*60).' 00:00:00');
        	    $end = strtotime(date('Y-m-d', time() - 24*60*60).' 23:59:59');
        		if($itemTime >= $start && $itemTime <= $end && $item['status'] != 0){
        			$monthOrder[] = $item;
        		}
        	}
        	
            $monthData = array();
        	if(!empty($monthOrder)){
        		for($i=0; $i<30; $i++){
        			$index = date('m-d', strtotime($begin) + 24 * 60 * 60 * $i);
        			$monthData[$index] = 0;
        		}
        		
        		foreach ($monthOrder as $item){
        			$index = substr($item['created_at'], 5, 5);
        			$monthData[$index]++;
        		}
        	}

        	$orderNum = array(
        	    "waitPay"=>$waitPay,
	        	"alreadyPay"=>$alreadyPay,
	        	"alreadySend"=>$alreadySend, 
	        	"completed"=>$completed, 
	        	"closed"=>$closed,
        	    "refund"=>$refund
        	);
		    $return = array("code"=>0, "data"=>$result, "allOrderNum"=>count($result), "statusNum"=>$orderNum, "monthData"=>$monthData, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
	/**
	 * 过去一个月现金流
	 */
    public function welcomeCashData()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
		$start = date('Y-m-d', time()-30*24*60*60);
		
		$monthOrderData = array();
        $resOrder = $this->merchant_model->welcomeOrderData($userInfo['id']);
        if($resOrder !== null){
        	if(!empty($resOrder)){
        		for($i=0; $i<30; $i++){
        			$index = date('m-d', strtotime($start) + 24 * 60 * 60 * $i);
        			$monthOrderData[$index] = 0;
        		}
        		
        		foreach ($resOrder as $key=>$item){
        			$index = substr($item['created_at'], 5, 5);
        			$monthOrderData[$index] = $monthOrderData[$index] + round($item['total_cash_price'], 2);
        		}
        	}
		}
	
		$result = array(
            "monthOrderData"=>$monthOrderData
        );
        
        $return = array("code"=>0, "data"=>$result);
	   
    	echo json_encode($return);
		exit();
	}
	
    public function allCashData()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
        $result = $this->merchant_model->allCashData($userInfo['id']);
        if($result !== null){
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}

    	echo json_encode($return);
		exit();
	}
	
    public function getNewMessage()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
    		
    	$data = $this->merchant_model->getWaitSendData($userInfo['id']);
    	if($data !== null){
    		$return = array("code"=>0, "data"=>$data, "count"=>count($data), "message"=>"获取成功！");
    	}
    	else{
    		$return = array("code"=>10001, "message"=>"没有数据");
    	}
	    
    	echo json_encode($return);
		exit();
	}
	
	/*
	 * merchant
	 */
    public function selectPageData()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
    	$status = $this->input->post('status');
        $searchkey = $this->input->post('searchkey');
	    $offset = $this->input->post('offset');
	    $time = $this->input->post('time');
	    $num = $this->input->post('num');
        $result = $this->merchant_model->selectPageData($userInfo['id'], $status, $time, $searchkey, $offset, $num);
        if($result !== null){
        	foreach($result['data'] as  $key=>$value){
        		$result['data'][$key]['cash_price'] = round($value['cash_price'], 2);
        		$result['data'][$key]['total_cash_price'] = round($value['total_cash_price'], 2);
        		
        		$size_op = '';
		    	if($value['sku_id']){
		    		$this->load->model('goods_model');
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
    
    	echo json_encode($return);
		exit();
	}
	
    public function changeStatus()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
        $id = $this->input->post('id');
        $status = 2;//发货
        $orderData = $this->orders_model->selectDataById($id);
	    if($orderData !== null){
        	$valueMemo = '订单发货';
        	$operater_id = $userInfo['id'];
		    $operater_name = $userInfo['name'];
		    $operater_desc = '修改订单订单id：'.$id.'，新状态：'.$valueMemo;
		    
		    $rtn_res = $this->orders_model->changeStatus($id, $status);
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
		    
    	echo json_encode($return);
		exit();
	}
	
    public function submitLogisticsInfo()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
        $id = $this->input->post('id');
        $order_id = $this->input->post('order_id');
        $order_address = $this->input->post('order_address');
        $order = $this->orders_model->selectDataById($id);
        if($order){
        	if($order['status'] == 1){
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
        		$return = array("code"=>10002, "message"=>"订单已发货");
        	}
        }
        else{
        	$return = array("code"=>10003, "message"=>"订单不存在");
        }

    	echo json_encode($return);
		exit();
	}
	
    public function outputExcelData()
	{
		$this->auth_json_merchant();
		$userInfo = $this->merchant;
		$status = $this->input->post('status');
		$time = $this->input->post('time');
		$search = $this->input->post('search');
		$dataByStatus = $this->merchant_model->outputOrderExcelData($userInfo['id'], $status, $time, $search);
		// Starting the PHPExcel library
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel
		->getProperties()
		->setCreator( "yewenhua")
		->setTitle("订单")
		->setDescription("订单列表");
		
		//设置当前的sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objActSheet = $objPHPExcel->getActiveSheet();
		//设置sheet名
		$sheetName = "order_".date('Y-m-d', time());
        $objActSheet->setTitle($sheetName);
    	
    	//设置宽度及居中
    	$objActSheet->getColumnDimension( 'A')->setWidth(12);
    	$objActSheet->getColumnDimension( 'B')->setWidth(40);
    	$objActSheet->getColumnDimension( 'C')->setWidth(12);
    	$objActSheet->getColumnDimension( 'D')->setWidth(15);
    	$objActSheet->getColumnDimension( 'E')->setWidth(12);
    	$objActSheet->getColumnDimension( 'F')->setWidth(25);
    	$objActSheet->getColumnDimension( 'G')->setWidth(25);
    	$objActSheet->getColumnDimension( 'H')->setWidth(12);
    	$objActSheet->getColumnDimension( 'I')->setWidth(22);
    	$objActSheet->getColumnDimension( 'J')->setWidth(12);
    	$objActSheet->getColumnDimension( 'K')->setWidth(15);
    	$objActSheet->getColumnDimension( 'L')->setWidth(15);
    	
    	//表头名称
    	$objActSheet->setCellValue( 'A1', "姓名")
    	->setCellValue( 'B1', "地址")
    	->setCellValue( 'C1', "单位名称") 
    	->setCellValue( 'D1', "手机号码")
    	->setCellValue( 'E1', "邮编")
    	->setCellValue( 'F1', "订单号")
    	->setCellValue( 'G1', "商品名")
    	->setCellValue( 'H1', "规格数量")
    	->setCellValue( 'I1', "订单备注")
    	->setCellValue( 'J1', "运费")
    	->setCellValue( 'K1', "快递公司")
    	->setCellValue( 'L1', "快递单号");
    	
    	$col = 2;
    	$i=0;
    	foreach($dataByStatus as $key=>$item){
    		$i++;
    		$status = '';
    		if($item['status'] == 0){
    			$status = '未支付';
    		}
    		elseif($item['status'] == 1){
    			$status = '待发货';
    		}
    	    elseif($item['status'] == 2){
    			$status = '已发货';
    		}
    	    elseif($item['status'] == 3){
    			$status = '已完成';
    		}
    	    elseif($item['status'] == 4){
    			$status = '已关闭';
    		}
    	    elseif($item['status'] == 5){
    			$status = '退款中';
    		}
    		
    		$addressObj = json_decode($item['order_address'], true);
    		$provinceName = isset($addressObj['provinceName']) ? $addressObj['provinceName'] : '';
    		$cityName = isset($addressObj['cityName']) ? $addressObj['cityName'] : '';
    		$countryName = isset($addressObj['countryName']) ? $addressObj['countryName'] : '';
    		$detailInfo = isset($addressObj['detailInfo']) ? $addressObj['detailInfo'] : '';
    		$address = $provinceName.$cityName.$countryName.$detailInfo;
    		if($item['logistic_fee'] > 0){
    			$logistic_fee = $item['logistic_fee'].'元';
    		}
    		else{
    			$logistic_fee = '免邮';
    		}
    		
    		$logisticName = isset($addressObj['logisticName']) ? $addressObj['logisticName'] : '';
    		$logisticNo = isset($addressObj['logisticNo']) ? $addressObj['logisticNo'] : '';
    		$userName = isset($addressObj['userName']) ? $addressObj['userName'] : '';
    		$telNumber = isset($addressObj['telNumber']) ? $addressObj['telNumber'] : '';
    		$postalCode = isset($addressObj['postalCode']) ? $addressObj['postalCode'] : '';
    		
    		$objActSheet->setCellValue( "A{$col}", $userName." ")
    		->setCellValue( "B{$col}", $address)
	    	->setCellValue( "C{$col}", '')
	    	->setCellValue( "D{$col}", $telNumber)
	    	->setCellValue( "E{$col}", $postalCode)
	    	->setCellValue( "F{$col}", $item['order_id']." ")
	    	->setCellValue( "G{$col}", $item['goods_name'])
	    	->setCellValue( "H{$col}", $item['num'])
	    	->setCellValue( "I{$col}", $item['memo'])
	    	->setCellValue( "J{$col}", $logistic_fee)
	    	->setCellValue( "K{$col}", $logisticName)
	    	->setCellValue( "L{$col}", $logisticNo." ");
	    	
	    	$col++;
    	}
    	
    	$col = $col - 1;
    	$styleThinBlackBorderOutline = array(
    		'borders' => array (
    			'outline' => array (
    				'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
    				'color' => array ('argb' => 'FF000000'),         //设置border颜色
    			),
    		),
    	);
    	$objActSheet->getStyle("A1:L1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    	$objActSheet->getStyle("A1:L1")->getFill()->getStartColor()->setARGB('FFCFCFCF');
    	$objActSheet->getRowDimension('1')->setRowHeight(20);
    	$objActSheet->getStyle("A1:L1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    	$objActSheet->getStyle("A1:L1")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    	$objActSheet->getStyle("A1:L1")->getFont()->setBold(true);
    	$objActSheet->getStyle( "A1:L{$col}")->applyFromArray($styleThinBlackBorderOutline);
    	$fileName = "order_".date('Y-m-d', time());
		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		//发送标题强制用户下载文件
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=\"{$fileName}.xlsx\"");
		header('Cache-Control: max-age=0');
		
		$objWriter->save('php://output');
	}
}