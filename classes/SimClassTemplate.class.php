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
 * 类模板文件
 *
 * @author 雨中歌者
 */
class SimClassTemplate {
	/**
	 * 初始配置值
	 *
	 * @var array
	 */
	private $ini = array();
	
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
	 * @return SimClassTemplate
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
	 * 获取配置值，不存在返回默认值$default
	 *
	 * @access private
	 * @param string $name 配置键名
	 * @param mixed $default 默认值
	 * @return mixed
	 */
	private function get($name, $default = null) {
		return isset($this->ini[$name]) ? $this->ini[$name] : $default;
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		unset($this->ini);
	}
}
 
 
 // End of file SimClassTemplate.class.php