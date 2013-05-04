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
 * 文件上传类
 * 主要功能和特点：
 * 1.支持同时上传多个文件
 * 2.自定义上传文件大小限制
 * 3.自定义上传文件类型限制
 * 4.支持多种文件命名方式，指定文件名、原文件名、MD5值、时间+随机数
 * 5.返回详细的上传结果信息，包括成功的文件信息和失败的文件信息
 * 6.可以保存为中文文件名
 *
 * @author 雨中歌者
 * @version 1.0
 */
class SimUpload {
	/**
	 * 初始配置值
	 * save_path:文件保存目录路径
	 * save_name:文件保存名称
	 * max_size:允许上传的最大值，单位字KB
	 * allow_ext:允许上传的文本扩展名类型，数组或字符串，字符串用'/[\s,\|\/]/'分开
	 * overwrite:原名保存时，是否允许同名文件覆盖
	 *
	 * @var array
	 */
	private $ini = array(
			'save_path' => 'upload_files',
			'save_name' => '',
			'max_size' => 0,
			'allow_ext' => array(),
			'overwrite' => true 
	);
	
	/**
	 * 上传的文件
	 *
	 * @var array
	 */
	private $files = array();
	
	/**
	 * 上传成功信息
	 *
	 * @var array
	 */
	private $succs = array();
	
	/**
	 * 上传失败信息
	 *
	 * @var array
	 */
	private $errors = array();
	
	/**
	 * 语言包
	 *
	 * @var array
	 */
	private $languages = array(
			0 => '文件上传成功',
			1 => '文件尺寸超过了php.ini中upload_max_filesize选项限定的值',
			2 => '文件尺寸超过了HTML表单中MAX_FILE_SIZE选项指定的值',
			3 => '文件只有部分被上传',
			4 => '没有文件被上传',
			6 => '找不到临时文件夹',
			7 => '文件写入失败',
			101 => '不允许上传的文件类型',
			102 => '不是HTTP POST上传的文件',
			103 => '文件尺寸超过系统限制或上传表单编码类型错误',
			104 => '文件尺寸超过了用户设定的值',
			105 => '移动文件错误',
			106 => '文件扩展名与文件类型不符',
			107 => '文件已经存在' 
	);
	
	/**
	 * 是否上传多个文件
	 *
	 * @var boolean
	 */
	private $multi_upload = false;
	
	/**
	 * 操作系统编码
	 *
	 * @var string
	 */
	const CHARSET_OS = 'GBK';
	
	/**
	 * 程序代码编码
	 *
	 * @var string
	 */
	const CHARSET_CODE = 'UTF-8';
	
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
	 * @return SimUpload
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
	 * 上传一个文件
	 *
	 * @access public
	 * @return boolean
	 */
	public function upload() {
		$this->init()->arrangeFiles();
		if (isset($this->files[0])) {
			return $this->handleFile($this->files[0]);
		}
		return false;
	}
	
	/**
	 * 上传多个文件
	 *
	 * @access public
	 */
	public function uploadMulti() {
		$this->multi_upload = true;
		$this->init()->arrangeFiles();
		foreach ($this->files as $key => $file) {
			$this->handleFile($file, $key);
		}
	}
	
	/**
	 * 返回上传成功文件的数量
	 *
	 * @access public
	 * @return number
	 */
	public function getCount() {
		return count($this->succs);
	}
	
	/**
	 * 返回上传成功文件的信息
	 * 返回结果有下面三种情况
	 * 1.如果是upload()，返回一维数组
	 * 2.如果是uploadMulti()，返回二维数组
	 * 3.如果没有上传成功文件，返回空数组
	 *
	 * @access public
	 * @return array
	 */
	public function getSucc() {
		if (empty($this->succs)) {
			return array();
		} else {
			return $this->multi_upload ? $this->succs : $this->succs[0];
		}
	}
	
	/**
	 * 返回上传失败文件的信息
	 * 返回结果有下面三种情况
	 * 1.如果是upload()，返回一维数组
	 * 2.如果是uploadMulti()，返回二维数组
	 * 3.如果没有上传失败文件，返回空数组
	 *
	 * @access public
	 * @return array
	 */
	public function getError() {
		if (empty($this->errors)) {
			return array();
		} else {
			return $this->multi_upload ? $this->errors : $this->errors[0];
		}
	}
	
	/**
	 * 返回上传结果信息
	 *
	 * @access public
	 * @return array
	 */
	public function getResult() {
		return array(
				'succ' => $this->getSucc(),
				'error' => $this->getError() 
		);
	}
	
