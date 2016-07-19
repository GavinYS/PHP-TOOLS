<?php
/**
 *	@author gavinys@gavinys.com
 *	@since 1.0
 *	@date 2016.07.19
 *
 */
require_once 'functions.php';

class PayApi {

	/**
	 *	微信公众号的appid
	 *	@var string
	 */
	private $appid;

	/**
	 *	微信公众号的secretid
	 *	@var string
	 */
	private $secretid;

	/**
	 *	微信公众号商户id
	 *	@var string
	 */
	private $mch_id;

	/**
	 *	微信公众号商户key
	 *	@var string
	 */
	private $key;

	/**
	 *	构造函数
	 *	@param string appid
	 *	@param string secretid
	 *	@param string mch_id
	 *	@param string key
	 */
	public function __construct($appid, $secretid, $mch_id, $key)
	{
		$this->appid = $appid;
		$this->secretid = $secretid;
		$this->mch_id = $mch_id;
		$this->key = $key;
	}

	/**	
	 *	生成随机字符串接口
	 * 
	 *	@return string
	 */
	protected function createNonceString()
	{
		$str = substr(microtime() , 0 , 6);
		$nonce_str = md5($str);
		return $nonce_str;
	}

	/**
	 *  生成签名接口
	 *  @param array 签名的相关数据
	 *
	 *  @return string
	 */
	protected function createSign($sign_data = array())
	{
		//读取
		$this->load->helper('pay');
		$sign_data = asckey_sort($sign_data);
		//构成stringA
		$stringA = '';
		foreach($sign_data as $key=>$value){
			if($key != "sign" && $value != "" && !is_array($value)){
				$stringA .= $key . "=" . $value . "&";
			}
		}
		$stringSignTemp = $stringA.'key='.$this->key;
		$sign = strtoupper(md5($stringSignTemp));
		return $sign;
	}

	/**	
	 *	根据二维数组生成XML字符串
	 *	@param array
	 *
	 *  return string xml
	 */
	protected function createXML($array = array())
	{
		$xml = '<xml>';
		foreach($array as $key => $value){
			$xml .= "<{$key}>{$value}</{$key}>";
		}
		$xml .= '</xml>';
		return $xml;
	}

	/**
	 *	发送XML方法
	 *  @param string urlxml
	 *	@param string xml
	 *
	 *	@return string
	 */
	protected function postXML($url , $xml)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS , $xml);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 *  查询微信服务器的订单信息,微信支付时,微信支付后须调用
	 *	@param string orderid
	 *
	 *  @return array
	 */
	public function queryOrder($orderid = '')
	{
		//读取
		$this->load->model('Order_model');
		$this->load->helper('pay');
		$url = 'https://api.mch.weixin.qq.com/pay/orderquery';
		//查询数据库订单
		$order = $this->Order_model->get_order_by_orderid($orderid);
		$sign_data = array(
			'appid' => $this->appid,
			'mch_id' => $this->mch_id,
			'out_trade_no' => $orderid,
			'nonce_str' => $this->createNonceString()
		);
		$sign = $this->createSign($sign_data);
		$sign_data['sign'] = $sign;
		//生成XML字符串
		$xml_string = $this->createXML($sign_data);
		$response = $this->postXML($url , $xml_string);
		$response = match_queryorder_response($response);
		return $response;
	}

	/**
	 *	统一下单,微信支付前需要向微信服务器统一下单,获取唯一的id以完成下面的支付步骤
	 *	@param array 订单信息
	 *
	 *	@return string
	 */
	public function unifyOrder($order)
	{
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$device_info = 'WEB';//PC网页或公众号内支付须传WEB
		$nonce_str = $this->createNonceString();
		//TODO 可根据需要修改
		$body = $order['body'];
		$detail = $order['detail'];
		$attach = $order['attach'];
		$out_trade_no = $order['orderid'];
		//END
		$fee_type = 'CNY';//人民币
		$total_fee = 2000;//支付价格,单位为分
		$spbill_create_ip = get_ip();//客户IP
		$time_start = date('YmdHis' , time());
		$time_expire = date('YmdHis' , time()+1800);
		$notify_url = 'url';//回调URL
		$trade_type = 'JSAPI';
		$openid = $_SESSION['openid'];//根据AuthApi.class.php获取
		$sign_data = array(
			'appid' => $this->appid,
			'mch_id' => $this->mch_id,
			'device_info' => $device_info,
			'nonce_str' => $nonce_str,
			'body' => $body,
			'detail' => $detail,
			'attach' => $attach,
			'out_trade_no' => $out_trade_no,
			'fee_type' => $fee_type,
			'total_fee' => $total_fee,
			'spbill_create_ip' => $spbill_create_ip,
			'time_start' => $time_start,
			'time_expire' => $time_expire,
			'notify_url' => $notify_url,
			'trade_type' => $trade_type,
			'openid' => $openid
		);
		//生成签名
		$sign = $this->createSign($sign_data);
		$sign_data['sign'] = $sign;
		//生成XML字符串
		$xml_string = $this->createXML($sign_data);
		$response = $this->postXML($url , $xml_string);
		$match = match_unifyorder_response($response);
		if($match){
			return $match;
		} else{
			return false;
		}
	}

	/**	
	 *	给微信服务器提供的回调接口,支付完成后微信服务器将调用此接口,须查询订单状态以完成本服务器的逻辑功能
	 *
	 */
	public function notif()
	{
		//读取
		$getXML = file_get_contents("php://input");
		$getData = match_notif_response($getXML);
		if($getData['code'] == 'SUCCESS' && $getData['orderid']){
			$orderid = $getData['orderid'];
			//查询系统订单
			$order = 'order';
			if(!$order){
				echo 'fail';
			}
			//查询微信接口订单
			$result = $this->queryOrder($orderid);
			if($result['state'] == 'SUCCESS'){
				//更改系统订单状态
				$sys_result = 'change'
				if($sys_result)
					echo 'SUCCESS';
				else
					echo 'FAIL';
			} else{
				echo $result['state'];
			}
		} else{
			echo $getData['code'];
		}
	}
}