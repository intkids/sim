<?php
error_reporting(E_ALL);
header('Content-type:text/html;charset=utf-8');
require 'functions/common.func.php';

$token = token_get_all(file_get_contents('classes/SimUpload/SimUpload.class.php'));

var_dump($token);