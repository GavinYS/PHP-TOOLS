# PHP-TOOLS
常用的PHP小工具包
## weixin
### functions.php
	asckey_sort(array $data);将数组中的键值按ASCII字典序排序,返回排序好的二维数组
### JsApi.class.php
	$js_api = new JsApi($appid, $secretid);
	//获取access_token
	$access_token = $js_api->getAccessToken();
	//获取js_api_ticket
	$ticket = $js_api->getJsApiTicket();
	//获取签名,需要POST数据timeStamp(当前时间戳),nonceStr(随机字符串),url(页面URL)
	$sign = $js_api->getSign();