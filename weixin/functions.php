<?php
/**
 *	@author gavinys@gavinys.com
 *	@since 1.0
 *	@date 2016.07.19
 *
 */


/**
 *	将数组中的键值按ASCII字典序排序,返回排序好的二维数组
 *	@param array
 *	
 *	@return array
 */
function asckey_sort($array)
{
	$key_array = array();
	$value_array = array();
	$i = 0;
	foreach($array as $key=>$value){
		$key_array[$i] = $key;
		$value_array[$i] = $value;
		$i++;
	}
	for($i = 0 ; $i < count($key_array) ; $i++){
		for($j = 0 ; $j < count($key_array)-1 ; $j++){
			if(asc_compare($key_array[$j] , $key_array[$j+1])){
				$temp = $key_array[$j];
				$key_array[$j] = $key_array[$j+1];
				$key_array[$j+1] = $temp;
				$temp = $value_array[$j];
				$value_array[$j] = $value_array[$j+1];
				$value_array[$j+1] = $temp;
			}
		}
	}
	for($i = 0 ; $i < count($key_array) ; $i++){
		$return_array[$key_array[$i]] = $value_array[$i];
	}
	return $return_array;
}


/**
 *	比较两个字符串ASC字典值,若a>=b return1,a<b return0
 *	@param string a
 *	@param string b
 *
 *	@return int 
 */
function asc_compare($a = '' , $b = '')
{
	$len = strlen($a) > strlen($b) ? strlen($b) : strlen($a);
	for($i = 0 ; $i < $len ; $i++){
		if(ord($a[$i]) > ord($b[$i])){
			return 1;
		}
		elseif(ord($a[$i]) < ord($b[$i]))
			return 0;
		else
			continue;
	}
	if($len == strlen($b) && $len != strlen($a))
		return 1;
	else
		return 0;
}

/**
 *	正则表达抓取查询订单返回XML的数据
 *	@param string
 *
 *	@return array
 */
function match_queryorder_response($xml)
{
	$pattern = '%<return_msg><!\[CDATA\[(.*?)</return_msg>%si';
	preg_match_all($pattern, $xml, $msg);
	if(isset($msg[1][0])){
		$temp_msg = '';
		for($i=0;$i<strlen($msg[1][0]);$i++){
			if($msg[1][0][$i] != ']'){
				$temp_msg .= $msg[1][0][$i];
			} else{
				break;
			}
		}
	}
	$pattern = '%<trade_state><!\[CDATA\[(.*?)</trade_state>%si';
	preg_match($pattern, $xml , $state);
	if(isset($state[1])){
		$temp_state = '';
		for($i=0;$i<strlen($state[1]);$i++){
			if($state[1][$i] != ']'){
				$temp_state .= $state[1][$i];
			} else{
				break;
			}
		}
	}
	$array = array('msg' => $temp_msg , 'state' => $temp_state);
	return $array;
}

/** 
 *	正则表达抓取回调接口POST的XML的数据 
 *	@param string xml
 *
 *	@return array
 */
function match_notif_response($xml)
{
	$pattern = '%<return_code><!\[CDATA\[(.*?)</return_code>%si';
	preg_match_all($pattern, $xml, $code);
	if(isset($code[1][0])){
		$temp_code = '';
		for($i=0;$i<strlen($code[1][0]);$i++){
			if($code[1][0][$i] != ']'){
				$temp_code .= $code[1][0][$i];
			} else{
				break;
			}
		}
	}
	$pattern = '%<out_trade_no><!\[CDATA\[(.*?)</out_trade_no>%si';
	preg_match($pattern, $xml , $orderid);
	if(isset($orderid[1])){
		$temp_orderid = '';
		for($i=0;$i<strlen($orderid[1]);$i++){
			if($orderid[1][$i] != ']'){
				$temp_orderid .= $orderid[1][$i];
			} else{
				break;
			}
		}
	}
	$array = array('code' => $temp_code , 'orderid' => $temp_orderid);
	return $array;
}

/**
 *	获取用户真实IP
 *
 *	@return string
 */
function get_ip()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}