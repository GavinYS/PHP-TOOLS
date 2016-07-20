# PHP-TOOLS
常用的PHP小工具包
## weixin
### functions.php
	asckey_sort(array $data);将数组中的键值按ASCII字典序排序,返回排序好的二维数组
	get_ip();获取用户真实IP
### JsApi.class.php
	$js_api = new JsApi($appid, $secretid);
	//获取access_token
	$access_token = $js_api->getAccessToken();
	//获取js_api_ticket
	$ticket = $js_api->getJsApiTicket();
	//获取签名,需要POST数据timeStamp(当前时间戳),nonceStr(随机字符串),url(页面URL)
	$sign = $js_api->getSign();
### AuthApi.class.php
	内有snsapi_base仅获取用户openid方法
### PayApi.class.php
	微信支付步骤:
	1.调用unifyOrder,统一下单,给前端返回prepay_id
	2.前端调用微信JS进入支付过程
	3.微信服务器接收到了支付成功的提示,回调服务器URL
	4.服务器调用queryOrder,查询微信服务器订单状态,完成逻辑功能

## page
### Page.class.php
	$param = array(
		'page' => $page,//当前页
		'limit' => $limit,//取出的条数
		'count' => $count//总条数
	);
	//获取页码数据
	$page = new Page($param);
	$data = $page->pagination();
	//直接输出bootstrap样式的翻页样式,需要URL重写/1代表第一页
	$page->bootstrapMode($url);