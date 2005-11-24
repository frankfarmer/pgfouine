<?php

$postgreSQLRegexps = array();

// PostgreSQLParser
$postgreSQLRegexps['LogLine'] = new RegExp("/^(?:.*?)(LOG|DEBUG|CONTEXT|WARNING|ERROR|FATAL|PANIC|HINT|DETAIL|STATEMENT):[\s]+/");
$postgreSQLRegexps['ContinuationLine'] = new RegExp("/^(\^I|\s)/");
$postgreSQLRegexps['QueryStartPart'] = new RegExp("/^(query|statement):[\s]+/");
$postgreSQLRegexps['StatusPart'] = new RegExp("/^(connection|received|unexpected EOF)/");
$postgreSQLRegexps['DurationPart'] = new RegExp("/^duration:([\s\d\.]+)(sec|ms)/");

// SyslogPostgreSQLParser
$postgreSQLRegexps['CommandLine'] = new RegExp('/\[(\d{1,10})(\-\d{1,5}){0,1}\] /');

// PostgreSQLStatusLine
$postgreSQLRegexps['ConnectionReceived'] = new RegExp('/connection received: host=([^\s]+) port=([\d]+)/');
$postgreSQLRegexps['ConnectionAuthorized'] = new RegExp('/connection authorized: user=([^\s]+) database=([^\s]+)/');

// PostgreSQLQueryStartWithDurationLine
$postgreSQLRegexps['QueryOrStatementPart'] = new RegExp('/[\s]*(query|statement):[\s]*/i');

// PostgreSQLContextLine
$postgreSQLRegexps['ContextSqlStatement'] = new RegExp('/^SQL statement "/');
$postgreSQLRegexps['ContextSqlFunction'] = new RegExp('/([^\s]+)[\s]+function[\s]+"([^"]+)"(.*)$/');

$GLOBALS['postgreSQLRegexps'] =& $postgreSQLRegexps;

?>