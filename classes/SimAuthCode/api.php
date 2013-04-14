SimAuthCode是一个字符验证码类。
一、功能特点
简单易用，功能强大。
自定义验证码长度，既字符数量。
自定义验证码词典，共有6种类别可设置
自定义验证码图像宽度和长度
自定义验证码字体大小和字体文件
自定义干扰元素密度，密度越大，识别难度越大
随机背景颜色，随机文本颜色，随机文本角度
二、典型用法：
<?php
// 引用类文件
require 'SimAuthCode.class.php';
// 新建类实例
$code = new SimAuthCode();
// 输出验证码图像，同时设置SESSION。
$code->output('authcode');
?>
三、API说明：
SimAuthCode共提供三个公用API：
1. set($key, $value)
	设置参数。共有以下7种参数
	(1)length:验证码长度(字符数量)，默认为4
	(2)type:字典类型，默认为0
		共有6种字典：
		a. type = 0: 数字、小写字母、大写字母，去除0,1,i,l,o等形近的字符
		b. type = 1: 数字
		c. type = 2: 小写字母
		d. type = 3: 大写字母
		e. type = 4: 小写字母、大写字母
		f. type = 5: 数字、小写字母、大写字母
	(3)width:验证码图像宽度，默认为80px
	(4)height:验证码图像高度，默认24px
	(5)fontsize:验证码字体大小，默认14
	(6)fontfile:验证码字体文件，默认arial.ttf
	(7)disturb:干扰强度，默认为10，强度越大，字符越难辨认。
	用法示例：
<?php
// 设置验证码长度
$code->set('length', 6);
// 设置干扰强度
$code->set('disturb', 5);
// set()方法支持链式操作
$code->set('width', 200)->set('height', 40);
// 也可以使用键值对数组
$code->set(array(
		'fontsize' => 20,
		'fontfile' => 'msyh.ttf' 
));
?>
2. output($session_name)
	(1)输出验证码图像
	(2)如果指定参数$session_name，则同时设置SESSION的值
	用法示例：
<?php
// 输出验证码图像
$code->output();
?>
3. getCode()
	返回验证码文本，用来设置SESSION。
	用法示例：
<?php
// 设置SESSION。如果在output()中指定了参数$session_name，此步骤可省略。
session_start();
$_SESSION['authcode'] = $code->getCode();
?>
一个自定义参数的示例：
<?php
// 引用类文件
require 'SimAuthCode.class.php';
// 新建类实例
$code = new SimAuthCode();
// 设置参数，可选
$code->set('length', 6);
// 输出验证码图像
$code->output();
// 设置SESSION
session_start();
$_SESSION['authcode'] = $code->getCode();
?>

四、相关链接
GitHub地址：https://github.com/simphp/sim/tree/master/classes/SimAuthCode
示例DEMO地址：http://www.simphp.com/sim/SimAuthCode/demo.php

五、完整源码

