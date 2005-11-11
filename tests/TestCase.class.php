<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

define('DEBUG', 0);

$commonTests = &new GroupTest('Common tests');

$commonTests->addTestFile('TestRegExp.class.php');
$commonTests->addTestFile('TestGenericLogReader.class.php');
$commonTests->addTestFile('TestQuery.class.php');
$commonTests->addTestFile('TestErrorQuery.class.php');
$commonTests->addTestFile('TestLogStream.class.php');
$commonTests->addTestFile('TestSlowestQueryList.class.php');
$commonTests->run(new TextReporter());


$postgresqlTests = &new GroupTest('PostgreSQL tests');
$postgresqlTests->addTestFile('TestSyslogPostgreSQLParser.class.php');
$postgresqlTests->run(new TextReporter());

?>