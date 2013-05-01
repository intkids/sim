<?php
/**
 * 常用函数库
 * @author 雨中歌者 http://weibo.com/esinger (新浪微博)
 * @link http://blog.csdn.net/esinger (技术博客) http://www.simphp.com (个人网站)
 * @since 2013/04/10
 */

/**
 * 函数命名规范
 * 1.判断类函数（is函数）：以"is_"开头，如is_var()、is_empty()。
 * 2.获取类函数（get函数）：以"get_"开头，如get_ip()、get_url()。
 * 3.设置类函数（set函数）：以"set_"开头，如set_name()、set_age()。
 * 4.动作类函数：动词 + "_" + 名词。
 * 5.其它函数命名原则，简洁、表意。
 */

/**
 * 是否合法变量名
 * 规定合法变量名为：字母或下划线开头，后跟字母、数字、下划线、短杠。
 * 仅限于字母、数字、下划线和短杠，更不能使用汉字等非ASCII字符。
 *
 * @param string $var
 * @return boolean
 */
function is_var($var) {
	return !!preg_match('/^[a-z_][a-z0-9-_]*$/i', $var);
}

/**
 * 是否非空字符串
 *
 * @param string $string
 * @return boolean
 */
function is_real_string($string) {
	return is_string($string) && strlen($string) > 0;
}

/**
 * 是否非空
 * 和PHP原生函数的区别在于
 * 1.empty()认为0和"0"为空，返回TRUE
 * 2.本函数认为非空，返回FALSE
 *
 * @param mixed $var 待检测变量
 * @return boolean 为空返回TRUE，否则返回FALSE
 */
function is_empty($var) {
	return empty($var) && $var !== 0 && $var !== "0";
}

/**
 * 创建目录
 * 1.支持多层目录的创建（>PHP5）
 * 2.自动识别目录是否存在
 *
 * @param string $pathname 目录名
 * @param int $mode 模式，默认为0777
 * @return string 返回规范化的绝对路径名
 */
function make_dir($pathname, $mode = null) {
	is_dir($pathname) or mkdir($pathname, $mode, true);
	return realpath($pathname);
}

/**
 * 递归列出目录下的所有目录和文件
 *
 * @param string $dirname
 * @return array 不是目录或目录打开失败返回空数组
 */
function list_dir($dirname) {
	$ret = array();
	if (is_dir($dirname)) {
		if (($dh = @opendir($dirname)) !== false) {
			while (false !== ($file = readdir($dh))) {
				if ($file != "." && $file != "..") {
					$path = $dirname . '/' . $file;
					is_dir($path) ? $ret[$file] = list_dir($path) : $ret[] = $file;
				}
			}
			closedir($dh);
		}
	}
	return $ret;
}

/**
 * 递归删除目录及所有子目录和文件
 * 改进PHP原生函数rmdir()
 * 1.判断目录是否存在，存在进行删除。
 * 2.递归删除目录及止内所有文件和文件夹
 *
 * @param string $dirname
 */
function del_dir($dirname) {
	if (is_dir($dirname)) {
		if (($dh = @opendir($dirname)) !== false) {
			while (false !== ($file = readdir($dh))) {
				if ($file != "." && $file != "..") {
					$path = $dirname . '/' . $file;
					is_dir($path) ? del_dir($path) : @unlink($path);
				}
			}
			closedir($dh);
		}
		rmdir($dirname);
	}
}

/**
 * 字符串文件操作（读、写、删）
 * 1.$data为NULL：读取文件，返回string
 * 2.$data为FALSE：删除文件，返回boolean
 * 3.$data为string：写入文件，返回number
 *
 * @param string|integer $filename
 * @param mixed|false|null $data
 * @return string boolean number
 */
function strfile($filename, $data = null) {
	if (is_scalar($filename)) {
		if (is_null($data)) {
			return is_file($filename) ? @file_get_contents($filename) : null;
		} elseif (false === $data) {
			return is_file($filename) ? unlink($filename) : false;
		} else {
			$dirname = dirname($filename);
			is_dir($dirname) or mkdir($dirname, 0777, true);
			return file_put_contents($filename, $data);
		}
	}
	return false;
}

