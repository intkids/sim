<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SimAuthCode_验证码类_demo</title>
<script type="text/javascript"
	src="http://lib.sinaapp.com/js/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript">
		$(function(){
			$("#frmconfig input").change(function(){
					$("#img").attr('src','authcode.php?'+$("#frmconfig").serialize());
				});
		});
	</script>
</head>
<body>
	<h1>验证码类：SimAuthCode.class.php</h1>
	<fieldset>
		<legend>参数配置</legend>
		<form id="frmconfig">
			<p>
				<img src="authcode.php" id="img">
			</p>
			<dl>
				<dt>修改下面的参数预览效果</dt>
				<dd>
					验证码长度:<input type="text" value="4" name="length">
				</dd>
				<dd>
					验证码字典:<input type="text" value="0" name="type">
				</dd>
				<dd>
					字体大小:<input type="text" value="14" name="fontsize">
				</dd>
				<dd>
					图像宽度:<input type="text" value="80" name="width">
				</dd>
				<dd>
					图像高度:<input type="text" value="24" name="height">
				</dd>
				<dd>
					干扰元素密度:<input type="text" value="10" name="disturb">
				
				
				<dd />
			</dl>
		</form>
	</fieldset>
	<fieldset>
		<legend> 用户登录实例 </legend>
		<form action="login.php" method="post">
			<dl>
				<dt>用户登录</dt>
				<dd>
					验证码：<input type="text" value="" name="authcode"> <label> <img
						title="点击换一个" src="authcode.php" style="cursor: pointer"
						onclick="javascript:this.src='authcode.php?t='+new Date().getTime();">
					</label> <input type="submit" value="登录">
				</dd>
			</dl>
		</form>
	</fieldset>
	<fieldset>
		<legend>页面源代码</legend>
		<p><?php highlight_file(__FILE__) ?></p>
	</fieldset>
</body>
</html>