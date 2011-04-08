#! /usr/bin/php -qC
<?php
require "include/PgfouineVacuum.class.php";
$cmd = new PgfouineVacuum();
$cmd->run();
