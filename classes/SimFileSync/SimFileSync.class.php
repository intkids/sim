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
 * 文件同步类
 *
 * @author 雨中歌者
 * @version 1.0
 */
class SimFileSync {
	/**
	 * 初始配置值
	 *
	 * @var array
	 */
	private $ini = array(
			'exclude_dir_pattern' => '',
			'exclude_file_pattern' => '',
			'exclude_dir_array' => array(),
			'exclude_file_array' => array() 
	);
	
	/**
	 * 源目录名
	 *
	 * @var string
	 */
	private $src;
	
	/**
	 * 目标目录名
	 *
	 * @var string
	 */
	private $dest;
	
	/**
	 * 源目录数据
	 *
	 * @var array
	 */
	private $src_data = array();
	
	/**
	 * 文件同步情况
	 *
	 * @var array
	 */
	private $sync = array();
	
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
	 * @return SimFileSync
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
	 * 同步
	 *
	 * @access public
	 * @param string $src 源文件目录
	 * @param string $dest 目标文件目录
	 * @return array
	 */
	public function sync($src, $dest) {
		$this->src = rtrim($src, '/\\') . '/';
		$this->dest = rtrim($dest, '/\\') . '/';
		$this->src_data = $this->getFile($src);
		foreach ($this->src_data as $file => $type) {
			$dest = str_replace($this->src, $this->dest, $file);
			if ($type == 'dir' && !is_dir($dest)) {
				// 目录不存在，创建目录
				mkdir($dest, 0777, true);
				$this->sync[$file] = 'mkdir';
			} elseif ($type == 'file') {
				if (!is_file($dest)) {
					// 目标文件不存在，复制文件
					$dir = dirname($dest);
					is_dir($dir) or mkdir($dir, 0777, true);
					copy($file, $dest);
					$this->sync[$file] = 'newfile';
				} else {
					if (md5_file($file) != md5_file($dest)) {
						// 目标文件存在，但修改时间不一样，覆盖文件
						copy($file, $dest);
						$this->sync[$file] = 'rewrite';
					}
				}
			}
		}
	}
	
	/**
	 * 返回同步的文件列表
	 *
	 * @access public
	 * @return array
	 */
	public function getSync() {
		return $this->sync;
	}
	
	/**
	 * 获取目录下的所有目录和文件
	 *
	 * @access public
	 * @param string $dirname
	 * @return array 不是目录或目录打开失败返回空数组
	 */
	public function getFile($dirname) {
		$dirname = rtrim($dirname, '/\\');
		$ret = array();
		if (is_dir($dirname)) {
			if (($dh = @opendir($dirname)) !== false) {
				while (false !== ($file = readdir($dh))) {
					if ($file != "." && $file != "..") {
						$path = $dirname . '/' . $file;
						if (is_dir($path)) {
							if (!$this->isExcluded($path, 'dir')) {
								$ret[$path] = 'dir';
								$ret = array_merge($ret, $this->getFile($path));
							}
						} else {
							if (!$this->isExcluded($path, 'file')) {
								$ret[$path] = 'file';
							}
						}
					}
				}
				closedir($dh);
			}
		}
		return $ret;
	}
	
	/**
	 * 是否被排除文件
	 *
	 * @access private
	 * @param string $filename 文件名
	 * @param boolean $type 目录或者文件（dir|file）
	 * @return boolean
	 */
	private function isExcluded($filename, $type) {
		$filename = basename($filename);
		$pattern = $this->ini["exclude_{$type}_pattern"];
		$array = $this->ini["exclude_{$type}_array"];
		if ((!empty($pattern) && preg_match($pattern, $filename)) || in_array($filename, $array)) {
			return true;
		}
		return false;
	}
	
	/**
	 * * 析构函数
	 */
	public function __destruct() {
		unset($this->ini);
	}
}
 
 
 // End of file SimFileSync.class.php