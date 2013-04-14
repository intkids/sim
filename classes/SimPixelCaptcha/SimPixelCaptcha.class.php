<?php
/**
 * Sim, Simple library simplify our PHP development.
 * 使用简单、简洁的类库，简化我们的PHP开发。
 *
 * @author 雨中歌者 http://weibo.com/esinger (新浪微博)
 * @link http://blog.csdn.net/esinger (技术博客)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * 像素验证码
 * 原理：把字符分解成像素点，并稀释，再辅以相同颜色的干扰像素点
 * 特点：安全性高，机器难识别，有效防止验证码识别程序
 * 功能：
 * 1.自定义字符长度
 * 2.自定义字典类型
 * 3.自定义放大倍数
 * 4.自定义像素密度
 * 5.自定义噪点密度
 * 6.随机背景与前景色
 *
 * @author 雨中歌者
 * @version 1.0
 */
class SimPixelCaptcha {
	/**
	 * 初始配置值
	 * length:验证码字符长度
	 * type:验证码字典类型
	 * zoom:放大倍数
	 * density:像素点密度
	 * noise:噪点密度
	 *
	 * @var array
	 */
	private $ini = array(
			'length' => 4,
			'type' => 3,
			'zoom' => 5,
			'density' => 6,
			'noise' => 4 
	);
	
	/**
	 * 验证码字典
	 *
	 * @var array
	 */
	private $dicts = array(
			0 => '2345678abcdefghjkmnprstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ',
			1 => '0123456789',
			2 => 'abcdefghijklmnopqrstuvwxyz',
			3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			4 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			5 => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' 
	);
	
	/**
	 * 验证码文本
	 *
	 * @var string
	 */
	private $text;
	
	/**
	 * 字体
	 *
	 * @var number
	 */
	private $font = 5;
	
	/**
	 * 字符位置索引信息
	 *
	 * @var array
	 */
	private $index = array();
	
	/**
	 * 图像资源
	 *
	 * @var resource
	 */
	private $im = null;
	
	/**
	 * 图像宽度
	 *
	 * @var number
	 */
	private $width = 0;
	
	/**
	 * 图像高度
	 *
	 * @var number
	 */
	private $height = 0;
	
	/**
	 * 前景色
	 *
	 * @var resource
	 */
	private $color;
	
	/**
	 * 构造函数
	 */
	public function __construct() {
	}
	
	/**
	 * 设置参数
	 * length:验证码字符长度，默认4
	 * type:验证码字典类型，默认3，大写字母
	 * zoom:放大倍数，默认为5
	 * density:像素点密度，默认为6
	 * noise:噪点密度，默认为4
	 *
	 * @access public
	 * @param string|array $name 参数键名或键值对数组
	 * @param mixed|null $value 参数值
	 * @return SimPixelCaptcha
	 */
	public function set($name, $value = null) {
		if (is_array($name)) {
			$this->ini = array_merge($this->ini, $name);
		} elseif (is_string($name)) {
			$this->ini[$name] = $value;
		}
		return $this;
	}
	
	/**
	 * 输出验证码
	 *
	 * @access public
	 * @param string $session_name SESSION名
	 */
	public function output($session_name = null) {
		$this->createText()->ParseCode()->imageBackground()->imageText()->imageNoise();
		header("Content-type:image/png");
		imagepng($this->im);
		imagedestroy($this->im);
		if (!is_null($session_name)) {
			isset($_SESSION) or session_start();
			$_SESSION[$session_name] = $this->text;
		}
	}
	
	/**
	 * 获取验证码文本
	 *
	 * @access public
	 * @return string
	 */
	public function getCode() {
		return $this->text;
	}
	
	/**
	 * 生成验证码字符
	 *
	 * @access private
	 * @return SimPixelCaptcha
	 */
	private function createText() {
		$dict = isset($this->dicts[$this->ini['type']]) ? $this->dicts[$this->ini['type']] : $this->dicts[0];
		$text = substr(str_shuffle($dict), 0, $this->ini['length']);
		$this->text = $text;
		return $this;
	}
	
	/**
	 * 解析验证码字符像素位置信息
	 *
	 * @access private
	 * @return SimPixelCaptcha
	 */
	private function parseCode() {
		$len = strlen($this->text);
		$this->width = imagefontwidth($this->font) * $len;
		$this->height = imagefontheight($this->font);
		$im = imagecreatetruecolor($this->width, $this->height);
		$color = imagecolorallocate($im, 255, 255, 255);
		imagestring($im, $this->font, 0, 0, $this->text, $color);
		for($x = 0; $x < $this->width; $x++) {
			for($y = 0; $y < $this->height; $y++) {
				$arr = imagecolorsforindex($im, imagecolorat($im, $x, $y));
				$arr['red'] == 255 && $this->index[] = ($x + 1) . ',' . $y;
			}
		}
		imagedestroy($im);
		return $this;
	}
	
	/**
	 * 绘出背景
	 *
	 * @access private
	 * @return SimPixelCaptcha
	 */
	private function imageBackground() {
		$zoom = $this->ini['zoom'];
		$this->width *= $zoom;
		$this->height *= $zoom;
		$this->im = imagecreatetruecolor($this->width, $this->height);
		$bgcolor = imagecolorallocate($this->im, rand(0, 100), rand(0, 100), rand(0, 100));
		imagefilledrectangle($this->im, 0, 0, $this->width, $this->height, $bgcolor);
		return $this;
	}
	
	/**
	 * 绘制字符
	 *
	 * @access private
	 * @return SimPixelCaptcha
	 */
	private function imageText() {
		$zoom = $this->ini['zoom'];
		$density = $this->ini['density'];
		$this->color = imagecolorallocate($this->im, rand(150, 255), rand(150, 255), rand(150, 255));
		foreach ($this->index as $val) {
			list($x, $y) = explode(',', $val);
			$xx = $x * $zoom;
			$yy = $y * $zoom;
			$of = intval($zoom / 2);
			for($i = 0; $i < $density; $i++) {
				imagesetpixel($this->im, $xx + rand(-$of, $of), $yy + rand(-$of, $of), $this->color);
			}
		}
		return $this;
	}
	
	/**
	 * 绘制噪点
	 *
	 * @access private
	 * @return SimPixelCaptcha
	 */
	private function imageNoise() {
		$noise = $this->ini['noise'];
		$count = intval($this->width * $this->height * $noise / 100);
		for($i = 0; $i < $count; $i++) {
			imagesetpixel($this->im, rand(1, $this->width), rand(1, $this->height), $this->color);
		}
		return $this;
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		unset($this->ini);
	}
}
 
 
 // End of file SimPixelCaptcha.class.php