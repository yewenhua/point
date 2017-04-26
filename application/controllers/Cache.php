<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cache extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
	/**
	 * memcache ...
	 */
    public function memcache()
	{
		//如果 memcached 在服务器环境下不可用，将降级到基于文件的缓存
		$this->load->driver('cache',  array('adapter' => 'memcached', 'backup' => 'file') ); 
		//$this->load->driver('cache');
		
		if ($this->cache->memcached->is_supported()){
			echo "OK";
			$this->cache->save('foo', 'barss', 10); //10s
	        $value = $this->cache->get('foo');
	        echo $value;
		}
		else{
			echo "FAIL";
		}
        exit;
	}
	
	/**
	 * qrcode ...
	 */
    public function qrcode()
	{
		include_once dirname(__FILE__).'/phpqrcode.php';
		$url = 'http://www.ziyivip.com/member/regist';
		$errorCorrectionLevel = 'H';
        $matrixPointSize = 10;
        $qr_filename = dirname(dirname(dirname(__FILE__))).'/media/qrcode/regist.png';
        QRcode::png($url, $qr_filename, $errorCorrectionLevel, $matrixPointSize, 2);
	}
	
	public function phpinfo(){
		phpinfo();
	}
	
    public function todo(){
    	$this->load->model('program_model');
		$this->program_model->todo();
	}
	
    public function update(){
    	$success = 0;
    	$this->load->model('goods_model');
    	$data = $this->goods_model->getUpdateTimeNullData();
    	if($data !== null){
    		foreach($data as $item){
    		   $res = $this->goods_model->modifyUpdateTime($item);
    		   if($res){
    		   	   $success++;
    		   }
    		}
    	}
    	echo $success;
    	exit;
	}
}