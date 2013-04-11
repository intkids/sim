<?php
/**
 * Sim, Simple library simplify our PHP development.
 * 使用简单、简洁的类库，简化我们的PHP开发。
 *
 * @author 雨中歌者 http://weibo.com/esinger (新浪微博)
 * @link http://blog.csdn.net/esinger (技术博客)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 2013/04/10
 */

/**
 * 验证码类
 * 1.自定义长度、大小
 * 2.背景颜色随机，文本颜色随机
 *
 * @author 雨中歌者
 * @since 2013-4-11
 */
class SimAuthCode {
	/**
	 * 初始配置值
	 *
	 * @var array
	 */
	private $ini = array(
			'length' => 4,
			'type' => 0,
			'width' => 80,
			'height' => 24,
			'fontsize' => 14,
			'fontfile' => 'arial.ttf',
			'disturb' => 10 
	);
	
	/**
	 * 字体最大倾斜角度
	 *
	 * @var integer
	 */
	const ANGLE_MAX = 30;
	
	/**
	 * 字体与边的间隔
	 *
	 * @var integer
	 */
	const PADDING = 5;
	
	/**
	 * 图像资源句柄
	 *
	 * @var object
	 */
	private $im;
	
	/**
	 * 验证码文本
	 *
	 * @var string
	 */
	private $text;
	
	/**
	 * 构造函数
	 */
	public function __construct() {
	}
	
	/**
	 * 设置参数
	 * 1.$name为string，参数键名，$value为参数值，如 set('name','value')
	 * 2.$name为array，参数键值对数组，如 set(array('name'=>'value'))
	 *
	 * @access public
	 * @param string|array $name 参数键名或键值对数组
	 * @param mixed|null $value 参数值
	 * @return SimAuthCode
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
	 * 输出验证码图像
	 *
	 * @access public
	 */
	public function output($session_name = null) {
		$this->createText()->imageBackground()->imageText()->imageDisturb();
		if (!is_null($session_name)){
			isset($_SESSION) or session_start();
			$_SESSION[$session_name] = $this->getCode();
		}
		header("Content-type:image/png");
		imagepng($this->im);
		imagedestroy($this->im);
	}
	
	/**
	 * 获取验证码
	 *
	 * @access public
	 * @return string
	 */
	public function getCode() {
		return $this->text;
	}
	
	/**
	 * 取得验证码字符
	 *
	 * @access private
	 * @return SimAuthCode
	 */
	private function createText() {
		$dicts = array(
				'2345678abcdefghjkmnprstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ',
				'0123456789',
				'abcdefghijklmnopqrstuvwxyz',
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' 
		);
		$dict = isset($dicts[$this->ini['type']]) ? $dicts[$this->ini['type']] : $dicts[0];
		$text = substr(str_shuffle($dict), 0, $this->ini['length']);
		$this->text = $text;
		return $this;
	}
	
	/**
	 * 生成背景图像
	 *
	 * @access private
	 * @return SimAuthCode
	 */
	private function imageBackground() {
		$this->im = imagecreatetruecolor($this->ini['width'], $this->ini['height']);
		$bgcolor = imagecolorallocate($this->im, rand(150, 255), rand(150, 255), rand(150, 255));
		$bordercolor = imagecolorallocate($this->im, 0, 0, 0);
		imagefill($this->im, 0, 0, $bgcolor);
		imagerectangle($this->im, 0, 0, $this->ini['width'] - 1, $this->ini['height'] - 1, $bordercolor);
		return $this;
	}
	
	/**
	 * 生成验证文本
	 *
	 * @access private
	 * @return SimAuthCode
	 */
	private function imageText() {
		$count = strlen($this->text);
		$text = str_split($this->text);
		$width = ($this->ini['width'] - 2 * self::PADDING) / $count;
		$height = $this->ini['height'];
		foreach ($text as $k => $char) {
			$angle = rand(-self::ANGLE_MAX, self::ANGLE_MAX);
			$fontsize = $this->ini['fontsize'];
			$fontfile = $this->ini['fontfile'];
			$box = $this->getBox($fontsize, $angle, $fontfile, $char);
			$x = $k * $width + ($width - $box['w']) / 2 + self::PADDING;
			$y = $height - ($height - $box['h']) / 2;
			$color = imagecolorallocate($this->im, rand(0, 140), rand(0, 140), rand(0, 140));
			imagettftext($this->im, $fontsize, $angle, $x, $height / 1.4, $color, $fontfile, $char);
		}
		return $this;
	}
	
	/**
	 * 生成干扰元素
	 *
	 * @access private
	 * @return SimAuthCode
	 */
	private function imageDisturb() {
		$num = $this->ini['disturb'];
		$width = $this->ini['width'];
		$height = $this->ini['height'];
		$fontsize = $this->ini['fontsize'];
		for ($i = 0; $i < $num; $i++) {
			$color = imagecolorallocate($this->im, rand(0, 140), rand(0, 140), rand(0, 140));
			imageline($this->im, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $color);
			imagechar($this->im, $fontsize, rand(0, $width), rand(0, $height), '*', $color);
		}
		for ($i = 0; $i < $num * 10; $i++) {
			$color = imagecolorallocate($this->im, rand(0, 140), rand(0, 140), rand(0, 140));
			imagesetpixel($this->im, rand(0, $width), rand(0, $height), $color);
		}
		return $this;
	}
	
	/**
	 * 获取字符长和宽
	 *
	 * @access private
	 * @param integer $fontsize
	 * @param integer $angle
	 * @param string $fontfile
	 * @param string $text
	 * @return array
	 */
	private function getBox($fontsize, $angle, $fontfile, $text) {
		$box = imageftbbox($fontsize, $angle, $fontfile, $text);
		return array(
				'w' => $box[2] - $box[0],
				'h' => $box[1] - $box[7] 
		);
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		unset($this->ini);
	}
}
 
 
 // End of file SimAuthCode.class.php