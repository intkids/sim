<?php
/**
 * 常用函数库
 * @author 雨中歌者 http://weibo.com/esinger (新浪微博)
 * @link http://blog.csdn.net/esinger (技术博客) http://www.simphp.com (个人网站)
 * @since 2013/04/10
 */

/**
 * 是否合法变量名
 * 规定合法变量名为：字母或下划线开头，后跟字母、数字、下划线、短杠。
 * 不能使用汉字等双字节字符。
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