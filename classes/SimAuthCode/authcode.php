<?php
require 'SimAuthCode.class.php';
$code = new SimAuthCode();
$code->set('disturb',10)->set('type',3)->set('length',4);
$code->output('authcode');
