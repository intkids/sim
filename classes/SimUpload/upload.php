<?php
error_reporting(E_ALL);
header("content-type:text/html;charset=utf-8");

require 'SimUpload.class.php';
$up = new SimUpload();
$up->uploadMulti();

var_dump($up->getResult());