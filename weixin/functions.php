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