#! /usr/bin/php -qC
<?php

define('VERSION', '0.1');
define('DEBUG', 1);

ini_set('max_execution_time', 7200);
error_reporting(E_ALL);

require_once('include/lib/common.lib.php');
require_once('include/base.lib.php');
require_once('include/listeners/listeners.lib.php');
require_once('include/postgresql/postgresql.lib.php');
require_once('include/reporting/reports.lib.php');

$stderr = fopen('php://stderr', 'w');

function usage($error) {
	// TODO : add help
	die($error."\n");
}

$executable = array_shift($argv);

$arguments = array();
$argvCount = count($argv);
for($i = 0; $i < $argvCount; $i++) {
	if(strpos($argv[$i], '-') === 0) {
		$optionKey = substr($argv[$i], 1);
		$value = false;
		if(($i+1 < $argvCount) && (strpos($argv[$i+1], '-') !== 0)) {
			$value = $argv[$i+1];
			$i++;
		}
		$options[$optionKey] = $value;
	} else {
		usage('invalid options format');
	}
}

if(!isset($options['file'])) {
	usage('the -file option is required');
} elseif(!$options['file']) {
	usage('you have to specify a file path');
} elseif(!is_readable($options['file'])) {
	usage('file '.$options['file'].' cannot be read');
} else {
	$filePath = realpath($options['file']);
}

if(isset($options['top'])) {
	if((int) $options['top'] > 0) {
		$top = (int) $options['top'];
	} else {
		usage('top option should be a valid integer');
	}
} else {
	$top = 20;
}
define('DEFAULT_TOP_QUERIES_NUMBER', $top);

$supportedFormats = array('text' => 'TextReportAggregator', 'html' => 'HtmlReportAggregator');
if(isset($options['format'])) {
	if(array_key_exists($options['format'], $supportedFormats)) {
		$aggregator = $supportedFormats[$options['format']];
	} else {
		usage('format not supported');
	}
} else {
	$aggregator = $supportedFormats['html'];
}

$supportedLogTypes = array('syslog' => 'SyslogPostgreSQLParser');
if(isset($options['logtype'])) {
	if(array_key_exists($options['logtype'], $supportedLogTypes)) {
		$parser = $supportedLogTypes[$options['logtype']];
	} else {
		usage('log type not supported');
	}
} else {
	$parser = $supportedLogTypes['syslog'];
}

$logReader = new GenericLogReader($filePath, $parser, 'PostgreSQLAccumulator');

$reportAggregator = new $aggregator($logReader);

$reportAggregator->addReport('OverallStatsReport');
$reportAggregator->addReport('QueriesByTypeReport');
$reportAggregator->addReport('SlowestQueriesReport');
$reportAggregator->addReport('NormalizedQueriesMostTimeReport');
$reportAggregator->addReport('NormalizedQueriesMostFrequentReport');
$reportAggregator->addReport('NormalizedQueriesSlowestAverageReport');

echo $reportAggregator->getOutput();

fclose($stderr);

?>