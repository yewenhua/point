<?php
class Logistic_model extends CI_Model {
	
	private $appid  = '1273711';
	private $appkey = '339adffb-15c9-4486-8fe4-26aa6e9640df';
	private $url = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * @brief 获取物流轨迹线路
	 * @param $ShipperCode string 物流公司代号
	 * @param $LogisticCode string 物流单号
	 * @return string array 轨迹数据
	 */
	public function query($ShipperCode, $LogisticCode)
	{
		$params = array(
			'ShipperCode' => $ShipperCode,
			'LogisticCode'=> $LogisticCode,
		);

		$sendData = json_encode($params);
		$curlData = array(
			'RequestData' => $sendData,
			'EBusinessID' => $this->appid,
			'RequestType' => '1002',
			'DataType'    => 2,
			'DataSign'    => base64_encode(md5($sendData.$this->appkey)),
		);

		$result_json = $this->curlSend($this->url, $curlData);
		$result_obj = json_decode($result_json);
		return $result_obj;
	}
	
   /**
	 * @brief 单号识别
	 * @param $LogisticCode string 物流单号
	 * @return string array 快递公司数据
	 */
	public function code($LogisticCode)
	{
		$params = array(
			'LogisticCode'=> $LogisticCode
		);

		$sendData = json_encode($params);
		$curlData = array(
			'RequestData' => $sendData,
			'EBusinessID' => $this->appid,
			'RequestType' => '2002',
			'DataType'    => 2,
			'DataSign'    => base64_encode(md5($sendData.$this->appkey)),
		);

		$result_json = $this->curlSend($this->url, $curlData);
		$result_obj = json_decode($result_json);
		return $result_obj;
	}
	
	/**
	 * @brief CURL模拟提交数据
	 * @param $url string 提交的url
	 * @param $data array 要发送的数据
	 * @return mixed 返回的数据
	 */
	private function curlSend($url,$data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return curl_exec($ch);
	}
}