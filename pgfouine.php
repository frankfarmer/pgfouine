#! /usr/bin/php -qC
<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005 Guillaume Smet
 *
 * pgFouine is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * pgFouine is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with pgFouine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

define('VERSION', '0.4');

ini_set('max_execution_time', 18000);
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
	echo "\n";
	echo 'Usage: '.$GLOBALS['executable'].' -file <file> [-top <n>] [-format <format>] [-logtype <logtype>] [-reports <report1,report2>]
  -file <file>                  log file to analyze
  -outputfile <file>            output file (necessary for html-with-graphs format)
  -top <n>                      number of queries in lists. Default is 20.
  -format <format>              output format: html, html-with-graphs or text. Default is html.
  -logtype <logtype>            log type: only syslog is currently supported
  -reports <report1,report2>    list of reports type separated by a comma
                                reports can be: overall, hourly, bytype, slowest, n-mosttime,
                                 n-mostfrequent, n-slowestaverage, n-mostfrequenterrors
  -examples <n>                 maximum number of examples for a normalized query
  -onlyselect                   ignore all queries but SELECT
  -from "date"                  ignore lines logged before this date (uses strtotime)
  -to "date"                    ignore lines logged after this date (uses strtotime)
  -debug                        debug mode
  -profile                      profile mode
  -help                         this help
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
if(isset($options['profile'])) {
	define('PROFILE', 1);
} else {
	define('PROFILE', 0);
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


if(isset($options['outputfile'])) {
	$tmpOutputFilePath = $options['outputfile'];
	$tmpOutputDirectory = dirname($tmpOutputFilePath);
	$tmpOutputFileName = basename($tmpOutputFilePath);

	if(file_exists($tmpOutputFilePath) && (!is_file($tmpOutputFilePath) || !is_writable($tmpOutputFilePath))) {
		usage($tmpOutputFilePath.' already exists and is not a file or is not writable');
	} elseif(!is_dir($tmpOutputDirectory) || !is_writable($tmpOutputDirectory)) {
		usage($tmpOutputDirectory.'is not a directory, does not exist or is not writable');
	} elseif(!$tmpOutputFileName) {
		usage('cannot find a valid basename in '.$tmpOutputFilePath);
	} else {
		$outputFilePath = realpath($tmpOutputDirectory).'/'.$tmpOutputFileName;
	}
} else {
	$outputFilePath = false;
}
setConfig('output_file_path', $outputFilePath);

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

$supportedFormats = array('text' => 'TextReportAggregator', 'html' => 'HtmlReportAggregator', 'html-with-graphs' => 'HtmlWithGraphsReportAggregator');
if(isset($options['format'])) {
	if(array_key_exists($options['format'], $supportedFormats)) {
		if($options['format'] == 'html-with-graphs' && !$outputFilePath) {
			usage('you need to define an output file with -outputfile to use HTML with graphs format');
		}
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
	'hourly' => 'HourlyStatsReport',
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

if(isset($options['examples'])) {
	$maxExamples = (int) $options['examples'];
} else {
	$maxExamples = 3;
}
setConfig('max_number_of_examples', $maxExamples);

if(isset($options['onlyselect'])) {
	setConfig('only_select', true);
} else {
	setConfig('only_select', false);
}

if(isset($options['from']) && !empty($options['from'])) {
	setConfig('from_timestamp', strtotime($options['from']));
} else {
	setConfig('from_timestamp', MIN_TIMESTAMP);
}

if(isset($options['to']) && !empty($options['to'])) {
	$toTimestamp = strtotime($options['to']);
	if($toTimestamp <= 0) {
		$toTimestamp = MAX_TIMESTAMP;
	}
} else {
	$toTimestamp = MAX_TIMESTAMP;
}
setConfig('to_timestamp', $toTimestamp);

$logReader = new GenericLogReader($filePath, $parser, 'PostgreSQLAccumulator');

$reportAggregator = new $aggregator($logReader);

foreach($reports AS $report) {
	$reportAggregator->addReport($supportedReports[$report]);
}

if($outputFilePath) {
	$outputFilePointer = @fopen($outputFilePath, 'w');
	if($outputFilePointer) {
		fwrite($outputFilePointer, $reportAggregator->getOutput());
		fclose($outputFilePointer);
	} else {
		stderr('cannot open file '.$outputFilePath.' for writing');
	}
} else {
	echo $reportAggregator->getOutput();
}

fclose($stderr);

?>
