<?php
require 'SimFileSync.class.php';

$sync = new SimFileSync();
$src = "F:/www/simphp";
$dest = "F:/www/simphp_sae";

$sync->set('exclude_dir_array', array(
		'.svn',
		'.settings' 
))->set('exclude_file_array', array(
		'.project',
		'.buildpath' 
));

$sync->sync($src, $dest);

print_r($sync->getSync());
print_r($sync->getFile($src));
print_r($sync->getFile($dest));