/**
 * 数组配置文件操作（读、写、删）
 * 1.获取返回数组式文件：$filename为string，$array为null。返回array，不存在或不是返回数组式文件，返回空数组。
 * 2.生成返回数组式文件：$filename为string，$array为array。返回array。
 * 3.删除文件：$filename为string，$array为false。返回boolean。
 *
 * @param string $filename
 * @param array|null $array
 * @return array boolean
 */
function arrfile($filename, $array = null) {
	if (is_scalar($filename)) {
		if (is_null($array)) {
			// 1.获取本地返回数组式文件：$filename为string，$array为null。不存在或不是数组文件，返回空数组。
			is_file($filename) && $ret = include ($filename);
			return isset($ret) && is_array($ret) ? $ret : array();
		} elseif (is_array($array)) {
			// 2.生成返回数组式文件：$filename为string，$array为array。
			$data = "<?php\nreturn " . var_export($array, true) . ';';
			// 自动创建目录
			$dirname = dirname($filename);
			is_dir($dirname) or mkdir($dirname, 0777, true);
			file_put_contents($filename, $data);
			return $array;
		} elseif ($array === false) {
			// 3.删除文件：$filename为string，$array为false。
			return is_file($filename) ? unlink($filename) : false;
		}
	}
	return false;
}

/**
 * Cookie操作，设置、读取、删除
 * 1.设置：$value为非null且非false，用法：cookie('key', 'value')
 * 2.读取：$value为null，用法：cookie('key')
 * 3.删除：$value为false，用法：cookie('key', false)
 *
 * @param string $name 名字
 * @param mixed $value 值
 * @param number $expire 有效期
 * @param string|null $path 路径
 * @param string|null $domain 域名
 * @param boolean $secure 是否通过安全的HTTPS连接传输
 * @return mixed
 */
function cookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = false) {
	if (!empty($name)) {
		if (is_null($value)) {
			return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
		} elseif ($value === false) {
			return setcookie($name, '', time() - 1);
		} else {
			$expire = empty($expire) ? 0 : (time() + intval($expire));
			return setcookie($name, $value, $expire, $path, $domain, $secure);
		}
	}
	return false;
}

/**
 * 输出常见HTTP状态信息
 *
 * @param integer $code 状态码
 */
function send_http_status($code) {
	$status = array(
			200 => 'OK',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			503 => 'Service Unavailable' 
	);
	$str = isset($status[$code]) ? (' ' . $status[$code]) : '';
	header("HTTP/1.1 $code$str");
}

/**
 * 获取随机字符串
 * 词典类型说明：
 * 0：数字+小写字母+大写字母-形近易混淆的字符
 * 1：数字
 * 2：小写字母
 * 3：大写字母
 * 4：小写字母+大写字母
 * 5：数字+小写字母+大写字母
 *
 * @param integer $length 字符串长度
 * @param integer $dict 词典类型，默认为5
 * @return string
 */
function get_rand_string($length, $dict = 5) {
	$dicts = array(
			'2345678abcdefghjkmnprstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ',
			'0123456789',
			'abcdefghijklmnopqrstuvwxyz',
			'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' 
	);
	$dict = isset($dicts[$dict]) ? $dicts[$dict] : $dicts[0];
	return substr(str_shuffle($dict), 0, $length);
}

/**
 * 数字的任意进制转换
 *
 * @param integer|string $number
 * @param integer $to 目标进制数
 * @param integer $from 源进制数
 * @return string
 */
function radix($number, $to = 62, $from = 10) {
	// 先转换成10进制
	$number = dec_from($number, $from);
	// 再转换成目标进制
	$number = dec_to($number, $to);
	return $number;
}

/**
 * 十进制数转换成其它进制
 * 可以转换成2-62任何进制
 *
 * @param integer $num
 * @param integer $to
 * @return string
 */
