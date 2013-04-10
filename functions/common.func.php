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
			make_dir(dirname($filename));
			return file_put_contents($filename, $data);
		}
	}
	return false;
}

/**
 * 数组文件操作（读、写、删）
 * 1.$data为NULL：读取文件，返回array
 * 2.$data为FALSE：删除文件，返回boolean
 * 3.$data为array：写入文件，返回array
 *
 * @param string|integer $filename
 * @param mixed|false|null $data
 * @return array boolean
 */
function arrfile($filename, $data = null) {
	if (is_scalar($filename)) {
		if (is_null($data)) {
			is_file($filename) && $data = @file_get_contents($filename);
			empty($data) or $data = unserialize($data);
			return empty($data) ? array() : $data;
		} elseif (false === $data) {
			return is_file($filename) ? unlink($filename) : false;
		} else {
			make_dir(dirname($filename));
			return file_put_contents($filename, serialize($data));
		}
	}
	return false;
}



