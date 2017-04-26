<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mall extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('mall_model');
        $this->load->model('goods_model');
    }
    
    public function center(){
    	$data = array();
    	$data['title'] = '商城首页';
		$data['page_id'] = 'homepage';
		$data['menu'] = 'mall';
		$data['env'] = $this->env;
		
		$bannerlist = $this->mall_model->allBannerData();
		if($bannerlist !== null){
			$url = $this->getPageUrl();
	    	$jsapi = $this->jsapi($url);
	    	$data = array_merge($data, $jsapi);
	    	$data['share_img'] = base_url("media/img/share_img.png");
	    	$data['url'] = $url;
	    	$data['bannerlist'] = $bannerlist;
	    	
	    	$recommend_goods = array();
	    	$category = $this->mall_model->selectHomeCategory();
	    	if($category !== null){
	    		foreach($category as $item){
	    		    $good_data = $this->mall_model->selectHomeGoods($item['path']);
	    		    if($good_data !== null){
	    		    	$pwd_key = $this->config->item('pwd_key');
				    	foreach($good_data as  $key=>$value){
			        		$imgData = json_decode($value['img_data'], true);
			        		$flag = false;
			        		foreach($imgData as $imgkey=>$imgitem){
			        			if($imgitem['selected']){
			        				$good_data[$key]['face'] = $imgitem['file'];
			        				$flag = true;
			        			}
			        		}
			        		if(!$flag){
			        			$good_data[$key]['face'] = $imgData[0]['file'];
			        		}
			        		
					    	$good_data[$key]['key'] = $this->encrypt($value['id'], $pwd_key);
					    	$good_data[$key]['cash_price'] = round($value['cash_price'], 2);
			        	}
			        	
			        	$cate_key = $this->encrypt($item['path'], $pwd_key);
	    		    	$recommend_goods[] = array(
	    		    	   'key' => $cate_key,
	    		    	   'face_img' => $item['img_url'],
	    		    	   'data' => $good_data
	    		    	);
	    		    }
	    		}
	    	}
	    	$data['recommend_goods'] = $recommend_goods;
	    	
	        if($this->systemInfo !== null){
	    		$systemInfo = $this->systemInfo;
	    	    $data['systemInfo'] = $this->systemInfo;
	    	}
	    	else{
	    		$systemInfo =array();
	    		$systemInfo['site_name'] = '';
	    		$data['systemInfo'] = '';
	    	}
	    	
			$data['page_css'] = array(
			    '/media/vendor/alternate/alternate.css'
			);
			$data['page_js'] = array(
			    '/media/vendor/alternate/alternate.js',
			    'http://res.wx.qq.com/open/js/jweixin-1.1.0.js'
			);
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/mall/home.js?v='.rand(1,10).'.'.rand(1,10)
		    );
			
			$this->load->view('header', $data);
			$this->load->view('mall/home', $data);
			$this->load->view('footer', $data);
		}
		else{
			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">获取广告图片出错</div>';
    		exit;
		}
    }
    
    public function home(){
    	$data = array();
    	$searchkey = '';
    	$offset = 0;
    	$num = 10;
    	
        $list = $this->goods_model->recommend_goods($searchkey, $offset, $num);
        if($list !== null){
        	foreach($list['data'] as  $key=>$value){
        		$imgData = json_decode($value['img_data'], true);
        		$flag = false;
        		foreach($imgData as $imgkey=>$imgitem){
        			if($imgitem['selected']){
        				$list['data'][$key]['face'] = $imgitem['file'];
        				$flag = true;
        			}
        		}
        		if(!$flag){
        			$list['data'][$key]['face'] = $imgData[0]['file'];
        		}
        		$pwd_key = $this->config->item('pwd_key');
		    	$list['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
		    	$list['data'][$key]['cash_price'] = round($value['cash_price'], 2);
        	}
		    $data['list'] = $list;
		    $data['totalPage'] = ceil($list['count']/$num);
		}
		else{
			$data['list'] = array();
			$data['totalPage'] = 1;
		}
		
		$bannerlist = $this->mall_model->allBannerData();
		if($bannerlist !== null){
			$url = $this->getPageUrl();
	    	$jsapi = $this->jsapi($url);
	    	$data = array_merge($data, $jsapi);
	    	$data['share_img'] = base_url("media/img/share_img.png");
	    	$data['url'] = $url;
	    	if($this->systemInfo !== null){
	    		$systemInfo = $this->systemInfo;
	    	    $data['systemInfo'] = $this->systemInfo;
	    	}
	    	else{
	    		$systemInfo =array();
	    		$systemInfo['site_name'] = '';
	    		$data['systemInfo'] = '';
	    	}
	    	
			$data['bannerlist'] = $bannerlist;
			$data['title'] = '商城首页';
			$data['page_id'] = 'centerpage';
			$data['menu'] = 'mall';
			$data['env'] = $this->env;
			$data['page_css'] = array(
			    '/media/vendor/alternate/alternate.css'
			);
			$data['page_js'] = array(
			    '/media/vendor/alternate/alternate.js',
			    'http://res.wx.qq.com/open/js/jweixin-1.1.0.js'
			);
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/mall/center.js?v='.rand(1,10).'.'.rand(1,10)
		    );
			
			$this->load->view('header', $data);
			$this->load->view('mall/center', $data);
			$this->load->view('footer', $data);
		}
		else{
			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">获取广告图片出错</div>';
    		exit;
		}
    }
    
    public function detail(){
    	$data = array();
    	$key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
		$id = $this->decrypt($key, $pwd_key);
    	$goods = $this->goods_model->selectDataById($id);
    	if($goods !== null){
    		$already_buy = 0;
    		$level = 99;
    		$userInfo = $this->session->user;
	    	if($userInfo){
	    		$already_buy = $this->mall_model->getAlreadyBuyNum($goods['id'], $userInfo['id']);
	    		$level = $userInfo['level'];
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
	    	
	    	$url = $this->getPageUrl();
	    	$jsapi = $this->jsapi($url);
	    	$data = array_merge($data, $jsapi);
	    	$data['share_img'] = base_url("backend/uploads/".$goods['face']);

	    	$data['level'] = $level;
	    	$data['already_buy'] = $already_buy;
	    	$skuParam = array();
	    	$goods['key'] = $key;
	    	$goods['options'] = $goods['options'] ? json_decode($goods['options'], true) : array();
	    	if(isset($goods['options']['is_option']) && $goods['options']['is_option'] == 1){
	    		//不包括库存为0的
	    		$skuData = $this->goods_model->selectSkuDataByGidNotZero($goods['id']);
	    		$size = 0;
	    		
	    		if($skuData !== null){
		    		foreach($skuData as $item){
		    			$size = $size + intval($item['total']);
		    			//为防止以后增加属性
		    			$skuParam[$item['attributes']] = array(
		    			   'count' => intval($item['total'])
		    			);
		    		}
	    		}
	    		
	    		$goods['total'] = $size;
	    	}
	    	
	    	$goods['sku'] = $skuParam;
	    	$data['goods'] = $goods;
	    	$data['imglist'] = $imgData;

	    	$data['title'] = '商品详情';
			$data['page_id'] = 'detailpage';
			$data['menu'] = 'mall';
			$data['env'] = $this->env;
			$data['page_css'] = array(
			    '/media/vendor/alternate/alternate.css',
			);
			$data['page_js'] = array(
			    '/media/vendor/alternate/alternate.js',
			    '/media/vendor/react/react.js',
			    '/media/vendor/react/react-dom.js',
			    '/media/vendor/react/browser.min.js',
			    'http://res.wx.qq.com/open/js/jweixin-1.1.0.js'
			);
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/mall/detail.js?v='.rand(1,10).'.'.rand(1,10)
			);
			
			$data['systemInfo'] = $this->systemInfo;
	    	$this->load->view('header', $data);
			$this->load->view('mall/detail', $data);
			$this->load->view('footer', $data);
    	}
    	else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
    }
    
    public function goodslist(){
    	$data = array();
    	$wd = $this->input->get('wd');
    	if($wd){
    	    $wd = urldecode($wd);
    	}
    	
    	$key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
    	if($key && $key != 'all'){
		    $path = $this->decrypt($key, $pwd_key);
    	}
    	else{
    		$path = 'all';
    	}
    	
    	$data['wd'] = $wd;
    	$data['path'] = $path;
    	$data['title'] = '商品分类列表';
		$data['page_id'] = 'goodslistpage';
		$data['menu'] = 'category';
		$data['env'] = $this->env;
		
		//分享信息
        $url = $this->getPageUrl();
    	$jsapi = $this->jsapi($url);
    	$data = array_merge($data, $jsapi);
    	$data['share_img'] = base_url("media/img/share_img.png");
    	$data['url'] = $url;
    	if($this->systemInfo !== null){
    		$systemInfo = $this->systemInfo;
    	    $data['systemInfo'] = $this->systemInfo;
    	}
    	else{
    		$systemInfo =array();
    		$systemInfo['site_name'] = '';
    		$data['systemInfo'] = '';
    	}
    	if($path !== 'all'){
    	    $category = $this->mall_model->getCategoryByPath($path);
    	    $data['category'] = $category['label'];
    	}
    	else{
    		$data['category'] = '所有';
    	}
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    'http://res.wx.qq.com/open/js/jweixin-1.1.0.js'
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/mall/goodslist.js?v='.rand(1,10).'.'.rand(1,10)
	    );
    	
    	$this->load->view('header', $data);
		$this->load->view('mall/goodslist', $data);
		$this->load->view('footer', $data);
    }
    
    public function list_page_info()
	{
		$path = $this->input->post('path');
		$sort = $this->input->post('sort');
	    $searchkey = $this->input->post('searchkey');
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $offset = ($page - 1) * $num;
        $result = $this->goods_model->list_page_info($path, $sort, $searchkey, $offset, $num);
        if($result !== null){
        	foreach($result['data'] as  $key=>$value){
        		$imgData = json_decode($value['img_data'], true);
        		$flag = false;
        		foreach($imgData as $imgkey=>$imgitem){
        			if($imgitem['selected']){
        				$result['data'][$key]['face'] = $imgitem['file'];
        				$flag = true;
        			}
        		}
        		if(!$flag){
        			$result['data'][$key]['face'] = $imgData[0]['file'];
        		}
        		$pwd_key = $this->config->item('pwd_key');
		    	$result['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
		    	$result['data'][$key]['cash_price'] = round($value['cash_price'], 2);
        	}
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function category(){
    	$data = array();
    	$category_list = $this->goods_model->category();
    	if($category_list !== null){
    		foreach($category_list as $key=>$item){
    			$pwd_key = $this->config->item('pwd_key');
    			$category_list[$key]['key'] = $this->encrypt($item['path'], $pwd_key);
    		}
    	}
    	
    	$data['category'] = $category_list !== null ? $category_list : array();
    	$data['title'] = '商品分类';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'category';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/mall/category.js?v='.rand(1,10).'.'.rand(1,10)
	    );
		$data['systemInfo'] = $this->systemInfo;
		$this->load->view('header', $data);
		$this->load->view('mall/category', $data);
		$this->load->view('footer', $data);
    }
    
    public function recommend_goods()
	{
	    $searchkey = $this->input->post('searchkey');
	    $num = $this->input->post('num');
	    $page = $this->input->post('page');
	    $offset = ($page - 1) * $num;
        $list = $this->goods_model->recommend_goods($searchkey, $offset, $num);
	    if($list !== null){
        	foreach($list['data'] as  $key=>$value){
        		$imgData = json_decode($value['img_data'], true);
        		$flag = false;
        		foreach($imgData as $imgkey=>$imgitem){
        			if($imgitem['selected']){
        				$list['data'][$key]['face'] = $imgitem['file'];
        				$flag = true;
        			}
        		}
        		if(!$flag){
        			$list['data'][$key]['face'] = $imgData[0]['file'];
        		}
        		$pwd_key = $this->config->item('pwd_key');
		    	$list['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
		    	$list['data'][$key]['cash_price'] = round($value['cash_price'], 2);
        	}
		    $return = array("code"=>0, "data"=>$list, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "data"=>null, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
    public function selectCompanyData()
	{
		$this->auth_json_admin();
		$userInfo = $this->admin;
    	
        $lists = $this->mall_model->selectCompanyData();
        if($lists !== null){
		    $return = array("code"=>0, "data"=>$lists, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
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
	
    public function timebuy(){
    	$data = array();
    	$data['title'] = '商品限时抢购';
		$data['page_id'] = 'goodslistpage';
		$data['menu'] = 'category';
		$data['env'] = $this->env;
		
		//分享信息
        $url = $this->getPageUrl();
    	$jsapi = $this->jsapi($url);
    	$data = array_merge($data, $jsapi);
    	$data['share_img'] = base_url("media/img/share_img.png");
    	$data['url'] = $url;
    	if($this->systemInfo !== null){
    		$systemInfo = $this->systemInfo;
    	    $data['systemInfo'] = $this->systemInfo;
    	}
    	else{
    		$systemInfo =array();
    		$systemInfo['site_name'] = '';
    		$data['systemInfo'] = '';
    	}
		
		$data['page_css'] = array();
		$data['page_js'] = array(
		    'http://res.wx.qq.com/open/js/jweixin-1.1.0.js'
		);
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/vendor/vue/vue.min.js', //2.0
		    '/media/vendor/vue/axios.min.js',
		    '/media/js/mall/timebuy.js?v='.rand(1,10).'.'.rand(1,10)
	    );
    	
    	$this->load->view('header', $data);
		$this->load->view('mall/timebuy', $data);
		$this->load->view('footer', $data);
    }
    
    public function timebuy_page_data()
	{
		$page = $this->input->post('page');
	    $num = $this->input->post('num');
	    $offset = ($page - 1) * $num;
        $result = $this->goods_model->timebuy_page_data($offset, $num);
        if($result !== null){
        	foreach($result['data'] as  $key=>$value){
        		$imgData = json_decode($value['img_data'], true);
        		$flag = false;
        		foreach($imgData as $imgkey=>$imgitem){
        			if($imgitem['selected']){
        				$result['data'][$key]['face'] = $imgitem['file'];
        				$flag = true;
        			}
        		}
        		if(!$flag){
        			$result['data'][$key]['face'] = $imgData[0]['file'];
        		}
        		$pwd_key = $this->config->item('pwd_key');
		    	$result['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
		    	$result['data'][$key]['cash_price'] = round($value['cash_price'], 2);
		    	$result['data'][$key]['buy_time_seconds'] = strtotime($value['buy_time']);
        	}
		    $return = array("code"=>0,"data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001,"data"=>null, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
	public function share_preview(){
		$this->auth_redirect_member();
		$userInfo = $this->user;
    	$data = array();
    	$share_num = $this->input->post('share_num');
    	$key = $this->input->post('share_goods_key');
    	$pwd_key = $this->config->item('pwd_key');
		$id = $this->decrypt($key, $pwd_key);
    	$goods = $this->goods_model->selectDataById($id);
    	if($goods !== null && $goods['model'] != 1){
    		$goods['key'] = $key;
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
        	
        	$data['uid'] = $userInfo ? $this->encrypt($userInfo['id'], $pwd_key) : '';
    		$order_id = $this->createRandNum();
    		$data['order_id'] = $order_id;
    		$data['num'] = $share_num;
    		$data['goods'] = $goods;
    		$data['title'] = '分享预览';
			$data['page_id'] = 'detailpage';
			$data['menu'] = 'mall';
			$data['env'] = $this->env;
			
			$data['page_css'] = array();
			$data['page_js'] = array();
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    
		    );
			
			$data['systemInfo'] = $this->systemInfo;
			
    		$this->load->view('header', $data);
			$this->load->view('mall/share_preview', $data);
			$this->load->view('footer', $data);
    	}
	    else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">参数错误</div>';
    		exit;
    	}
	}
	
	public function share(){
	    $data = array();
	    $this->checkUser();
	    if($this->isInWechat()){
	    	$wechat = $this->session->wechat;
	    	if(!$wechat){
	    		$url = "Location: /mall/center";
		    	header($url);
		    	return;
	    	}
	    }
	    
    	$id_key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
    	$id = $this->decrypt($id_key, $pwd_key);
    	$share = $this->mall_model->selectShareById($id);
    	if($share !== null){
    		$is_over = 0;
    		if($share['status'] == 1){
    			$is_over = 1;
    		}
    		
    		$is_zero = 1;
    		if($share['rest_num'] <= 0){
    			$is_zero = 0;
    		}
    		
    		$data['is_over'] = $is_over;
    		$data['is_zero'] = $is_zero;
    		$share['key'] = $id_key;
    		$data['share'] = $share;
    		$goods = $this->goods_model->selectDataById($share['goods_id']);
    		$already_buy = 0;
    		$level = 99;
    		$userInfo = $this->session->user;
	    	if($userInfo){
	    		$already_buy = $this->mall_model->getAlreadyBuyNum($goods['id'], $userInfo['id']);
	    		$level = $userInfo['level'];
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
	    	
	    	$url = $this->getPageUrl();
	    	$jsapi = $this->jsapi($url);
	    	$data = array_merge($data, $jsapi);
	    	$data['share_img'] = base_url("backend/uploads/".$goods['face']);

	    	$data['level'] = $level;
	    	$data['already_buy'] = $already_buy;
	    	$skuParam = array();
	    	$goods['key'] = $this->encrypt($goods['id'], $pwd_key);
	    	$goods['options'] = $goods['options'] ? json_decode($goods['options'], true) : array();
	    	if(isset($goods['options']['is_option']) && $goods['options']['is_option'] == 1){
	    		//不包括库存为0的
	    		$skuData = $this->goods_model->selectSkuDataByGidNotZero($goods['id']);
	    		$size = 0;
	    		
	    		if($skuData !== null){
		    		foreach($skuData as $item){
		    			$size = $size + intval($item['total']);
		    			//为防止以后增加属性
		    			$skuParam[$item['attributes']] = array(
		    			   'count' => intval($item['total'])
		    			);
		    		}
	    		}
	    		
	    		$goods['total'] = $size;
	    	}
	    	
	    	$goods['sku'] = $skuParam;
	    	$data['goods'] = $goods;
	    	$data['imglist'] = $imgData;

	    	$data['title'] = '分享商品详情';
			$data['page_id'] = 'detailpage';
			$data['menu'] = 'mall';
			$data['env'] = $this->env;
			$data['page_css'] = array(
			    '/media/vendor/alternate/alternate.css',
			);
			$data['page_js'] = array(
			    '/media/vendor/alternate/alternate.js',
			    '/media/vendor/react/react.js',
			    '/media/vendor/react/react-dom.js',
			    '/media/vendor/react/browser.min.js',
			    'http://res.wx.qq.com/open/js/jweixin-1.1.0.js'
			);
			
			//页面底部最后加载的js
			$data['page_detail_js'] = array(
			    '/media/js/mall/share.js?v='.rand(1,10).'.'.rand(1,10)
			);
			
			$data['systemInfo'] = $this->systemInfo;
	    	$this->load->view('header', $data);
			$this->load->view('mall/share', $data);
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
    
    public function do_share(){
		$this->auth_json_member();
		$userInfo = $this->user;
		
		$this->load->model('member_model');
		$newlist_userInfo = $this->member_model->getDataById($userInfo['id']);
    	$userInfo['exchange_pwd'] = $newlist_userInfo['exchange_pwd'];
    	$userInfo['consume_point'] = $newlist_userInfo['consume_point'];
    	
		$key = trim($this->input->post('key'));
		$num = trim($this->input->post('num'));
		$order_id = trim($this->input->post('order_id'));
		$check_key = trim($this->input->post('check_key'));
		$pwd_key = $this->config->item('pwd_key');
		$id = $this->decrypt($key, $pwd_key);
    	$goods = $this->goods_model->selectDataById($id);
    	if($goods !== null && $goods['model'] != 1){
    		$total_point_price = $num * $goods['point_price'];
    		$total_num = $goods['total'] + $goods['sales_volume'];
    		$total_share_num = $num + $goods['share_num'];
    		if($total_num >= $total_share_num){
    			if($total_point_price <= $userInfo['consume_point']){
					if($order_id && $check_key){
						if($goods['point_price'] > 0 && $goods['share_price'] > 0){
					    	$hash = $this->check($check_key);
						    if(!$userInfo['exchange_pwd']){
						    	$return = array("code"=>10005, "message"=>"请先设置二级密码");
						    }
					    	elseif($userInfo['exchange_pwd'] && $hash == $userInfo['exchange_pwd']){
							    $result_id = $this->mall_model->do_share($goods, $userInfo['id'], $order_id, $num);
							    if($result_id){
							    	$id_key = (string)$result_id;
							    	$insert_key = $this->encrypt($id_key, $pwd_key);
							    	$return = array("code"=>0, "key"=>$insert_key, "message"=>"提交成功！");
							    }
							    else{
							    	$return = array("code"=>10001, "message"=>"提交失败！");
							    }
					    	}
					    	else{
					    		$msg = '二级密码错误';
					    		$return = array("code"=>10002, "message"=>$msg);
					    	}
						}
						else{
							$msg = '积分或分享价格必须大于等于0';
							$return = array("code"=>10003, "message"=>$msg);
						}
					}
					else{
						$msg = '参数错误';
						$return = array("code"=>10004, "message"=>$msg);
					}
    			}
    			else{
    				$msg = '消费积分不足';
				    $return = array("code"=>10005, "message"=>$msg);
    			}
    		}
    		else{
    			$msg = '该商品分享数量已超额';
				$return = array("code"=>10006, "message"=>$msg);
    		}
    	}
    	else{
    		$msg = '产品参数错误';
			$return = array("code"=>10007, "message"=>$msg);
    	}
		
		echo json_encode($return);
		exit();
	}
	
    protected function check($original){
		$string = "goodluck_second";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
    private function checkUser(){
    	log_message('info', '用户确认开始==>分享');
    	if($this->isInWechat()){
    		$this->load->model('weixin_model');
	    	$code = $this->input->get('code');
	    	if(!$code){
	    	    $wechat = $this->session->wechat;
	    		if($wechat){
	    			log_message('info', '第二次进入，带有个人openid信息');
	    			//有session,不执行oauth取用户信息，有openid，URL完整，执行后续代码
	    		}
	    		else{
	    			log_message('info', '第一次进入oauth');
	    			//没有session，oauth授权获取openid
	    			$redirect_uri = $this->getPageUrl();
	    			$state = 'good';
	    			log_message('info', 'oauth授权获取openid，回调URL：'.$redirect_uri);
	    			$oauth_url = $this->weixin_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
	    			//执行网页授权模式，然后跳转到redirect_uri
	    			header("location: ".$oauth_url);
	                die();
	    		}
	    	}
	    	else{
	    	    //oauth后回调原来URL并带上code参数
	    		$oauthInfo = $this->weixin_model->getOauthInfoByCode($code);
	    		if($oauthInfo != null){
	    			log_message('info', 'oauth回调带code');
	    			$openid = $oauthInfo['openid'];
	                $access_token = $oauthInfo['access_token'];
	                
	                //授权模式获取用户信息
                	$userInfo = $this->weixin_model->getUserInfoByOauth($openid, $access_token);
                	if($userInfo != null){
                		log_message('info', '网页授权获取用户信息');
                		unset($userInfo['privilege']);
                		$this->session->wechat = $userInfo;
                	}
                	else{
                		//获取出错，重新oauth获取用户信息
                		$url = $this->getPageUrl();
		                $redirect_uri = $this->updateOpenidToUrl($url, $openid); //去掉code state参数,session 有用户信息
		    			$state = '';
		    			$oauth_url = $this->weixin_model->set_oauth_snsapi_userinfo($redirect_uri, $state);
		    			//执行网页授权模式，然后跳转到redirect_uri
		    			log_message('info', '授权模式获取用户信息出错,改变授权方式');
		    			header("location: ".$oauth_url);
		                die();
                	}
	    		}
	    		else{
	    			log_message('info', '授权出错');
	    			echo $oauthInfo['errmsg'];
	                die();
	    		}
	    	}
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
    
    public function updateOpenidToUrl($url, $openid)
    {
        if (!isset($openid)){
            return $url;
        }
        
        $urlArr = parse_url($url);
        if (isset($urlArr['query'])) {
            $query_old_block_arr = explode("&", $urlArr['query']);
            $query_old_arr = array();
            foreach ($query_old_block_arr as $k => $v) {
                $tmp = explode("=", $v);
                if (count($tmp) == 2){
                    $query_old_arr[$tmp[0]] = $tmp[1];
                }
            }
            if(empty($openid)){
                unset($query_old_arr['openid']);
            }
            else{
                $query_old_arr['openid'] = $openid;
            }
            unset($query_old_arr['code']);
            unset($query_old_arr['state']);
            $query_new_block_arr = array();
            foreach ($query_old_arr as $key => $val) {
                array_push($query_new_block_arr, $key . "=" . $val);
            }
            $urlArr['query'] = implode("&", $query_new_block_arr);
        } 
        else if(!empty($openid)){
            $urlArr['query'] = "openid=" . $openid;
        }
        
        $url_new = $urlArr["scheme"] . "://" . $urlArr["host"] . $urlArr["path"] . "?" . $urlArr["query"];
        if (isset($urlArr['fragment'])){
            $url_new .= "#" . $urlArr['fragment'];
        }

        return $url_new;
    }
    
    public function sharelist(){
    	$this->auth_redirect_member();
    	$data = array();
    	$data['title'] = '分享列表';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'personal';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    '/media/js/mall/sharelist.js?v='.rand(1,10).'.'.rand(1,10)
		);
		$data['systemInfo'] = $this->systemInfo;
	    $this->load->view('header', $data);
		$this->load->view('mall/sharelist', $data);
		$this->load->view('footer', $data);
    }
    
    public function share_data_by_page()
	{
		$this->auth_json_member();
		$userInfo = $this->user;
	    $searchkey = $this->input->post('searchkey');
	    $page = $this->input->post('page');
	    $num = $this->input->post('num');
	    
        $result = $this->mall_model->share_data_by_page($userInfo['id'], $searchkey, $page, $num);
        if($result !== null){
            foreach($result['data'] as  $key=>$value){
        		$imgData = json_decode($value['img_data'], true);
        		$flag = false;
        		foreach($imgData as $imgkey=>$imgitem){
        			if($imgitem['selected']){
        				$result['data'][$key]['face'] = $imgitem['file'];
        				$flag = true;
        			}
        		}
        		if(!$flag){
        			$result['data'][$key]['face'] = $imgData[0]['file'];
        		}
        		$pwd_key = $this->config->item('pwd_key');
		    	$result['data'][$key]['key'] = $this->encrypt($value['id'], $pwd_key);
		    	$result['data'][$key]['share_price'] = round($value['share_price'], 2);
        	}
		    $return = array("code"=>0, "data"=>$result, "message"=>"获取成功！");
		}
		else{
			$return = array("code"=>10001, "message"=>"获取失败！");
		}
    	echo json_encode($return);
		exit();
	}
	
	public function sharedetail(){
		$this->auth_redirect_member();
		$data = array();
		$data['title'] = '分享详情';
		$data['page_id'] = 'personal-page';
		$data['menu'] = 'mall';
		$data['env'] = $this->env;
		
		$data['page_css'] = array();
		$data['page_js'] = array();
		
		//页面底部最后加载的js
		$data['page_detail_js'] = array(
		    
	    );
		$data['systemInfo'] = $this->systemInfo;
		    	
		$id_key = $this->input->get('key');
    	$pwd_key = $this->config->item('pwd_key');
    	$id = $this->decrypt($id_key, $pwd_key);
    	$share = $this->mall_model->selectShareById($id);
    	if($share !== null){
    		$goods = $this->goods_model->selectDataById($share['goods_id']);
    		if($goods !== null){
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
	        	$goods['key'] = $this->encrypt($share['goods_id'], $pwd_key);
	        	$data['goods'] = $goods;
	        	
	        	$salelist = $this->mall_model->selectShareOrderById($share['id']);
	        	$data['salelist'] = $salelist ? $salelist : array();
	        	$used_point = 0;
	        	$geted_commision = 0;
	        	if($salelist !== null){
	        		foreach($salelist as $item){
	        			if($item['status'] == 3 && $item['is_clear_share'] == 1){
	        				$used_point = $used_point + $item['point_price'] * $item['num'];
						    $total_cash_price = $goods['cash_price'] * $item['num'];
							$total_share_price = $item['cash_price'] * $item['num'];
							if($total_cash_price >= $total_share_price){
							    $commision = $total_cash_price - $total_share_price;
							}
							else{
								$commision = $total_share_price - $total_cash_price;
							}
	        				$geted_commision = $geted_commision + $commision;
	        			}
	        		}
	        	}
	        	
	        	$back_point = 0;
	        	if($share['status'] == 1 && $share['rest_num'] > 0){
	        		$back_point = $share['single_point'] * $share['rest_num'];
	        	}
	        	
	        	$share['used_point'] = $used_point;
	        	$share['geted_commision'] = $geted_commision;
	        	$share['back_point'] = $back_point;
	        	$share['share_price'] = round($share['share_price'], 2);
	        	$data['share'] = $share;
		    	
	    		$this->load->view('header', $data);
				$this->load->view('share/sharedetail', $data);
				$this->load->view('footer', $data);
    		}
    		else{
    			echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">商品参数错误</div>';
    		    exit;
    		}
    	}
	    else{
    		echo '<div style="text-align:center; padding-top:30px; font-size:50px; color:red;">分享参数错误</div>';
    		exit;
    	}
	}
}