function dec_to($num, $to = 62) {
	if ($to == 10 || $to > 62 || $to < 2) {
		return $num;
	}
	$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$ret = '';
	do {
		$ret = $dict[bcmod($num, $to)] . $ret;
		$num = bcdiv($num, $to);
	} while ($num > 0);
	return $ret;
}

/**
 * 其它进制数转换成十进制数
 * 适用2-62的任何进制
 *
 * @param string $num
 * @param integer $from
 * @return number
 */
function dec_from($num, $from = 62) {
	if ($from == 10 || $from > 62 || $from < 2) {
		return $num;
	}
	$num = strval($num);
	$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen($num);
	$dec = 0;
	for($i = 0; $i < $len; $i++) {
		$pos = strpos($dict, $num[$i]);
		if ($pos >= $from) {
			continue;
		}
		$dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
	}
	return $dec;
}

/**
 * 十进制数转换成62进制
 *
 * @param integer $num
 * @return string
 */
function to62($num) {
	$to = 62;
	$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$ret = '';
	do {
		$ret = $dict[bcmod($num, $to)] . $ret;
		$num = bcdiv($num, $to);
	} while ($num > 0);
	return $ret;
}

/**
 * 62进制数转换成十进制数
 *
 * @param string $num
 * @return string
 */
function from62($num) {
	$from = 62;
	$num = strval($num);
	$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen($num);
	$dec = 0;
	for($i = 0; $i < $len; $i++) {
		$pos = strpos($dict, $num[$i]);
		$dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
	}
	return $dec;
}

/**
 * 根据IP获取地址信息
 * 使用淘宝IP地址库
 * 成功返回如下数组（此IP是ping baidu.com的）：
 * array(
 * 'country' => '中国',
 * 'country_id' => 'CN',
 * 'area' => '华北',
 * 'area_id' => '100000',
 * 'region' => '北京市',
 * 'region_id' => '110000',
 * 'city' => '北京市',
 * 'city_id' => '110000',
 * 'county' => '',
 * 'county_id' => '-1',
 * 'isp' => '电信',
 * 'isp_id' => '100017',
 * 'ip' => '220.181.111.85'
 * );
 *
 * @param string $ip
 * @return array boolean 成功返回数组，失败返回FALSE。
 */
function get_address($ip) {
	$taobao_ip = "http://ip.taobao.com/service/getIpInfo.php?ip=";
	$data = json_decode(file_get_contents($taobao_ip . $ip));
	if ($data->code == 0) {
		$data = $data->data;
		return (array)$data;
	} else {
		return false;
	}
}

/**
 * 获取PHP错误类型
 * 
 * @param integer $code
 * @return string
 */
function get_error_type($code) {
	$types = array(
			1 => 'E_ERROR',
			2 => 'E_WARNING',
			4 => 'E_PARSE',
			8 => 'E_NOTICE',
			16 => 'E_CORE_ERROR',
			32 => 'E_CORE_WARNING',
			64 => 'E_COMPILE_ERROR',
			128 => 'E_COMPILE_WARNING',
			256 => 'E_USER_ERROR',
			512 => 'E_USER_WARNING',
			1024 => 'E_USER_NOTICE',
			2048 => 'E_STRICT',
			4096 => 'E_RECOVERABLE_ERROR',
			8192 => 'E_DEPRECATED',
			16384 => 'E_USER_DEPRECATED' 
	);
	return isset($types[$code]) ? $types[$code] : $code;
}

/**
 * 获取自动转换单位的文件大小值（四舍五入）
 *
 * @param float $byte
 * @param integer $precision 精度
 * @return string
 */
function get_auto_size($byte, $precision = 2) {
	$kb = 1024;
	$mb = $kb * 1024;
	$gb = $mb * 1024;
	$tb = $gb * 1024;
	if ($byte < $kb) {
		return $byte . ' B';
	} elseif ($byte < $mb) {
		return round($byte / $kb, $precision) . ' KB';
	} elseif ($byte < $gb) {
		return round($byte / $mb, $precision) . ' MB';
	} elseif ($byte < $tb) {
		return round($byte / $gb, $precision) . ' GB';
	} else {
		return round($byte / $tb, $precision) . ' TB';
	}
}