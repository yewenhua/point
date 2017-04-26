<?php
class Agent_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
    private function mkdir($path, $chmod=0777)
	{
		return is_dir($path) or ($this->mkdir(dirname($path),$chmod) and mkdir($path,$chmod));
	}
	
    /**
     * 删除文件。当文件夹内文件个数不是5时 ...
     * @param $dir
     */
    private function rmdirs($dir)
	{
        //rmdir函数会返回一个状态,用@屏蔽掉输出
        if(file_exists($dir)){
	        $dir_arr = @scandir($dir);
	        $length = is_array($dir_arr) ? count($dir_arr) : 0;
	        if($length > 2 && $length != 4){
		        foreach($dir_arr as $key=>$val){
		            if($val == '.' || $val == '..'){
		            	
		            }
		            else {
		                if(is_dir($dir.'/'.$val))    
		                {                            
		                    if(@rmdir($dir.'/'.$val) == 'true'){
		                    
		                    }    //去掉@您看看                
		                    else{
		                         $this->rmdirs($dir.'/'.$val);    
		                    }                
		                }
		                else{             
		                    @unlink($dir.'/'.$val);
		                }
		            }
		        }
	        }
        }
        else{
        	$this->mkdir($dir);
        }
	}
	
	/**
	 * 设置参数 ...
	 */
    private function get_tg_stream_context_create_opts(){
        return array(
            "http"=>array(
                "method"=>"GET",
                "timeout"=>3
            ),
        );
    }
	
    public function isOpenidExist($openid) {
        $sql = "SELECT * FROM wechat WHERE openid = '".$openid."' AND deleted_at is null";
		$query = $this->db->query($sql);
	    if ($query->num_rows() >= 0){
	    	if($query->num_rows() == 0){
	    		return null;
	    	}
	    	else{
	    		return $query->row_array();
	    	}
		}
		else{
			return null;
		}
	}
	
    public function updateAccesstoken($param) {
		$paramPlatform = array(
		    'refresh_token' => $param['refresh_token'],
		    'access_token' => $param['access_token'],
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		$this->db->where('openid', $param['openid']);
	    $this->db->update('wechat', $paramPlatform);
	
	    //当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
    public function updateWechatInfo($param) {
		$paramPlatform = array(
		    "nickname"=>$param['nickname'],
		    "sex" => $param['sex'],
		    "province" => $param['province'],
	        "city" => $param['city'],
		    "country" => $param['country'],
		    "headimgurl" => $param['headimgurl'],
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		$this->db->where('openid', $param['openid']);
	    $this->db->update('wechat', $paramPlatform);
	
	    //当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function insertWechatInfo($param, $is_subscribe){
	    if($param['from'] == 'timeline'){
    	    $channel = '朋友圈';
    	}
    	elseif($param['from'] == 'groupmessage'){
    	    $channel = '微信群';
    	}
        elseif($param['from'] == 'singlemessage'){
    	    $channel = '好友分享';
    	}
	    elseif($param['from'] == 'self'){
    	    $channel = '主动关注';
    	}
        else{
    	    $channel = '链接';
    	}
    	
	    $param = array(
			"openid"=>$param['openid'],
			"headimgurl"=>$param['headimgurl'],
		    "sex" => $param['sex'],
		    "nickname" => $param['nickname'],
		    "province" => $param['province'],
		    "city" => $param['city'],
		    "country" => $param['country'],
		    "access_token" => $param['access_token'],
	        "refresh_token" => $param['refresh_token'],
		    "code" => $param['key'],
		    "from" => $channel,
	        "is_subscribe" => $is_subscribe
		);
			
		$this->db->insert('wechat', $param);
		if($this->db->affected_rows()){
			$id = $this->db->insert_id();
			$param['id'] = $id;
		    return $param;
		}
		else{
			return null;
		}
	}
	
    public function unsubscribe($openid) {
		$paramPlatform = array(
		    "is_subscribe" => 0,
			'updated_at' => date('Y-m-d H:i:s', time()),
		);
		$this->db->where('openid', $openid);
	    $this->db->update('wechat', $paramPlatform);
	
	    //当执行写入操作（insert,update等）的查询后，显示被影响的行数。
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getUserByCode($code) {
	    $sql = "SELECT * FROM user WHERE binary code = '".$code."'";
		$query = $this->db->query($sql);
	    if ($query->num_rows() >= 0){
	    	if($query->num_rows() == 0){
	    		return null;
	    	}
	    	else{
	    		return $query->row_array();
	    	}
		}
		else{
			return null;
		}
	}
	
	/**
	 * password...
	 */
	public function password($original){
		$string = "goodluck";
		$newPwd = md5($string.$original.$string);
		return $newPwd;
	}
	
    /**
     *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
     * @param int $length 需要生成的字符串的长度
     * @return string 包含 大小写英文字母 和 数字 的随机字符串
     */
    public function random_str($length)
    {
        //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }

        return $str;
    }
	
	public function updateUserInfo($userid, $openid, $code) {
		$data = array(
			'userid' => $userid,
		    'code' => $code,
		    'updated_at' => date('Y-m-d H:i:s', time())
		);
		$this->db->where('openid', $openid);
		$this->db->update('wechat', $data);
		if($this->db->affected_rows()){
			return true;
		}
		else{
			return false;
		}
	}
	
    /**
     * 创建海报 ...
     * @param $user_id
     */
    public function createHaibao($memberInfo){
    	$personalDir = BASEPATH.'../wechat/userTgCache'.DIRECTORY_SEPARATOR.$memberInfo['userid'];
    	$this->rmdirs($personalDir); 
    	$haibaoFilename= 'tg.png';
    	$returnFilePath = $personalDir.DIRECTORY_SEPARATOR.$haibaoFilename;
        if(!file_exists($returnFilePath)){
        	//生成的合成二维码并返回路径
        	$start_time = time();
        	$qrcodePath = $this->generateQrcode($memberInfo);
        	$haibaoTemplatePath = BASEPATH.'../wechat/userTgCache/tpl.png';
        	$qrlogo_context = stream_context_create($this->get_tg_stream_context_create_opts());
            $haibao_context = stream_context_create($this->get_tg_stream_context_create_opts());

            $qrlogo_str = file_get_contents($qrcodePath, false, $qrlogo_context);
            $haibao_str = file_get_contents($haibaoTemplatePath, false, $haibao_context);

            //从字符串中的图像流新建一图像
            $qrlogo_img = imagecreatefromstring($qrlogo_str);
            $haibao_img = imagecreatefromstring($haibao_str);

            if (imageistruecolor($haibao_img)) {
            	//将真彩色图像转换为调色板图像
            	imagetruecolortopalette($haibao_img, false, 65535);
            }
            
            //合并海报和带头像的二维码
            imagecopyresampled($haibao_img, $qrlogo_img, 226, 702, 0, 0, 300, 300, 430, 430);
            //430为原二维码的高度和宽度  300为要生成的图像中的二维码高度和宽度  226和702为生成的图像中的二维码的坐标 0为载入图片要载入的区域坐标


			$name = $memberInfo['nickname'];
            $info = "祝您幸福平安！ ";
            
            //设定图像的混色模式并启用
            imagealphablending($haibao_img, true);
            
            //为一幅图像分配颜色
            $black = imagecolorallocate($haibao_img, 0, 0, 0);
            $font_file = BASEPATH.'../wechat/userTgCache/wryh.ttf';
            
            //使用 FreeType 2 字体将文本写入图像
            imagefttext($haibao_img, 26, 0, 180, 75, $black, $font_file, $name);
            //imagefttext($haibao_img, 20, 0, 280, 260, $white, $font_file, $info);

            //建立 PNG 图型
            imagepng($haibao_img, $returnFilePath);

            //销毁图像
            imagedestroy($haibao_img);
            imagedestroy($qrlogo_img);
            log_message('info', "生成推广海报，耗时：".(time() - $start_time));
        }

        return $returnFilePath;
    }
    
    /**
     * 将头像与二维码合并 ...
     */
    private function generateQrcode ($memberInfo){
    	$personalDir = BASEPATH.'../wechat/userTgCache'.DIRECTORY_SEPARATOR.$memberInfo['userid'];
    	$combine_filename = 'QRCODE.png';
    	$returnFilePath = $personalDir.DIRECTORY_SEPARATOR.$combine_filename;
    	if(!file_exists($returnFilePath)){
    		$logo_str_time = time();
    		$head_img_path = $memberInfo['headimgurl'];
    		$scene_id = 'userTg-'.$memberInfo['userid'];
    		$type = 2;
    		$qrcode_path = $this->createQrcode($type, $scene_id);
    		log_message('info', 'qrcode_path:'.var_export($qrcode_path, true));
    		
    		//创建并返回一个文本数据流
    		$qr_context = stream_context_create($this->get_tg_stream_context_create_opts());
            $head_img_context = stream_context_create($this->get_tg_stream_context_create_opts());

            $qrcode_str = file_get_contents($qrcode_path, false, $qr_context);
            $logo_str = file_get_contents($head_img_path, false, $head_img_context );
            
            if(!empty($logo_str)){
            	log_message('info', "远程获取微信头像，耗时：".(time() - $logo_str_time));
            	
            	//从字符串中的图像流新建一图像
                $qrcode_img = imagecreatefromstring($qrcode_str);
                $logo_img = imagecreatefromstring($logo_str);

                if (imageistruecolor($logo_img)) {
                	//将真彩色图像转换为调色板图像
                	imagetruecolortopalette($logo_img, false, 65535);
                }

                //配置拼合参数
                $qrcode_width = imagesx($qrcode_img);//取得二维码图片宽度
                $qrcode_height = imagesy($qrcode_img);//取得二维码图片高度
                $logo_width = imagesx($logo_img);//取得logo图片宽度
                $logo_height = imagesy($logo_img);//取得logo图片高度
                $logo_qrcode_width = $qrcode_width / 3;
                $scale = $logo_width/$logo_qrcode_width;
                $logo_qrcode_height = $logo_height/$scale;
                $from_width = ($qrcode_width - $logo_qrcode_width) / 2;
                
                //圆角效果
                $this->create_radius($logo_img, $logo_width, $logo_height);
                
                //重新组合图片并调整大小
                imagecopyresampled($qrcode_img, $logo_img, $from_width, $from_width, 0, 0, $logo_qrcode_width, $logo_qrcode_height, $logo_width, $logo_height);
                
                //保存图像
                imagepng($qrcode_img, $returnFilePath);

                //销毁图像
                imagedestroy($qrcode_img);
                imagedestroy($logo_img);
                log_message('info', "头像与二维码合并，耗时：".(time() - $logo_str_time));
            }
            else{
            	log_message('info', "远程获取微信头像失败");
                $qrcode_img = imagecreatefromstring($qrcode_str);
                imagepng($qrcode_img, $returnFilePath);
            }
    	}
    	return $returnFilePath;
    }
    
   /**
	* 创建二维码...
	*/
    private function createQrcode($type, $scene_id) {
    	log_message('info', '创建二维码，场景值ID:'.var_export($scene_id, true));
    	$qrcode_img_url = null;
    	$access_token = $this->weixin_model->getAccessToken();
        if($access_token !== null){
    		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
    		if($type == 1){
    			//临时二维码
    			$param = <<<PARAM
    			    {
	    			    "expire_seconds": 604800,
	    			    "action_name": "QR_SCENE", 
	    			    "action_info": 
	    			        {"scene": 
	    			           {"scene_id": "{$scene_id}"}
    		                }
    		        }
PARAM;
    		}
    		elseif($type == 2){
    			//永久二维码
    			$param = <<<PARAM
    			    {
	    			    "action_name": "QR_LIMIT_STR_SCENE", 
	    			    "action_info": 
	    			        {"scene": 
	    			           {"scene_str": "{$scene_id}"}
    		                }
    		        }
PARAM;
    		}
    		
    		$result = $this->curl_post($url, $param);
    		log_message('info', '创建二维码，结果：'.var_export($result, true));
    		if(isset($result->errcode) && $result->errcode != 0){
	        	$qrcode_img_url = null;
	        }
	        else{
	        	$qrcode_img_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($result->ticket);
	        }

        }
        else{
        	log_message('info', 'createQrcode获取token出错');
        }
    	return $qrcode_img_url;
    }
    
    /**
     * 新增临时素材
     * @param $type image voice video thumb
     */
    public function uploadMediaFile($media_path, $type)
    {
    	$access_token = $this->weixin_model->getAccessToken();
        if($access_token !== null){
            $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$access_token}&type=image";
	        $curl = curl_init ();
	        curl_setopt ( $curl, CURLOPT_URL, $url );
	        curl_setopt ( $curl, CURLOPT_POST, 1 );
	        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	        curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
	        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
	        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
	
	        if (class_exists('\CURLFile')) {
	            $curlfile = curl_file_create($media_path);
	        }
	        else{
	        	$curlfile = '@' . realpath($media_path);
	        }
	        $postdata = array('media' => $curlfile);
	        
	        curl_setopt($curl, CURLOPT_POST,1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	        curl_setopt($curl, CURLOPT_INFILESIZE,filesize($media_path));
	        $return = curl_exec ( $curl );
	
	        if(curl_errno($curl)==0){
	            curl_close($curl);
	            $result = json_decode($return, true);
	            if(isset($result['errcode']) && $result['errcode'] != 0){
	                return null;
	            }
	            else{
	                return $result['media_id'];
	            }
	        }else {
	            return null;
	        }
        }
        else{
        	log_message('info', 'uploadMediaFile获取token出错');
        	return null;
        }
    }
    
    /**
     * 产生一个弧角图片  
     * @param $radius 弧角图片的大小  
     */  
    private function create_corner_img($radius) {  
        $img        = imagecreatetruecolor($radius, $radius);  
        $bgcolor    = imagecolorallocate($img, 255, 255, 255);  
        $fgcolor    = imagecolorallocate($img, 0, 0, 0);  
        imagefill($img, 0, 0, $bgcolor);  
        
        // $radius,$radius：以图像的右下角开始画弧  ，$radius*2, $radius*2：已宽度、高度画弧  ，180, 270：指定了角度的起始和结束点  ，fgcolor：指定颜色  
        imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);  
        
        // 设置颜色为透明  
        imagecolortransparent($img, $fgcolor);  
        return $img;  
    } 

    /**
     * 利用弧角图片生成圆角图片 ...
     * @param $logo_img
     * @param $logo_width
     * @param $logo_height
     */
    private function create_radius($logo_img, $logo_width, $logo_height){
    	// 图片圆角处理  
        $radius     = 20;  
        // lt(左上角)  
        $lt_corner  = $this->create_corner_img($radius);  
        imagecopymerge($logo_img, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);  
        // lb(左下角)  
        $lb_corner  = imagerotate($lt_corner, 90, 0);  
        imagecopymerge($logo_img, $lb_corner, 0, $logo_height - $radius, 0, 0, $radius, $radius, 100);  
        // rb(右上角)  
        $rb_corner  = imagerotate($lt_corner, 180, 0);  
        imagecopymerge($logo_img, $rb_corner, $logo_width - $radius, $logo_height - $radius, 0, 0, $radius, $radius, 100);  
        // rt(右下角)  
        $rt_corner  = imagerotate($lt_corner, 270, 0);  
        imagecopymerge($logo_img, $rt_corner, $logo_width - $radius, 0, 0, 0, $radius, $radius, 100);
    }
    
   /**
	* curl post方法...
	*/
	public function curl_post($url, $data){ // 模拟提交数据函数
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	    $tmpInfo = curl_exec($curl); // 执行操作
	    if (curl_errno($curl)) {
	        //echo 'Errno'.curl_error($curl);//捕抓异常
	        curl_close($curl); // 关闭CURL会话
	        return null;
	    }
	    else{
		    curl_close($curl); // 关闭CURL会话
		    $result = json_decode($tmpInfo);
		    if($result && isset($result->errcode) && $result->errcode == 40001){
		        $fileName = dirname(dirname(dirname(__FILE__)))."/application/models/access_token.json";
				if(file_exists($fileName)){
					log_message('info', '有40001错误值，accesstoken清空');
		            file_put_contents($fileName, '');
		            $this->curl_post($url, $data);
		        }
		    }
		    return $result; // 返回数据
	    }
	}
}