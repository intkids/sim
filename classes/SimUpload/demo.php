<html>
<head>
<title>文件上传DEMO</title>
<meta charset="utf-8">
</head>
<body>
	<form name="upload_form" action="upload.php" method="post"
		enctype="multipart/form-data">
		<dl>
			<dd>
				<input type="file" name="file">
			</dd>
			<dd>
				<input type="file" name="file2">
			</dd>
			<dd>
				<input type="file" name="file3[]">
			</dd>
			<dd>
				<input type="file" name="file3[]">
			</dd>
			<dd>
				<input type="submit" value="upload">
			</dd>
		</dl>
	</form>
</body>
</html>
