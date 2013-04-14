<?php
require 'SimPixelCaptcha.class.php';

$sim = new SimPixelCaptcha();
$sim->set('type',1);
$sim->output();