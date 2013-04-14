##SimAuthCode验证码类API说明
### SimAuthCode类共提供三个公用API
#### 一、 set($key,$value) 设置验证码参数
> #####共有7个参数
> 1. length:验证码长度，默认为4，用法：`set('length', 6)`  
> 2. type:验证码词典类型，共有0,1,2,3,4,5，默认为0，用法：`set('type', 3)`  
	>0:数字和大小写字母，去除0o1il等形近易混字符  
	>1:数字  
	>2:小写字母  
	>3:大写字母  
	>4:小写字母与大写字母  
	>5:数字、小写字母与大写字母  
> 3. width:验证码图像宽度，默认80px，用法：`set('width', 100)`  
> 4. height:验证码图像高度，默认24px，用法：`set('height', 30)`  
> 5. fontsize:字体大小，默认14，用法：`set('fontsize', 18)`  
> 6. fontfile:字体文件，默认arial.ttf，用法：`set('fontfile', 'msyh.ttf')`  
> 7. disturb:干扰强度，默认为10，用法：`set('disturb', 8)`

##### 二、 output($session_name=null) 输出验证码图像  
可选参数$session_name为设置验证码值的SESSION的键名，如果指定此参数，则自动设置SESSION值。
##### 三、 getCode()
返回验证码文本，用于设置SESSION。