	/**
	 * 初始化
	 *
	 * @access private
	 * @return SimUpload
	 */
	private function init() {
		// 处理允许上传后缀名
		if (is_string($this->ini['allow_ext']) && $this->ini['allow_ext'] !== '') {
			$this->ini['allow_ext'] = preg_split('/[\s,\|\/]/', $this->ini['allow_ext']);
		}
		// 如果保存目录不存在，自动创建
		is_dir($this->ini['save_path']) or mkdir($this->ini['save_path'], 0777, true);
		$this->ini['save_path'] = realpath($this->ini['save_path']) . DIRECTORY_SEPARATOR;
		return $this;
	}
	
	/**
	 * 整理上传文件信息
	 * 把$_FILES里的上传文件信息整理成规范的数组
	 *
	 * @access private
	 * @return SimUpload
	 */
	private function arrangeFiles() {
		if ($_FILES) {
			foreach ($_FILES as $key => $val) {
				if (is_string($val['name']) && $val['name'] != '') {
					$val['src'] = $key;
					$this->files[] = $val;
				} elseif (is_array($val['size'])) {
					foreach ($val['name'] as $k => $name) {
						if ($name != '') {
							$this->files[] = array(
									'name' => $val['name'][$k],
									'type' => $val['type'][$k],
									'tmp_name' => $val['tmp_name'][$k],
									'error' => $val['error'][$k],
									'size' => $val['size'][$k],
									'src' => $key 
							);
						}
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * 处理上传文件
	 *
	 * @access private
	 * @return SimUpload
	 */
	private function handleFile($file, $order_number = 0) {
		if ($file['error'] == 0) {
			$name = $file['name'];
			// 首先检测是否合法上传的文件
			if (!is_uploaded_file($file['tmp_name'])) {
				$this->setError($file, 102);
				return false;
			}
			// 检测文件后缀名
			$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
			if (!empty($this->ini['allow_ext']) && !in_array($ext, $this->ini['allow_ext'])) {
				$this->setError($file, 101);
				return false;
			}
			// 检测文件大小
			if ($this->ini['max_size'] > 0 && $file['size'] > $this->ini['max_size'] * 1024) {
				$this->setError($file, 104);
				return false;
			}
			// 获取保存文件名
			$save_name = $this->getSaveName($name, $ext, $order_number);
			// 转换编码，防止中文名乱码
			$filename = $this->ini['save_path'] . iconv(self::CHARSET_CODE, self::CHARSET_OS, $save_name);
			// 如果不允许覆盖，且已经存在同名文件，则上传错误。
			if (!$this->ini['overwrite'] && is_file($filename)) {
				$this->setError($file, 107);
				return false;
			}
			// 移动文件
			if (!@move_uploaded_file($file['tmp_name'], $filename)) {
				$this->setError($file, 105);
				return false;
			}
			// 移动成功，保存成功信息
			$this->setSucc($file, $this->ini['save_path'], $save_name);
			return true;
		} else {
			$this->setError($file, $file['error']);
			return false;
		}
	}
	
	/**
	 * 获取文件保存名称
	 *
	 * @access private
	 * @param string $name 原文件名
	 * @param string $ext 原文件扩展名
	 * @param integer $order_number 同时上传多文件时的序号
	 * @return string
	 */
	private function getSaveName($name, $ext, $order_number) {
		$save_name = $this->ini['save_name'];
		empty($ext) or $ext = '.' . $ext;
		if ($save_name == 1) {
			// save_name为1，使用原文件名保存
			return $name;
		} elseif ($save_name == 2) {
			// save_name为2，使用原文件名的MD5值保存
			return md5($name) . $ext;
		} elseif ($save_name == '') {
			// save_name为空，使用时间+四位随机数
			return date('HmdHis') . substr(microtime(), 2, 6) . rand(1000, 9999) . $ext;
		} else {
			$order_number = $order_number == 0 ? '' : "_$order_number";
			return $save_name . $order_number . $ext;
		}
		return $save_name;
	}
	
	/**
	 * 设置上传成功信息
	 *
	 * @access private
	 * @param array $file
	 * @param string $save_path
	 * @param string $save_name
	 * @return SimUpload
	 */
	private function setSucc($file, $save_path, $save_name) {
		$this->succs[] = array(
				'name' => $file['name'],
				'save_path' => $save_path,
				'save_name' => $save_name,
				'mime' => $file['type'],
				'size' => $file['size'],
				'src' => $file['src'] 
		);
		return $this;
	}
	
	/**
	 * 设置错误信息
	 *
	 * @access private
	 * @param string $file 文件信息
	 * @param integer $error 错误代码
	 * @return SimUpload
	 */
	private function setError($file, $error) {
		$this->errors[] = array(
				'name' => $file['name'],
				'error' => $this->languages[$error],
				'src' => $file['src'] 
		);
		return $this;
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		unset($this->ini);
	}
}
 
 
 // End of file SimUpload.class.php