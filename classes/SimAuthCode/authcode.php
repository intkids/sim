<?php
require 'SimAuthCode.class.php';
$code = new SimAuthCode();
$code->set($_GET);
$code->output('authcode');
