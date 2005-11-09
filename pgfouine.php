#! /usr/bin/php -q
<?php

require_once('include/lib/common.lib.php');
require_once('include/base.lib.php');
require_once('include/listeners/listeners.lib.php');
require_once('include/postgresql/postgresql.lib.php');

$logReader = new GenericLogReader('tests/logs/test_pqa.log', 'SyslogPostgreSQLParser', 'PostgreSQLAccumulator');
$logReader->addListener('PrintQueryListener');
$logReader->parse();

?>