<?php

require 'global.php';
require 'classes/Database.php';
require 'classes/Builder.php';

$tng_conn = dodbTNG();
$tng_db = new Database($tng_conn);
$bldr = new Builder();

?>
