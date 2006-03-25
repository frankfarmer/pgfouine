<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

define('DEBUG', false);
define('CONFIG_ONLY_SELECT', false);
define('CONFIG_TIMESTAMP_FILTER', false);

$commonTests = &new GroupTest('Common tests');

$commonTests->addTestFile('TestRegExp.class.php');
$commonTests->addTestFile('TestProfiler.class.php');
$commonTests->addTestFile('TestGenericLogReader.class.php');
$commonTests->addTestFile('TestLogObject.class.php');
$commonTests->addTestFile('TestQueryLogObject.class.php');
$commonTests->addTestFile('TestErrorLogObject.class.php');
$commonTests->addTestFile('TestLogStream.class.php');
$commonTests->addTestFile('TestSlowestQueryList.class.php');
$commonTests->run(new TextReporter());


$postgresqlTests = &new GroupTest('PostgreSQL tests');
$postgresqlTests->addTestFile('TestSyslogPostgreSQLParser.class.php');
$postgresqlTests->run(new TextReporter());

?>