#! /usr/bin/php -qC
<?php

define('VERSION', '0.1');

ini_set('max_execution_time', 7200);
error_reporting(E_ALL);

require_once('include/lib/common.lib.php');
require_once('include/base.lib.php');
require_once('include/listeners/listeners.lib.php');
require_once('include/postgresql/postgresql.lib.php');
require_once('include/reporting/reports.lib.php');

$stderr = fopen('php://stderr', 'w');

function usage($error = false) {
	if($error) {
		stderr('Error: '.$error);
	}
	echo 'Usage: '.$GLOBALS['executable'].' -file <file> [-top <n>] [-format <format>] [-logtype <logtype>] [-reports report1,report2]
  -file <file>          		log file to analyze
  -top <n>              		number of queries in lists. Default is 20.
  -format <format>      		output format: html or text. Default is html.
  -logtype <logtype>    		log type: only syslog is currently supported
  -reports <report1,report2>	list of reports type separated by a comma
  -debug                		debug mode
  -help                 		this help
';
	if($error) {
		exit(1);
	} else {
		exit(0);
	}
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

if(isset($options['help']) || isset($options['h']) || isset($options['-help'])) {
	usage();
}

if(isset($options['debug'])) {
	define('DEBUG', 1);
} else {
	define('DEBUG', 0);
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
setConfig('default_top_queries_number', $top);

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

$supportedReports = array(
	'overall' => 'OverallStatsReport',
	'bytype' => 'QueriesByTypeReport',
	'slowest' => 'SlowestQueriesReport',
	'n-mosttime' => 'NormalizedQueriesMostTimeReport',
	'n-mostfrequent' => 'NormalizedQueriesMostFrequentReport',
	'n-slowestaverage' => 'NormalizedQueriesSlowestAverageReport',
	'n-mostfrequenterrors' => 'NormalizedErrorsMostFrequentReport',
);
$defaultReports = array('overall', 'bytype', 'slowest', 'n-mosttime', 'n-mostfrequent', 'n-slowestaverage');

if(isset($options['reports'])) {
	$selectedReports = explode(',', $options['reports']);
	
	$notSupportedReports = array_diff($selectedReports, array_keys($supportedReports));
	if(empty($notSupportedReports)) {
		$reports = $selectedReports;
	} else {
		usage('report types not supported: '.implode(',', $notSupportedReports));
	}
} else {
	$reports = $defaultReports;
}

$logReader = new GenericLogReader($filePath, $parser, 'PostgreSQLAccumulator');

$reportAggregator = new $aggregator($logReader);

foreach($reports AS $report) {
	$reportAggregator->addReport($supportedReports[$report]);
}

echo $reportAggregator->getOutput();

fclose($stderr);

?>