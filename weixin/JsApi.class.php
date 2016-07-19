<?php
require_once 'functions.php';
/**
 *	@author gavinys@gavinys.com
 *	@since 1.0
 *	@date 2016.07.19
 *
 */


class JsApi {

	/**
	 *	微信公众号的appid
	 *	@var string
	 *
	 */
	private $appid = '';

	/**
	 *	微信公众号的secretid
	 *	@var string
	 *
	 */
	private $secretid = '';

	/**
	 *	构造方法,传递微信公众号的appid与secretid
	 *	@param string appid
	 *	@param string secretid
	 *
	 */
	public function __construct($appid, $secretid)
	{
		$this->appid = $appid;
		$this->secretid = $secretid;
	}

	/**
	 *	CURL公用方法
	 *	@param string url
	 *
	 *	@return string output
	 */
	private function curlMethod($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//信任任何证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//检查证书中是否设置域名
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	/**
	 *	保存access_token
	 *
	 *
	 */
	private function saveAccessToken($data)
	{
		//TODO insert into wx_access_token value(data)
		return 1;
	}

	/**
	 *	获取上一个access_token,请根据数据结构自行修改
	 *
	 */
	private function getLastAccessToken()
	{
		//TODO select * from wx_access_token limit 1 order by id DESC
		return 0;
	}

	/**
	 *	获取Access_Token方法,微信公众号后台规定7200秒刷新一次,为保证易用性,此方法5000秒重新获取一个Access_Token
	 *	
	 */
	public function getAccessToken()
	{
		//从数据库中获取上一次的access_token,并判断是否过期,过期则重新获取一个
		$last = $this->getLastAccessToken();
		if($last && (time() - $last['create_time']) < 5000) {
			return $last['access_token'];
		} else {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secretid}";
			$json = $this->curlMethod($url);
			//尝试获取access_token
			try {
				$json = json_decode($json);
				$access_token = $json->access_token;
				//执行插入数据库操作
				$this->saveAccessToken($access_token);
				return $access_token;
			} catch(Exception $e) {
				print_r($json);
				exit(0);
			}
		}
	}

	/**
	 *	保存JsApiTicket
	 *
	 *
	 */
	private function saveJsApiTicket($data)
	{
		//TODO insert into wx_js_api_ticket value(data)
		return 1;
	}

	/**
	 *	获取上一个JsApiTicket,请根据数据结构自行修改
	 *
	 *
	 */
	private function getLastJsApiTicket()
	{
		//TODO select * from wx_js_api_ticket limit 1 order by id DESC
		return 0;
	}

	/**
	 *	获取JsApiTicket方法,微信公众号后台规定7200秒刷新一次,为保证易用性,此方法为5000秒重新获取一个JsApiTicket
	 *
	 *
	 */
	public function getJsApiTicket()
	{
		//从数据库中获取上一个JsApiTicket,并判断是否过去,过期则重新获取一个
		$last = $this->getLastJsApiTicket();
		if($last && (time() - $last['create_time']) < 5000) {
			return $last['js_api_ticket'];
		} else {
			$access_token = $this->getAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
			$json = $this->curlMethod($url);
			//尝试获取JsApiTicket
			try {
				$json = json_decode($json);
				$ticket = $json->ticket;
				//执行插入数据库操作
				$this->saveJsApiTicket($ticket);
				return $ticket;
			} catch(Exception $e) {
				print_r($json);
				exit(0);
			}
		}
	}

	/**
	 *	获取签名所需要的数据
	 *
	 */
	private function getSignPost()
	{
		$post_data = array();
		if(isset($_POST['timeStamp'])) {
			$post_data['timeStamp'] = $_POST['timeStamp'];
		} else {
			echo '缺少时间戳参数';
			exit(0);
		}
		if(isset($_POST['nonceStr'])) {
			$post_data['nonceStr'] = $_POST['nonceStr'];
		} else {
			echo '缺少随机字符串';
			exit(0);
		}
		if(isset($_POST['url'])) {
			$post_data['url'] = $_POST['url'];
		} else {
			echo '缺少当前页面的URL';
			exit(0);
		}
		return $post_data;
	}

	/**
	 *	生成签名
	 *	@param array
	 *
	 *	@return string
	 */
	private function createSign($sign_data)
	{
		$sign_data['jsapi_ticket'] = $this->getJsApiTicket();
		$sign_data = asckey_sort($sign_data);
		//构成stringA
		$stringA = '';
		foreach($sign_data as $key=>$value){
			if($key != "sign" && $value != "" && !is_array($value)){
				$stringA .= strtolower($key) . "=" . $value . "&";
			}
		}
		$stringSignTemp = substr($stringA , 0 , -1);
		$sign = strtolower(sha1($stringSignTemp));
		return $sign;
	}

	/**
	 *	获取签名
	 *
	 *
	 */
	public function getSign()
	{
		$post_data = $this->getSignPost();
		$sign = $this->createSign($post_data);
		return $sign;
	}
}