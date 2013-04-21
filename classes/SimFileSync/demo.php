<?php
require 'SimFileSync.class.php';

// 新建实例
$sync = new SimFileSync();

$src = "F:/www/simphp";
$dest = "F:/www/simphp_sae";

// 设置排除文件夹和文件名
$sync->set('exclude_dir_array', array(
		'.svn',
		'.settings' 
))->set('exclude_file_array', array(
		'.project',
		'.buildpath' 
));

// 同步
$sync->sync($src, $dest);

// 返回同步列表
print_r($sync->getSync());


